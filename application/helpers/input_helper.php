<?php

if (!function_exists('grab_array_var')) {
    /**
     * Grabs from the request variables.
     * 
     * @param $key The key to grab from an array.
     * @param $default The default to return if it does not exist.
     * @param $xssfilter Boolean flag to enable or disable the xssfilter.
     * 
     * @returns Mixed value that was requested.
     */
    function grab_request_var($key, $default=NULL, $xssfilter=TRUE)
    {
        $ci =& get_instance();
        $post = $ci->input->post($key, $xssfilter);
        if($post !== FALSE) {
            if(is_string($post)) {
                return urldecode($post);
            }
            else {
                return $post;
            }
        }
        $get = $ci->input->get($key, $xssfilter);
        if($get !== FALSE) {
            if(is_string($get)) {
                return urldecode($get);
            }
            else {
                return $get;
            }
        }
        return $default;
    }
}

if (!function_exists('urldecode_array_walk')) {
    /**
     * Function meant to give to array_walk
     * 
     * @param $key The key will not be urldecoded.
     * @param $item The item that will actually be urldecoded.
     * 
     * @returns Mixed value that was requested.
     */
    function urldecode_array_walk(&$item, $key)
    {
        $item = urldecode($item);
    }
}
