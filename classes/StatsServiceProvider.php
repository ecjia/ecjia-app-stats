<?php

namespace Ecjia\App\Stats;

use Royalcms\Component\App\AppParentServiceProvider;

class StatsServiceProvider extends  AppParentServiceProvider
{
    
    public function boot()
    {
        $this->package('ecjia/app-stats', null, dirname(__DIR__));
    }
    
    public function register()
    {
        
    }
    
    
    
}