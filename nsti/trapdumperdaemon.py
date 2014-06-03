#!/usr/bin/env python

import logging
import time

import os
import random

def dump_trap(loop):
    import MySQLdb
    import sys

    db = MySQLdb.connect("localhost" , user = "root" , passwd = "nsti" , db = "snmptt" )
    c  = db.cursor()
    ente = [ '2021' , '9996' , '2343' , '5675' , '6879' ]
    suff = [ '.13.990.0.17' , '.2.993.1.17' , '.13.991.3.4' , '.45.33.5.6' ]
    stat = [ 'normal' , 'warning' , 'critical' , 'ok' ]
    even = [ 'Status Event' , 'Other Event' , 'Closure' ]
    mess = [ 'Oh no the fire hydrant blew up' , 'Smoke alarm detected.' ,
             'Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long Very long',
             'Out of pizza, send backup.' , 'Chassis fan is dead.' ,
             'Another sample message' , 'Do not forget to eat.' ,
             'Doggon it, people like me.' , 'Coldstart detected.' ]
    comm = 'private'
    name = 'demoTrap'

    for j in xrange(int(loop)):
        possible = [    '192.168.5.2',
                        '192.168.5.54',
                        '192.168.5.233',
                        '192.168.5.41',
                        '192.168.5.3',
                        'localhost',
                        '192.168.5.1' ]
                        
        agent = random.choice(possible)
        enter = '.1.3.6.1.4.1.' + random.choice(ente)
        troid = enter + random.choice(suff)
        sever = random.choice(stat)
        event = random.choice(even)
        messa = random.choice(mess)    
        dater = time.strftime('%Y-%m-%d %H:%M:%S') 
        c.execute("""INSERT INTO snmptt (eventname,eventid,trapoid,enterprise,community,hostname,agentip,category,severity,uptime,traptime,formatline) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)""",(name,troid,troid,enter,comm,agent,agent,event,sever,dater,dater,messa))

while True:
    num = random.choice([2,3,4])
    dump_trap(num)
    time.sleep(random.choice(range(10)))

