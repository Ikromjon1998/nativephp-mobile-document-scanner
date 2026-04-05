<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Max Pages
    |--------------------------------------------------------------------------
    |
    | The default maximum number of pages per scan session. Set to 0 for
    | unlimited. Can be overridden per-scan via options.
    |
    */

    'default_max_pages' => 0,

    /*
    |--------------------------------------------------------------------------
    | Max Pages Limit
    |--------------------------------------------------------------------------
    |
    | The absolute maximum number of pages allowed per scan. Prevents
    | accidental memory issues from scanning too many pages. Must be at
    | least 1.
    |
    */

    'max_pages_limit' => 100,

    /*
    |--------------------------------------------------------------------------
    | Default Output Format
    |--------------------------------------------------------------------------
    |
    | The default output format for scanned documents. Supported values:
    | 'jpeg' and 'pdf'.
    |
    */

    'default_output_format' => 'jpeg',

    /*
    |--------------------------------------------------------------------------
    | Default JPEG Quality
    |--------------------------------------------------------------------------
    |
    | JPEG compression quality (1-100) used when outputFormat is 'jpeg'.
    | Higher values produce larger but better quality images.
    |
    */

    'default_jpeg_quality' => 90,

    /*
    |--------------------------------------------------------------------------
    | Storage Directory
    |--------------------------------------------------------------------------
    |
    | Subdirectory within app storage where scanned documents are saved.
    | Paths returned in events are relative to this directory.
    |
    */

    'storage_directory' => 'scanned-documents',

    /*
    |--------------------------------------------------------------------------
    | Default Gallery Import
    |--------------------------------------------------------------------------
    |
    | Whether to allow importing images from the device gallery in addition
    | to live camera scanning. Android only — iOS does not support this.
    |
    */

    'default_gallery_import' => false,

];
