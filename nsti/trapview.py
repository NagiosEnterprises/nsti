from nsti import app
import database as db

from flask import render_template, session, request, abort, Response

try:
    import json
except ImportError:
    import simplejson as json

@app.route('/traplist')
def traplist():
    '''Renders the page where all of the traps of the given type (specified
    by the SESSION).
    '''
    session['TRUNCATE'] = app.config.get('TRUNCATE')
    table = request.args.get('traptype') or session.get('traptype') or 'Snmptt'
    c_tablename = table.capitalize()
    
    if c_tablename in ['Snmptt', 'SnmpttArchive', 'SnmpttUnknown']:
        session['tablename'] = c_tablename
    else:
        abort(400, 'Bad Request. Table type submitted was bad. Got: %s, expected Snmptt, SnmpttArchive or SnmpttUnknown.' % table)

    return render_template('trapview/traplist.html')

@app.route('/trapview/<trapid>')
def trapview(trapid):
    '''Renders a single trap page.
    '''
    table = request.args.get('traptype') or session.get('traptype') or 'Snmptt'
    c_tablename = table.capitalize()
    
    if table in ['Snmptt', 'SnmpttArchive', 'SnmpttUnknown']:
        session['tablename'] = table
    
    return render_template('trapview/trapview.html', trapid=trapid, table=table)

@app.route('/api/trapview/read/<tablename>')
def read(tablename):
    traptype = getattr(db, tablename)

    bool_combine = request.args.get('combiner', 'AND')
    if bool_combine == 'OR':
        acombine = False
    else:
        acombine = True
    
    where_clause = db.sql_where_query(traptype, request.args, acombine)
    
    if where_clause:
        results = db.DB.find(traptype, where_clause)
    else:
        results = db.DB.find(traptype)
    
    result_dict = db.encode_storm_result_set(results)
    
    json_str = json.dumps(result_dict, default=db.encode_storm_result_set)
    return Response(response=json_str, status=200, mimetype='application/json')
    
@app.route('/api/trapview/delete/<tablename>')
def delete(tablename):
    traptype = getattr(db, tablename)
    
    query = None
    id_list = [int(x) for x in request.args.getlist('id')]
    
    where_clause = db.sql_where_query(traptype, {'id__in': id_list})
    
    try:
        result = db.DB.find(traptype, where_clause)
        count = result.count()
        result.remove()
        json_str = {'success': 'Deleted %d traps.' % count}
    except Exception, e:
        json_str = {'error': 'Could not delete traps: %s' % str(e)}
    
    json_str = json.dumps(json_str)
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/trapview/archive')
def archive():
    traptype = db.Snmptt
    
    id_list = [int(x) for x in request.args.getlist('id')]
    
    where_clause = db.sql_where_query(traptype, {'id__in': id_list})
    
    try:
        result = db.DB.find(traptype, where_clause)
        count = result.count()
        for r in result:
            x = db.SnmpttArchive()
            x.eventname = r.eventname
            x.eventid = r.eventid
            x.trapoid = r.trapoid
            x.enterprise = r.enterprise
            x.community = r.community
            x.hostname = r.hostname
            x.agentid = r.agentip
            x.category = r.category
            x.severity = r.severity
            x.uptime = r.uptime
            x.traptime = r.traptime
            x.formatline = r.formatline
            x.trapread = r.trapread
            x.timewritten = r.timewritten
            db.DB.add(x)
        json_str = {'success': 'Successfully archived %d traps.' % count}
        result.remove()
    except Exception, e:
        json_str = {'error': 'Error occurred while archiving trap: %s' % str(e)}
    
    json_str = json.dumps(json_str)
    return Response(response=json_str, status=200, mimetype='application/json')
