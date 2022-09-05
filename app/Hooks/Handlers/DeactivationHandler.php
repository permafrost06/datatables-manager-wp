<?php

namespace DtManager\App\Hooks\Handlers;

use DtManager\Framework\Foundation\Application;

class DeactivationHandler
{
    protected $app = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    public function handle()
    {
        // ...
    }
}
