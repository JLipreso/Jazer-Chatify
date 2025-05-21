<?php

return [
    /** Project Configurations */
    'project_refid'                 => env('PROJECT_REFID', ''),


    /** Database Connection Configurations */
    'conn_chatify_ip'                 => env('CONN_CHATIFY_IP', env('DB_HOST')),
    'conn_chatify_pt'                 => env('CONN_CHATIFY_PT', env('DB_PORT')),
    'conn_chatify_db'                 => env('CONN_CHATIFY_DB', env('DB_DATABASE')),
    'conn_chatify_un'                 => env('CONN_CHATIFY_UN', env('DB_USERNAME')),
    'conn_chatify_pw'                 => env('CONN_CHATIFY_PW', env('DB_PASSWORD')),

    /** Query parameters */
    'fetch_paginate_max'            => env('FETCH_PAGINATE_MAX', 25),
];
