<?php

/**
 * shortcut to spit out static media url
 **/

if (!function_exists('media_url')) {

    function media_url($media_file)
    {
        return sprintf("%s/%s", config_item('media_url'), $media_file);
    }
}
