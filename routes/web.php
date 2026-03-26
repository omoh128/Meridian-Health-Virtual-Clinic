<?php

/**
 * Web Routes for Meridian Application
 * 
 * PHP version 8.2
 * 
 * @category Router
 * @package  Meridian
 * @author   Your Name <you@example.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://laravel.com
 */

use Illuminate\Support\Facades\Route;

Route::view('/{any}', 'app')
    ->where('any', '.*');