<?php

namespace DtManager\App\Hooks\Handlers;

use DtManager\Framework\Foundation\Application;
use DtManager\Database\DBMigrator;
use DtManager\Database\DBSeeder;

class ActivationHandler
{
    protected $app = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    public function handle($network_wide = false)
    {
        DBMigrator::run($network_wide);
        DBSeeder::run();
    }
}
