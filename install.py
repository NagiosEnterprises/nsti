#!/usr/bin/env python
#-*- coding:utf-8 -*-

import os,sys,time

try:
    import MySQLdb
except:
    os.system('yum install MySQL-python -y')
    try:
        import MySQLdb
    except:
        print 'Unable to import MySQLdb is required for install. Please install MySQLdb for python manually. If the distro name is different edit this script to reflect that.'
        sys.exit(1)

#~ Determines whether we give short pause for messages
sleep   = False

unknown = True
xi      = True
core    = True

mysql_host  = 'localhost'
mysql_port  = 3306
mysql_root  = 'root'
mysql_pass  = 'nagiosxi'
mysql_client= 'localhost'

snmptt_user = 'snmptt'
snmptt_table= 'snmptt'
snmptt_pass = 'snmpttpass'

corexi      = 'xi'

snmptt_base = '/etc/snmp/'
httpd_path  = '/etc/httpd/conf.d/'
#~ snmptt_base = '/tmp/'

def create_db( user , password , table , client ):
    """ Function doc

    @param user: User to create mysql table with
    @param password: Password to assign user
    @param table: Table to give access to
    """
    #~ Connect to database
    try:
        db      = MySQLdb.connect( host = mysql_host , user = mysql_root , passwd = mysql_pass , port = mysql_port )
    except :
        #~ If cant connect, print error message and bail
        print 'ERROR: Could not connect to database.\n'
        sys.exit(1)
    else:
        print 'Sucessfully connected to database.'
        
    if sleep: time.sleep(2)
    
    pipe    = db.cursor()
    #~ Create user in DB
    try:
        pipe.execute('create user ' + snmptt_user)
    except :
        #~ User cannot be created
        print 'ERROR creating user. User may already be created. Continuing with given information.\n'
    else:
        print 'User created sucessfully.'
        
    if sleep: time.sleep(2)
    
    try:
        pipe.execute('create database ' + snmptt_table )
    except :
        #~ Table cannot be created
        print 'Error creating database. Database may already be created. \n'
    else:
        'Database created successfully.'
        
    if sleep: time.sleep(2)
    
    
    permstring = "grant all on "   + table \
                                + ".* to '" \
                                + user \
                                + "'@'" \
                                + client \
                                + "' identified by '" \
                                + password \
                                + "'"
    #~ Add permissions to mysql for snmptt user
    try:
        pipe.execute( permstring )
    except  :
        print 'Error adding permissions to user.\n'
        print 'This error indicates that the install has failed. Please make sure you have entered'
        print 'the proper root credentials and rerun the script.'
        sys.exit(1)
    else:
        print 'Successfully added permissions to database.'
    
    if sleep: time.sleep(2)
    
    db.close()
    
    try:
        db      = MySQLdb.connect( host = mysql_host , user = user , passwd = password , port = mysql_port , db = table)
    except :
        print 'Could not connect to database with new credentials.\n'
        print 'This error indicates that the install has failed. Please contact the developer.'
        print 'Or if you are using a complex MySQL setup make sure the crendentials entered are valid.'
        sys.exit(1)
    else:
        print 'Successfully tested connection with new authentication.'
    
    if sleep: time.sleep(2)
    
    db.close()

def get_user():
    
    global mysql_host, mysql_pass, mysql_client, mysql_port
    global snmptt_pass, snmptt_table, snmptt_user
    global corexi
    
    print "Ok, lets get some of the MySQL information."
    print "To use the default just hit enter."
    print "What server is your MySQL located on?"
    temp = raw_input("Default: [" + mysql_host + "]: ")
    if temp:
        mysql_host = temp
    print "What port is your MySQL listening on?"
    temp = raw_input("Default: [" + str(mysql_port) + "]: ")
    if temp:
        try:
            temp = int(temp)
        except:
            print "Entry MUST be an integer."
        else:
            mysql_port = temp
    print "What is the root password for your MySQL?"
    temp = raw_input("Default: [" + mysql_pass + "]: ")
    if temp:
        mysql_pass = temp
    print "What is the desired username for the snmptt user?"
    temp = raw_input("Default: [" + snmptt_user + "]: ")
    if temp:
        snmptt_user = temp
    print "What is the desired password for the snmptt user?"
    temp = raw_input("Default: [" + snmptt_pass + "]: ")
    if temp:
        snmptt_pass = temp
    print "What is the desired table for the snmptt application?"
    temp = raw_input("Default: [" + snmptt_table + "]: ")
    if temp:
        snmptt_table = temp
    print "What is the desired host for the origin of the snmptt user?"
    temp = raw_input("Default: [" + mysql_client + "]: ")
    if temp:
        mysql_client = temp
    print "What is the primary Nagios appplication [core|xi]?"
    temp = raw_input("Default: [" + corexi + "]: ")
    if not (temp == 'xi' or temp == 'core'):
        print 'Input was out of not xi or core. Using default.'
    elif temp == 'core':
        corexi = 'core'
    
def print_info():
    print "\n --- MySQL Info ---"
    print "MySQL host: \t\t",mysql_host
    print "MySQL root password: \t",mysql_pass
    print "MySQL port: \t\t",mysql_port
    print "\n --- SNMPTT User Info ---"
    print "SNMPTT user: \t\t",snmptt_user
    print "SNMPTT password: \t",snmptt_pass
    print "SNMPTT client location:",mysql_client
    print "SNMPTT table: \t\t",snmptt_table
    print "\n --- Install Type ---"
    print corexi
    print "\n"

def dump_sql():
    dampstring =    'mysql -u ' + snmptt_user + ' -p' + snmptt_pass + ' ' + snmptt_table \
                    + ' < dist/snmptt-1.2.sql'
    val = os.system( dampstring )
    if val:
        print 'Error importing sql schema. Does dist/snmptt-1.2.sql exist?'
        print 'Stopping install, make sure dist/ contains both sql schemas.'
        sys.exit(1)
    if unknown:
        dampstring =    'mysql -u ' + snmptt_user + ' -p' + snmptt_pass + ' ' + snmptt_table \
                        + ' < dist/snmptt_unknown.sql'
        val = os.system( dampstring )
        if val:
            print 'Error import sql schema. Does dist/snmptt_unknown.sql exist?'
            print 'Stopping install, make sure dist/ contains both sql schemas.'
            sys.exit(1)

def snmptt_check():
    """ See if snmptt is installed"""
    snmptt_readable = os.access( snmptt_base + 'snmptt.ini' , os.R_OK )
    snmptt_writable = os.access( snmptt_base + 'snmptt.ini' , os.W_OK )
    if not ( snmptt_writable and snmptt_readable ):
        print snmptt_base + '''snmptt.ini does not appear to be readable and writable by current user.
        If you have not installed snmptt yet, please install that first as this program
        is a frontend for it. You can find an installer script at:
        http://assets.nagios.com/downloads/nagiosxi/docs/Integrating_SNMP_Traps_With_XI.pdf
        If you are positive you have alread installed snmptt, check to see if the snmptt.ini
        at /etc/snmp/, if you have it installed at a different location change this script's snmptt_base
        variable.'''
        return 1
    print 'snmptt install verified.'
    if sleep: time.sleep(2)
    return 0

def edit_snmptt():
    """ Function doc

    @param PARAM: DESCRIPTION
    @return RETURN: DESCRIPTION
    """
    import re, fileinput
    
    user    = re.compile(r'^mysql_dbi_user')
    passwd  = re.compile(r'^mysql_dbi_password')
    enable  = re.compile(r'^mysql_dbi_enable')
    host    = re.compile(r'^mysql_dbi_host')
    port    = re.compile(r'^mysql_dbi_port')
    db      = re.compile(r'^mysql_dbi_database')
    
    try:
        inplacefile = fileinput.input( snmptt_base + 'snmptt.ini' , inplace = 1)
    except :
        print 'Unable to open file.\n'
        print 'Cannot continue with install.'
        sys.exit(1)
    else:
        for line in inplacefile:
            if user.search(line):
                print 'mysql_dbi_username = ' + snmptt_user
            elif passwd.search(line):
                print 'mysql_dbi_password = ' + snmptt_pass
            elif enable.search(line):
                print 'mysql_dbi_enable = 1'
            elif host.search(line):
                print 'mysql_dbi_host = ' + mysql_host
            elif port.search(line):
                print 'mysql_dbi_port = ' + str(mysql_port)
            elif db.search(line):
                print 'mysql_dbi_database = ' + snmptt_table
            else:
                print line,
        print 'Sucessfully edited snmptt.ini.'
    if sleep: time.sleep(2)
            
def apache_config():
    try:
        os.system('cp dist/nsti.conf ' + httpd_path + ' -f')
    except :
        print 'Could not move apache configuration from dist/nsti.conf to ' + httpd_path + '\n'
        print 'If you are using a non-RHEL based system, edit the script variable httpd_path to reflect your own.'
        sys.exit(1)
    else:
        print 'Sucessfully moved apache configuration.'
        if sleep: time.sleep(2)

def edit_nsti_conf():
    
    import re,fileinput
    
    auth    = re.compile(r'^useAuthentification')
    passwd  = re.compile(r'^password')
    host    = re.compile(r'^host')
    db      = re.compile(r'^name')
    user    = re.compile(r'^user')
    
    try:
        inplacefile = fileinput.input( 'etc/config.ini' , inplace = 1)
    except :
        print 'Unable to open file.\n'
        print 'Cannot continue with install.'
        sys.exit(1)
    else:
        for line in inplacefile:
            if user.search(line):
                print 'user = ' + snmptt_user
            elif passwd.search(line):
                print 'password = ' + snmptt_pass
            elif auth.search(line):
                if corexi == 'core':
                    print 'useAuthentification = 1'
                else:
                    print 'useAuthentification = 0'
            elif host.search(line):
                print 'host = ' + mysql_host
            elif db.search(line):
                print 'name = ' + snmptt_table
            else:
                print line,
        print 'Sucessfully edited etc/config.ini.'
    if sleep: time.sleep(2)

def do_index():
    movestring = 'cp dist/index.php.' + corexi + ' index.php'
    try:
        os.system( movestring )
    except :
        print 'Unable to copy index.php files from dist/.\n'
        print 'Cannot continue with install.'
        sys.exit(1)
    else:
        print 'Sucessfully edited index.php'
    if sleep: time.sleep(2)

def do_move():
    copystring = 'cp . /usr/local/nsti/ -rf'
    try:
        os.system( copystring )
    except :
        print 'Unable to copy nsti folder to /usr/local.\n'
        print 'Cannot continue with install.'
        sys.exit(1)
    else:
        print 'Sucessfully copied install directory.'
    if sleep: time.sleep(2)

def print_final():
    print 'Success.'
    print 'Nagios SNMP Trap Interface has successfully been installed.'
    print 'You can now safely remove this directory:'
    print os.getcwd()
    print 'You need to restart apache.'
    print 'You can access Nagios SNMP Trap Interface at http://<your server>/nsti/'

def main():
    
    if snmptt_check():
        sys.exit(1)
    loop = 'n'
    #~ Get user input
    while not loop is 'y':
        get_user()
        raw_input("There will be sensitive information displayed. Please ready your surroundings, then press enter.")
        print_info()
        loop = raw_input("\nContinue? [ enter 'y' to continue, any other to redo ]: ")
    #~ Create DB user
    create_db( snmptt_user , snmptt_pass , snmptt_table , mysql_client )
    #~ Dump the SQL schema into the database
    dump_sql()
    #~ Edit snmptt.ini file to do logging to SQL
    edit_snmptt()
    #~ Add nsti.conf to the apache config directory
    apache_config()
    #~ Edit our own ini to allow access to database
    edit_nsti_conf()
    #~ Change index.php based on install base
    do_index()
    #~ Move everything to /usr/local/nsti
    do_move()
    #~ Print final word
    print_final()
    
main()
