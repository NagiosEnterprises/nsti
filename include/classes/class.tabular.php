<?php
###########################################################################
#
# class.tabular.php -  NSTI class with functions to create the frontend
#
# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
# Copyright (c) 2011 - 2012 Nicholas Scott (nscott@nagios.com)
#
# License:
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 2 as
# published by the Free Software Foundation.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
###########################################################################

/**
* This Class is the template for all pages created for NSTI.
* 
* When adding a new page, simply extend this class, put your functions
* for creating content in functions and call them in the constructor for
* the class. Obviously, you can do it your own way, but thats they way
* I'm trying for this project. See index.php and includes/classes/class.index.php
* for examples.
* 
*/

 class tabular {
    
    function
