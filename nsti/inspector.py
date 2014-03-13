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
    
	return render_template('trapview/traplist.html')
