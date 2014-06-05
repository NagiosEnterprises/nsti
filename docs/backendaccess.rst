NSTI Backend
============

The new ORM (Object-Relational Mapping) is now more efficient and the database is queryable without using the front-end at all.  You can use the GET URL parameters to return JSON formatted data in your web browser.  You can also combine any number of search parameters or even filters created from the UI to drill down the search results as needed.



Accessing data from the Backend
--------------------------------

To access NSTI normally you qould go to 

    <ip address>/nsti


To access the API from a URL use keywords for getting the JSON formatted data

    <ip address>/api/trapview/read/Snmptt

This example will return every trap that is located in the main snmptt database


Furthermore you can now access the API with multiple search and filtering criteria as stated above.  

Here is a sort by date example:

.. code-block :: html

    Time Format for NSTI  MM-DD-YYYY HH:mm:ss

    ?timewritten__lt=10-10-2014 12:00:00


This will yield all traps that are YOUNGER than the given date. Similar can be done with __gt to yield traps OLDER than the given date. There is also the ability to sort by relative time. A __gt and __lt must still be given, like so:


.. code-block :: html

    ?relative_timewritten__lt=1d


Will yield traps OLDER than 1 day old. The supported date specifiers are:


.. code-block :: html

    s - Seconds
    m - Minutes
    h - Hours
    d - Days
    w - Weeks
    M - Months

Simply use any of these date suffixes and prefix it with an INTEGER representing the number of seconds, minutes, etc. that you wish to find.

Also, the Filters can now be brought up via the GET URL params just like the relative timewritten. Simply use the ?filters=<name of filter> to apply your filter to the search result.

You can now also specify more than one directive for anything in the GET params. For example, if you would specify *&id=1&id=2*, one of these would get removed from the API GET return data. Now both make it through to the query.


API Data Formatting
-------------------

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