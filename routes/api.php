<?php

/**
 * Meridian Booking and Availability Routes
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
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AvailabilityController;

// ── Bookings ──────────────────────────────────────────
Route::get('/bookings',             [BookingController::class, 'index']);
Route::get('/bookings/by-email',    [BookingController::class, 'byEmail']);
Route::get('/bookings/by-token',    [BookingController::class, 'byToken']);
Route::post('/bookings',            [BookingController::class, 'store']);
Route::patch('/bookings/{booking}', [BookingController::class, 'updateStatus']);

// ── Availability ──────────────────────────────────────
Route::get('/availability',                 [AvailabilityController::class, 'index']);
Route::post('/availability',                [AvailabilityController::class, 'save']);
Route::get('/availability/slots',           [AvailabilityController::class, 'slots']);
Route::get('/availability/calendar',        [AvailabilityController::class, 'calendar']);
Route::get('/availability/blocked',         [AvailabilityController::class, 'blockedDates']);
Route::post('/availability/blocked',        [AvailabilityController::class, 'blockDate']);
Route::delete('/availability/blocked/{id}', [AvailabilityController::class, 'unblockDate']);