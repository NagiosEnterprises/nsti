Introduction
============

Welcome to the official documentation for NSTI 3.0.  You can find all the information, documentation and 'how-to' guides on NSTI here.

What is NSTI?
-------------
NSTI stands for Nagios SNMP Trap Interface.  It is a JavaScript, HTML and Bootstrap frontend served by a Python framework and MySQL backend that NSTI creates. It allows you to filter SNMP results quickly and effectively to get a comprehensive overview of the information you want to see.

First and foremost let's talk about SNMP: Simple Network Management Protocol (SNMP).  This is a protocol used in network management to collect information and to configure all varieties of network devices such as servers, routers, switches and printers on an Internet Protocol (IP) network.  SNMP creates packets of data called traps, which NSTI collects and displays in a user friendly method.  NSTI also allows the user to interact with the traps they receive in a way that wouldn't normally be possible, such as trap filtering, the Trap Data Visualizer, searching traps and accessing trap information via the API.

Below is an example of the main page view with traps in the Traplist page:

.. image:: nstimain.png
	:align: center

*From this page you can see every received trap and apply filters you create to the table*

Why use NSTI?
-------------
Collecting SNMP data can be a daunting task and without a proper user interface it can be even more difficult.  Some users prefer to not use SNMP because of this reason and NSTI is designed to make SNMP a useful and informative tool without any further engineering.  This is achieved by a smart installer, a clean user interface using bootstrap and a way to collect, filter and manage all the SNMP traps.


What's new in NSTI 3.0?
-----------------------
There have been 4 big additions to NSTI 3.0:

        | Trap Filtering: The biggest addition that was made to NSTI 3.0 is that you can now create filters that can be applied to the main traplist. This allows you to check specific columns and values to be able to understand the SNMP data you have received. Using filters in this manner will make sifting through traps a lot more convenient especially when you are getting a large number of traps on a large system. To learn more about Filtering click here.

        | Trap Data Visualizer: This is the newest feature of NSTI 3.0 and it will be updated more in the future. For now it is a simple way to visualize the overall trap data you have in your table to get a better overall view of what your traps are doing. To learn more about the Trap Data Visualizer click here.

        | Trap List Search: The search bar works almost the same as filters but is a quick way to search through your traplist. For example if you enter ‘.41’ into the search field, it will return any traps that contain '.41' in any of the traplist columns: including Trap OID, Message and Hostname.

        | Trap Detailed View: The Trap Detailed View is accessed via the eyeball icon on each trap record.  Clicking this icon will pop up more details about the specific trap.