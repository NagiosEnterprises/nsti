from nsti import app
import logging
import datetime
import storm.locals as SL
import filters
from flask import session

from storm.tracer import debug
import storm.store
import storm.expr


debug(False)  # The flag enables or disables statement logging


LIMIT = app.config.get('PERPAGE', 50)

#~ Setup our DB connect string for Storm
db_connect = '%s://%s:%s@%s:%s/%s' % (app.config.get('DB_TYPE'),
                                      app.config.get('DB_USER'),
                                      app.config.get('DB_PASS'),
                                      app.config.get('DB_HOST'),
                                      app.config.get('DB_PORT'),
                                      app.config.get('DB_NAME'))

#~ Establish the database connection
def database_monitor():
    try:
        DATABASE = SL.create_database(db_connect)
        DB = SL.Store(DATABASE)
    except DisconnectionError:
        DB = SL.Store(DATABASE).rollback()


class Snmptt(object):
    __storm_table__ = 'snmptt'
    id = SL.Int(primary=True)
    eventname = SL.Unicode()
    eventid = SL.Unicode()
    trapoid = SL.Unicode()
    enterprise = SL.Unicode()
    community = SL.Unicode()
    hostname = SL.Unicode()
    agentip = SL.Unicode()
    category = SL.Unicode()
    severity = SL.Unicode()
    uptime = SL.Unicode()
    traptime = SL.Unicode()
    formatline = SL.Unicode()
    trapread = SL.Int()
    timewritten = SL.DateTime()


class SnmpttArchive(object):
    __storm_table__ = 'snmptt_archive'
    id = SL.Int(primary=True)
    eventname = SL.Unicode()
    eventid = SL.Unicode()
    trapoid = SL.Unicode()
    enterprise = SL.Unicode()
    community = SL.Unicode()
    hostname = SL.Unicode()
    agentip = SL.Unicode()
    category = SL.Unicode()
    severity = SL.Unicode()
    uptime = SL.Unicode()
    traptime = SL.Unicode()
    formatline = SL.Unicode()
    trapread = SL.Int()
    timewritten = SL.DateTime()


class SnmpttUnknown(object):
    __storm_table__ = 'snmptt_unknown'
    id = SL.Int(primary=True)
    trapoid = SL.Unicode()
    enterprise = SL.Unicode()
    community = SL.Unicode()
    hostname = SL.Unicode()
    agentip = SL.Unicode()
    uptime = SL.Unicode()
    traptime = SL.Unicode()
    formatline = SL.Unicode()
    trapread = SL.Int()
    timewritten = SL.DateTime()


class FilterAtom(object):
    __storm_table__ = 'filter_atom'
    id = SL.Int(primary=True)
    column_name = SL.Unicode()
    comparison = SL.Unicode()
    val = SL.Unicode()
    filter_id = SL.Int()


class Filter(object):
    __storm_table__ = 'filter'
    id = SL.Int(primary=True)
    name = SL.Unicode()
    filter_atom = SL.ReferenceSet(id, FilterAtom.filter_id)

    def __init__(self, name):
        self.name = name


def encode_storm_result_set(storm_obj):
    if not isinstance(storm_obj, storm.store.ResultSet):
        raise TypeError(repr(storm_obj) + " is not JSON serializable")

    result = []

    try:
        info = [x for x in storm_obj[0].__class__.__dict__.keys() if not x.startswith('_')]
    except IndexError:
        return result

    for item in storm_obj:
        trap = {}
        for attr in info:
            if attr == 'timewritten':
                try:
                    trap[attr] = getattr(item, attr).strftime('%m-%d-%Y %H:%M:%S')
                except:
                    trap[attr] = getattr(item, attr)
            else:
                trap[attr] = getattr(item, attr)
        result.append(trap)
    return result


def parse_timewritten(timewritten):
    return datetime.datetime.strptime(timewritten, '%m-%d-%Y %H:%M:%S')


def parse_relative_timewritten(relative):
    try:
        coefficient, suffix = int(relative[:-1]), relative[-1].lower()
    except Exception:
        coefficient, suffix = 1, 'd'

    now = datetime.datetime.now()
    offset = {}
    if suffix == 's':
        offset['seconds'] = coefficient
    elif suffix == 'm':
        offset['minutes'] = coefficient
    elif suffix == 'h':
        offset['hours'] = coefficient
    elif suffix == 'd':
        offset['days'] = coefficient
    elif suffix == 'w':
        offset['weeks'] = coefficient
    elif suffix == 'M':
        offset['months'] = coefficient

    return now - datetime.timedelta(**offset)


def prepare_query_tuple(traptype, key, value):
    valid_comparisons = ['contains', 'in', 'gt', 'lt']
    if not getattr(traptype, key, None) is None:
        if key == 'timewritten':
            query = (key, parse_timewritten(value))
        else:
            query = (key, value)
    else:
        try:
            column, comparison = key.split('__')
            actual_column = column
            if column == 'relative_timewritten':
                actual_column = 'timewritten'
            getattr(traptype, actual_column)
            assert comparison in valid_comparisons
            if column == 'relative_timewritten':
                adjusted = parse_relative_timewritten(value)
                query = (actual_column + '__' + comparison, adjusted)
            elif column == 'timewritten':
                query = (key, parse_timewritten(value))
            else:
                query = (key, value)
        except Exception:
            query = None
    return query


def get_queryable_keys(traptype, arguments, filter_arguments):
    '''Gets the queryable columns from a dictionary. Looks to see if they are
    valid lookups in the database, and if they are, returns all valid search
    result.

    @param arguments - The raw arguments, usually the request variables.
    @param traptype - The type of the trap
    @returns - A list containing the valid queryable columns.
    '''
    queryable = []

    for key in arguments.keys():
        all_key_values = arguments.getlist(key)
        for value in all_key_values:
            query = prepare_query_tuple(traptype, key, value)
            if not query is None:
                queryable.append(query)

    for key, value in filter_arguments:
        query = prepare_query_tuple(traptype, key, value)
        if not query is None:
            queryable.append(query)

    return queryable


def get_combiner(arguments, force_combiner):
    c = arguments.get('combiner', 'AND')
    if c.upper().strip() == 'OR' or force_combiner == 'OR':
        return storm.expr.Or
    else:
        return storm.expr.And


def pick_non_columns(traptype, queryable):
    safe =[]
    for query in queryable:
        try:
            column_name = query[0].split('__')[0]
            getattr(traptype, column_name)
            safe.append(query)
        except Exception:
            logging.info('Could not add query %r to trap filter %r', query, traptype)
    return safe


def sql_where_query(traptype, arguments, parsed_filters=None, force_combiner=None):
    '''Gets the actual query function that will be passed to find
    given the arguments we are searching for.

    @param traptype - The object that will be queried
    @param arguments - Dictionary that holds the key values for searching

    '''
    if parsed_filters is None:
        parsed_filters = []

    logging.debug('Entering sql_where_query...')
    query = []
    queryable = get_queryable_keys(traptype, arguments, parsed_filters)
    combiner = get_combiner(arguments, force_combiner)
    safe_queryable = pick_non_columns(traptype, queryable)

    logging.debug('Filtering with: %r', safe_queryable)

    for key, value in safe_queryable:
        attribute = key

        if key.endswith('__contains'):
            new_key = key.replace('__contains', '')
            attribute = getattr(traptype, new_key)
            query.append(attribute.like(u'%%%s%%' % unicode(value)))
        elif key.endswith('__in'):
            new_key = key.replace('__in', '')
            attribute = getattr(traptype, new_key)
            query.append(attribute.is_in(value))
        elif key.endswith('__gt'):
            new_key = key.replace('__gt', '')
            attribute = getattr(traptype, new_key)
            query.append(attribute > value)
        elif key.endswith('__lt'):
            new_key = key.replace('__lt', '')
            attribute = getattr(traptype, new_key)
            query.append(attribute < value)
        #~ Otherwise we want to do an exact match
        else:
            attribute = getattr(traptype, key)
            if(key in ['id']):
                query.append(attribute == int(value))
            else:
                query.append(attribute == unicode(value))

    if not query:
        return combiner(True)
    else:
        return combiner(*query)
