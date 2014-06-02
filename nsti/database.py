from nsti import app
import storm.locals as SL
import filters
from flask import session

from storm.tracer import debug
import storm.store

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
DATABASE = SL.create_database(db_connect)
DB = SL.Store(DATABASE)


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
                    trap[attr] = getattr(item, attr).strftime('%x %X')
                except:
                    trap[attr] = getattr(item, attr)
            else:
                trap[attr] = getattr(item, attr)
        result.append(trap)
    return result


def get_queryable_keys(traptype, arguments):
    '''Gets the queryable columns from a dictionary. Looks to see if they are
    valid lookups in the database, and if they are, returns all valid search
    result.

    @param arguments - The raw arguments, usually the request variables.
    @param traptype - The type of the trap
    @returns - A dictionary containing the valid queryable columns.
    '''
    queryable = {}
    valid_comparisons = ['contains', 'in']

    for key in arguments.keys():
        if not getattr(traptype, key, None) is None:
            queryable[key] = arguments[key]
        else:
            try:
                column, comparison = key.split('__')
                getattr(traptype, column)
                assert comparison in valid_comparisons
                queryable[key] = arguments[key]
            except:
                continue

    return queryable


def get_combiner(arguments):
    c = arguments.get('combiner', 'AND')
    if c.upper().strip() == 'OR':
        return False
    else:
        return True


def get_active_filters_as_queryable(all_filters, active_filters):
    additional_args = {}
    for filter_name in active_filters:
        try:
            f = all_filters[filter_name]
        except KeyError:
            continue
        for a in f['actions']:
            instruction = str(a['column_name'] + a['comparison'])
            value = a['value']
            additional_args[instruction] = value
    return additional_args


def sql_where_query(traptype, arguments, use_session_filters=False):
    '''Gets the actual query function that will be passed to find
    given the arguments we are searching for.

    @param traptype - The object that will be queried
    @param arguments - Dictionary that holds the key values for searching

    '''
    query = None
    queryable = get_queryable_keys(traptype, arguments)

    if use_session_filters:
        all_filters = filters.read_filter_raw()
        active_filters = session.get('active_filters', [])
        filter_queryable = get_active_filters_as_queryable(all_filters, active_filters)
        queryable.update(filter_queryable)
    acombine = get_combiner(arguments)

    for key in queryable:
        #~ If it ends with contain, we want to do a LIKE
        attribute = key
        if key.endswith('__contains'):
            new_key = key.replace('__contains', '')
            attribute = getattr(traptype, new_key)
            cond = attribute.like(u'%%%s%%' % unicode(queryable[key]))
        #~ If its they want an in
        elif key.endswith('__in'):
            new_key = key.replace('__in', '')
            attribute = getattr(traptype, new_key)
            cond = attribute.is_in(queryable[key])
        #~ Otherwise we want to do an exact match
        else:
            if(key in ['id']):
                cond = attribute == int(queryable[key])
            else:
                cond = attribute == unicode(queryable[key])
        #~ If a query has already been made, AND this on, otherwise just
        #~ make query and set our latest condition to be the query.
        if not query:
            query = cond
        else:
            if acombine:
                query = query & cond
            else:
                query = query | cond
    return query
