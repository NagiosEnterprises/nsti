from server import app
import storm.locals as SL


LIMIT = app.config.get('PERPAGE', 50)

#~ Setup our DB connect string for Storm
db_connect = '%s://%s:%s@%s:%s/%s' % (  app.config.get('DB_TYPE'),
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

class SnmpttArchive(object):
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

def sql_where_query(traptype, arguments):
    '''Gets the actual query function that will be passed to find
    given the arguments we are searching for.
    
    @param traptype - The object that will be queried
    @param arguments - Dictionary that holds the key values for searching'''
    query = None
    
    for key in arguments.keys():
        #~ If it ends with contain, we want to do a LIKE
        if key.endswith('__contains'):
            new_key = key.replace('__contains', '')
            attribute = getattr(traptype, new_key)
            cond = attribute.like(u'%%%s%%' % unicode(arguments[key]))
        #~ Otherwise we want to do an exact match
        else:
            attribute = getattr(traptype, key)
            cond = attribute == unicode(arguments[key])
        #~ If a query has already been made, AND this on, otherwise just
        #~ make query and set our latest condition to be the query.
        if not query:
            query = cond
        else:
            query = query & cond
    return query
