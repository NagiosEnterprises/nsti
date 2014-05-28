import sys
import os
basedir = os.path.abspath(os.path.dirname(__file__))
nstidir = os.path.join(basedir)

sys.path.append(nstidir)

from nsti import nsti 

nsti.app.run(host='0.0.0.0', port=8080, debug=True)
