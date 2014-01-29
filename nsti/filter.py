from nsti import app

from flask import render_template, session, request, abort


@app.route('/filter')
def filter():
    return render_template('/filter/filter.html')