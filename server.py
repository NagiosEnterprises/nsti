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
    
    

if __name__ == '__main__':
    app.run('0.0.0.0', 8080, debug=True)
