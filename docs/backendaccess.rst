NSTI Backend
============

The new ORM (Object-Relational Mapping) is now more efficient and the database is queryable without using the front-end at all.  You can use the URL landings to return JSON formatted data in your web browser.



Accessing data from the Backend
--------------------------------

To access NSTI normally you qould go to <ip address>/nsti


To access the API from a URL use keywords for getting the JSON formatted data

<ip address>/api/trapview/read/Snmptt

- This example will return every trap that is located in the database


Here is what a single SNMP trap looks like in JSON format:

.. code-block:: javascript

    {
        "eventid": ".1.3.6.1.4.1.2021.13.991.3.4",
        "category": "Closure",
        "uptime": "2014-04-11 14:27:50",
        "severity": "normal",
        "traptime": "2014-04-11 14:27:50",
        "timewritten": "04\/11\/14 14:27:50",
        "formatline": "Oh no the fire hydrant blew up",
        "hostname": "192.168.5.2",
        "community": "private",
        "agentip": "192.168.5.2",
        "eventname": "demoTrap",
        "trapoid": ".1.3.6.1.4.1.2021.13.991.3.4",
        "trapread": 0,
        "enterprise": ".1.3.6.1.4.1.2021",
        "id": 151
    }


<ip address>/api/filter/read

- This example will return all the filters that have been created, also in JSON format as seen below.


Here is an example of the api/filter/read request in JSON format:

.. code-block:: javascript

    {
        "Test Filter": {
            "id": 65,
            "actions": [
                {
                    "comparison": "__contains",
                    "value": ".41",
                    "column_name": "hostname"
                }
            ]
        },
        "Host Filter": {
            "id": 66,
            "actions": [
                {
                    "comparison": "__contains",
                    "value": "critical",
                    "column_name": "severity"
                }
            ]
        }
    }
