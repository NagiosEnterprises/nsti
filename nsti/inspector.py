from nsti import app
import database as db

import datetime
import time
from flask import render_template, session, request, abort, Response

try:
    import json
except ImportError:
    import simplejson as json

@app.route('/inspector')
def inspector():
	'''Renders the page where all of the traps of the given type (specified
    by the SESSION).
    '''
	session['TRUNCATE'] = app.config.get('TRUNCATE')
	table = request.args.get('traptype') or session.get('traptype') or 'Snmptt'
    
	return render_template('inspector/inspector.html')

@app.route('/inspector/<trapid>')
def inspector_view(trapid):
    '''Renders a single trap page.
    '''
    table = request.args.get('traptype') or session.get('traptype') or 'Snmptt'
    c_tablename = table.capitalize()
    
    if table in ['Snmptt', 'SnmpttArchive', 'SnmpttUnknown']:
        session['tablename'] = table
    
    return render_template('inspector/inspector.html', trapid=trapid, table=table)

@app.route('/api/inspector/read/<trapid>')
def inspector_read(trapid):
    trap = getattr(db, trapid)
    
    where_clause = db.sql_where_query(trap, request.args)
    
    if where_clause:
        results = db.DB.find(trap, where_clause)
    else:
        results = db.DB.find(trap)
    
    result_dict = db.encode_storm_result_set(results)
    
    json_str = json.dumps(result_dict, default=db.encode_storm_result_set)
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/inspector/chart/read/<traptype>')
def inspector_chart(traptype):
    trapt = getattr(db, traptype)

    where_clause = db.sql_where_query(trapt, request.args)
    
    if where_clause:
        results = db.DB.find(trapt, where_clause)
    else:
        results = db.DB.find(trapt)

    result_dict = db.encode_storm_result_set(results)
    
    json_str = json.dumps(result_dict, default=db.encode_storm_result_set)
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/inspector/test')
def inspector_test():
    return render_template('inspector/test.html')

@app.route('/api/inspector/chart/read_debug/<traptype>')
def inspector_chart_debug(traptype):
    trapt = getattr(db, traptype)

    where_clause = db.sql_where_query(trapt, request.args)
    
    if where_clause:
        results = db.DB.find(trapt, where_clause)
    else:
        results = db.DB.find(trapt)
    
    start_date = request.args.get('start_date', 0)
    end_date = request.args.get('end_date', int(time.time()))

    try:
        start_date = datetime.datetime.fromtimestamp(int(start_date))
        end_date = datetime.datetime.fromtimestamp(int(end_date))
    except TypeError:
        start_date = datetime.datetime.fromtimestamp(0)
        end_date = datetime.datetime.now()

    results = inspector_results_aggregator(trapt, results, start_date, end_date) 
    json_str = json.dumps(results)
    return Response(response=json_str, status=200, mimetype='application/json')

def inspector_results_aggregator(traptype, results, start_date, end_date):
    from storm.locals import SQL
    return json_result


