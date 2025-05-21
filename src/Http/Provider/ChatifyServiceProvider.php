<?php

namespace Jazer\Chatify\Http\Provider;

use Illuminate\Support\ServiceProvider;

class ChatifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../../config/database.php', 'chatify'  
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/config.php' => config_path('chatifyconfig.php')
        ], 'chatifyconfig-config');
        
        $this->loadRoutesFrom( __DIR__ . '/../../../routes/api.php');

        config(['database.connections.conn_chatify' => config('chatify.database_connection')]);
    }
}
