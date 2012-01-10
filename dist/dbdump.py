#!/usr/bin/env python

import MySQLdb
import sys
import random

db = MySQLdb.connect("localhost" , user = "snmptt" , passwd = "snmpttpass" , db = "snmptt" )
c  = db.cursor()

loop = sys.argv[1]
ente = [ '2021' , '9996' , '2343' , '5675' , '6879' ]
suff = [ '.13.990.0.17' , '.2.993.1.17' , '.13.991.3.4' , '.45.33.5.6' ]
stat = [ 'normal' , 'warning' , 'critical' , 'ok' ]
even = [ 'Status Event' , 'Other Event' , 'Closure' ]
mess = [ 'Oh no the fire hydrant blew up' , 'Smoke alarm detected.' ,
         'Out of pizza, send backup.' , 'Chassis fan is dead.' ,
         'Another sample message' , 'Do not forget to eat.' ,
         'Doggon it, people like me.' , 'Coldstart detected.' ]
comm = 'private'
othe = '0:0:41:57.76'
date = 'Wed Dec 28 16:20:'
dato = ' 2011'
time = 24
name = 'demoTrap'

for j in xrange(int(loop)):
    agent = '192.168.5.' + str(random.randint(44,55))
    enter = '.1.3.6.1.4.1.' + random.choice(ente)
    troid = enter + random.choice(suff)
    sever = random.choice(stat)
    event = random.choice(even)
    messa = random.choice(mess)    
    time += 1
    dater = date + str(time) + dato
    c.execute("""INSERT INTO snmptt (eventname,eventid,trapoid,enterprise,community,hostname,agentip,category,severity,uptime,traptime,formatline) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)""",(name,troid,troid,enter,comm,agent,agent,event,sever,othe,dater,messa))

print 'done'
