//###########################################################################
//#
//# nagtrap.js -  NagTrap java functions
//#
//# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
//#               2011        Nicholas Scott (nscott@nagios.com)
//# Last Modified: 13.10.2007
//#
//# License:
//#
//# This program is free software; you can redistribute it and/or modify
//# it under the terms of the GNU General Public License version 2 as
//# published by the Free Software Foundation.
//#
//# This program is distributed in the hope that it will be useful,
//# but WITHOUT ANY WARRANTY; without even the implied warranty of
//# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//# GNU General Public License for more details.
//#
//# You should have received a copy of the GNU General Public License
//# along with this program; if not, write to the Free Software
//# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
//###########################################################################

function ToggleInForm(field){
    for (i = 0; i < field.length; i++)
        field[i].checked = !field[i].checked;
}


function clearForm(oForm) {
   
    var elements = oForm.elements;
   
    oForm.reset();

    for(i=0; i<elements.length; i++) {
         
        field_type = elements[i].type.toLowerCase();
     
        switch(field_type) {
     
        case "text":
        case "password":
        case "textarea":
        case "hidden":  
         
            elements[i].value = "";
            break;
           
        case "radio":
        case "checkbox":
        
            if (elements[i].checked) {
                elements[i].checked = false;
            }
            break;

        case "select-one":
        case "select-multi":
            
            elements[i].selectedIndex = -1;
            break;

        default:
            
            break;
        }
    }
}
