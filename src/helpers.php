<?php

if (!function_exists('is_assoc_array')) {
    /**
     * @return \Laravel\Lumen\Routing\UrlGenerator
     */
    function is_assoc_array(array $var) {
        return count(
            array_filter(array_keys($var), 'is_string')
        ) > 0;
    }
}
