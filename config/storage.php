<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage chunk size
    |--------------------------------------------------------------------------
    |
    | This max size of a uploaded chunk in Kb (default 1024).
    |
    */
    'chunk_size' => env('STORAGE_CHUNK_SIZE', 1024),

    /*
    |--------------------------------------------------------------------------
    | Source of mimtypes list
    |--------------------------------------------------------------------------
    |
    | This URL that return all mimetypes availlable (default http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types).
    |
    */
    'mimetypes_src' => env('STORAGE_MIMETYPES_SRC', 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types'),

    /*
    |--------------------------------------------------------------------------
    | The disk
    |--------------------------------------------------------------------------
    |
    | This is the disk used (default public).
    |
    */
    'disk' => env('STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | The directory
    |--------------------------------------------------------------------------
    |
    | This is the directory used (default lpack_found).
    |
    */
    'dir' => env('STORAGE_DIR', 'lpack_found'),

    /*
    |--------------------------------------------------------------------------
    | The variation number
    |--------------------------------------------------------------------------
    |
    | This maximum number of variation for one image (default 10).
    |
    */
    'max_variations' => env('STORAGE_MAX_VARIATIONS', 10)
];
