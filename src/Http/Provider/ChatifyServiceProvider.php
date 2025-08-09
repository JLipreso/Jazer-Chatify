<?php

namespace Jazer\Chatify\Http\Provider;

use Illuminate\Support\ServiceProvider;

class ChatifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/database.php', 'jtchatifyconfig'  
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/config.php' => config_path('jtchatifyconfig.php')
        ], 'jtchatifyconfig-config');
        
        $this->loadRoutesFrom( __DIR__ . '/../../../routes/api.php');

        config(['database.connections.conn_chatify' => config('jtchatifyconfig.database_connection')]);
    }
}
