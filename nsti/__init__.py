# from flask import Flask, url_for, render_template, redirect
# import os
#
# app = Flask(__name__)
# app.config.from_pyfile(os.path.join(app.root_path, 'etc', 'nsti.py'))
# app.secret_key = 'mysecretkey'
# app.server_name = 'nsti.dev'
# app.jinja_env.globals['static'] = lambda filename: url_for('static', filename=filename)
#
# #~ First the error handlers...
# @app.errorhandler(400)
# def bad_request(error):
#     return render_template('system/bad_request.html', error=error), 400
#
# #~ Then our default landing page...
# @app.route('/')
# def landing():
#     return redirect(url_for('traplist'))
#
# #~ Now import all of our modules
# import trapview
# import filters
# import inspector
