<?php

namespace Ecjia\App\Stats;

use Royalcms\Component\App\AppServiceProvider;

class StatsServiceProvider extends  AppServiceProvider
{
    
    public function boot()
    {
        $this->package('ecjia/app-stats');
    }
    
    public function register()
    {
        
    }
    
    
    
}