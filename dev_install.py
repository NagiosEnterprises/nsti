#!/usr/bin/env python
    
import os
import shutil
import sys

os.chdir(os.path.abspath(os.path.dirname(__file__)))

def preamble():
    basepath = os.path.abspath(os.path.dirname(__file__))
    
    config = os.path.join(basepath, 'nsti', 'etc', 'nsti.cfg')
    
    print """
Today we will be running the devel install for NSTI.
    
Here is the deal:
    
    - Wherever the NSTI folder is RIGHT now, is where
      this installer will configure it to work
    
    - Before continuing ensure that the config file at:
          
      %s
    
      Has the proper information. Meaning the user, database
      and password should already exist.
    
    - All information in this database will be destroyed.
    
Deal? """ % config
    
    y = raw_input('[y|n] ')

    if y.lower() != 'y':
        print 'Exiting on user request...'
        sys.exit(1)

def apply_sql():
    from nsti import app
    mysql_string = 'mysql -u%s -p"%s" %s < nsti/dist/nsti.sql' % ( app.config.get('DB_USER'), 
                                                            app.config.get('DB_PASS'), 
                                                            app.config.get('DB_NAME')) 
    retcode = os.system(mysql_string)
    if retcode != 0:
        print 'Whoa...bad error code! I tried to run:'
        print mysql_string
        sys.exit(1)

def apply_apache():
    basename = os.path.abspath(os.path.dirname(__file__))
    apache = open(os.path.join(basename, 'nsti', 'dist', 'apache.conf'), 'r')
    target = open('/etc/httpd/conf.d/flnsti.conf', 'w')
    
    for l in apache.readlines():
        if 'WSGI' in l:
            target.write('WSGIScriptAlias /nsti %s\n' % os.path.join(basename, 'wsgi.py'))
        elif '/usr/local/nsti' in l:
            target.write('<Directory %s>\n' % basename)
        else:
            target.write(l)
    os.system('service httpd restart')

if __name__ == '__main__':
    preamble()
    apply_sql()
    apply_apache()
    check_python_prereqs()


