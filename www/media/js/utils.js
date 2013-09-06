/**
 * Miscellaneous js utils
 **/

// check ajax response headers for the login page and redirect if found
function handle_logout(xhr)
{
    if(xhr.responseText.toLowerCase().indexOf("doctype") >= 0) {
        window.location.replace('/');
    }
}
