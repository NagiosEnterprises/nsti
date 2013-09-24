from flask import render_template, Flask, redirect, url_for, session, request, abort, Response
try:
    import json
except ImportError:
    import simplejson as json
import os

#~ Initial setup of the Flask application
app = Flask(__name__)
app.config.from_pyfile(os.path.join(app.root_path, 'etc', 'nsti.cfg'))
app.secret_key = os.urandom(24)

app.jinja_env.globals['static'] = lambda filename: url_for('static', filename=filename)

#~ First the error handlers...
@app.errorhandler(400)
def bad_request(error):
    return render_template('bad_request.html', error=error), 400

#~ Now all the routes...
@app.route('/')
def landing():
    return redirect(url_for('traplist'))

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
    
    return render_template('traplist.html')

@app.route('/trapview/<trapid>')
def trapview(trapid):
    '''Renders a single trap page.
    '''
    table = request.args.get('traptype') or session.get('traptype') or 'Snmptt'
    c_tablename = table.capitalize()
    
    if table in ['Snmptt', 'SnmpttArchive', 'SnmpttUnknown']:
        session['tablename'] = table
    
    return render_template('trapview.html', trapid=trapid, table=table)
    

@app.route('/api/read/<tablename>')
def read(tablename):
    
    import database as db
    
    traptype = getattr(db, tablename)
    
    where_clause = db.sql_where_query(traptype, request.args)
    
    if where_clause:
        results = db.DB.find(traptype, where_clause)
    else:
        results = db.DB.find(traptype)
    
    result_dict = db.encode_storm_result_set(results)
    
    json_str = json.dumps(result_dict, default=db.encode_storm_result_set)
    return Response(response=json_str, status=200, mimetype='application/json')
    
@app.route('/api/delete/<tablename>')
def delete(tablename):
    
    import database as db
    
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

@app.route('/api/archive')
def archive():
    
    import database as db
    
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

if __name__ == '__main__':
    app.run('0.0.0.0', 8080, debug=True)
