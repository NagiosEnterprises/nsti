from nsti import app

import database as db
from flask import render_template, session, request, abort, Response
import storm.locals as SL

try:
    import json
except ImportError:
    import simplejson as json

@app.route('/filterlist')
def filter():
    return render_template('/filter/filterlist.html')

@app.route('/api/filter/create')
def create_filter():
    json_result = {}
    
    name = request.args.get('name', '')
    existing_count = db.DB.find(db.Filter, db.Filter.name == name).count()
    #~ Check to see if the name already exists.
    if name == '':
        json_result['error'] = 'Must give a name.'
    elif existing_count != 0:
        json_result['error'] = 'Name already exists and the name must be unique.'
    else:
        #~ Add the filter.
        new_filter = db.Filter(name)
        result = db.DB.add(new_filter)
        try:
            for atom in request.args.getlist('atoms[]'):
                atom_info = json.loads(atom)
                new_atom = db.FilterAtom()
                new_atom.column_name = atom_info['column_name']
                new_atom.comparison = atom_info['comparison']
                new_atom.val = atom_info['val']
                new_filter.filter_atom.add(new_atom)
            db.DB.flush()
            json_result['success'] = 'Successfully added new filter to the database.'
        except Exception, e:
            json_result['error'] = str(e)
    
    json_str = json.dumps(json_result)
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/filter/delete')
def delete_filter():
    json_result = {}
    
    name = request.args.get('name')
    existing_count = db.DB.find(db.Filter, db.Filter.name == name).count()
    #~ Check to see if the name already exists.
    if existing_count == 0:
        json_result['error'] = 'Filter by that name does not exist.'
    else:
        try:
            target_filter = db.DB.find(db.Filter, db.Filter.name == name)
            target_atoms = db.DB.find(db.FilterAtom, db.FilterAtom.filter_id == target_filter[0].id)
            target_atoms.remove()
            target_filter.remove()
            db.DB.flush()
            json_result['success'] = 'Successfully removed filter from the database.'
        except Exception, e:
            json_result['error'] = str(e)
    
    json_str = json.dumps(json_result)
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/filter/read')
def read_filter():
    filters = {}
    json_result = {'filters': filters}
    where_clause = db.sql_where_query(db.Filter, request.args)

    if where_clause:
        db_filters = db.DB.find(db.Filter, where_clause)
    else:
        db_filters = db.DB.find(db.Filter)
        
    for f in db_filters:
        j = {'id': f.id}
        action = []
        for atom in db.DB.find(db.FilterAtom, db.FilterAtom.filter_id == f.id):
            action.append({'column_name': atom.column_name,
                           'comparison': atom.comparison,
                           'value': atom.val})
        j['actions'] = action
        filters[f.name] = j
    
    json_str = json.dumps(json_result)
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/filter/add-active-filter')
def add_active_filter():
    json_result = {}
    session['active_filters'] = []
    name = request.args.get('name', '')
    session['active_filters'].append(name)
    print session
    existing_count = db.DB.find(db.Filter, db.Filter.name == name).count()
    json_str = json.dumps(json_result)
    return Response(response=json_str, status=200, mimetype='application/json')


    if not name:
        json_result['error'] = 'No name was given.'
    elif existing_count == 0:
        json_result['error'] = 'No filter by that name exists.'
    elif existing_count > 1:
        json_result['error'] = 'Filter by that name returns more than one result.'
    else:
        try:
            print session
            print name in session['active_filters']
            if not 'active_filters' in session:
                session['active_filters'] = []
            if not name in session['active_filters']:
                session['active_filters'].append(name)
            print session['active_filters']
            json_result['active_filters'] = session['active_filters']
            json_result['success'] = 'Successfully added filter to list.'
        except Exception, e:
            json_result['error'] = 'Error adding filter to list: %s' % str(e)
            
    json_str = json.dumps(json_result)
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/filter/read-active-filter')
def read_active_filter():
    print session.get('active_filters')
    json_result = {'filters': session.get('active_filters', [])}
    json_str = json.dumps(json_result)
     
    return Response(response=json_str, status=200, mimetype='application/json')

@app.route('/api/filter/delete-active-filter')
def delete_active_filter():
    json_result = {}
    name = request.args.get('name', '')
    existing_count = db.DB.find(db.Filter, db.Filter.name == name).count()
    
    if not name:
        json_result['error'] = 'No name was given.'
    elif existing_count == 0:
        json_result['error'] = 'No filter by that name exists.'
    elif existing_count > 1:
        json_result['error'] = 'Filter by that name returns more than one result.'
    else:
        try:
            active_filters = session.get('active_filters', [])
            if name in active_filters:
                active_filters.remove(name)
            session['active_filters'] = active_filters
            json_result['active_filters'] = session['active_filters']
            json_result['success'] = 'Successfully deleted filter from active filter list.'
        except Exception, e:
            json_result['error'] = 'Error adding filter to list: %s' % str(e)
            
    json_str = json.dumps(json_result)
    return Response(response=json_str, status=200, mimetype='application/json')
