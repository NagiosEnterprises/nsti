import sys
import os

basedir = os.path.abspath(os.path.dirname(__file__))
nstidir = os.path.join(basedir, 'nsti')

print nstidir

sys.path.append(nstidir)
from nsti import app as application

application.secret_key = 'mysecretkey'
