<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct() {
        
        $maintenance = getOption('maintenance');
        if ($maintenance=='1') {
            $current_route = Route::currentRouteName();
            if (!str_starts_with($current_route, 'user') && !str_starts_with($current_route, 'profile') || auth()->user() && auth()->user()->getType() == "admin") { 
                
            } else {
                if ($current_route!="maintenance") {
                    $url = route('maintenance');
                    header("Location: $url");
                    die();
                    exit();
                }
            }
        }
    }
}
