from nsti import app
import database as db

from flask import render_template, session, request, abort, Response

try:
    import json
except ImportError:
    import simplejson as json

@app.route('/inspector')
def inspector():
	session['TRUNCATE'] = app.config.get('TRUNCATE')
	table = request.args.get('traptype') or session.get('traptype') or 'Snmptt'
	c_tablename = table.capitalize()
    
    ''' c_table showstoppng until defined in next def '''
    if c_tablename in ['Snmptt', 'SnmpttArchive', 'SnmpttUnknown']:
        session['tablename'] = c_tablename
    else:
        abort(400, 'Bad Request. Inspector could not find what you specified, if you can believe that. Got: %s, expected Snmptt, SnmpttArchive or SnmpttUnknown.' % table)
    
	return render_template('trapview/traplist.html')
