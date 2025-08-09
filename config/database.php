<?php

return [
    'database_connection' => [
        'driver'        => 'mysql',
        'host'          => env('CONN_CHATIFY_IP', config('jtchatifyconfig.conn_chatify_ip')),
        'port'          => env('CONN_CHATIFY_PT', config('jtchatifyconfig.conn_chatify_pt')),
        'database'      => env('CONN_CHATIFY_DB', config('jtchatifyconfig.conn_chatify_db')),
        'username'      => env('CONN_CHATIFY_UN', config('jtchatifyconfig.conn_chatify_un')),
        'password'      => env('CONN_CHATIFY_PW', config('jtchatifyconfig.conn_chatify_pw')),
        'charset'       => 'utf8mb4',
        'collation'     => 'utf8mb4_unicode_ci',
        'prefix'        => ''
    ],
];