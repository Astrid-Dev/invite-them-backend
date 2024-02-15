<?php

use App\Http\Controllers\Auth\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')
    ->group(function () {
        Route::prefix('auth')
            ->group(function () {
                Route::post('/register', [UserAuthController::class, 'register'])
                    ->withoutMiddleware('api');
                Route::post('/login', [UserAuthController::class, 'login'])
                    ->withoutMiddleware('api');
                Route::post('/logout', [UserAuthController::class, 'logout']);
                Route::post('/refresh', [UserAuthController::class, 'refreshToken']);
            });

        Route::prefix('me')
            ->group(function () {
                Route::prefix('events')
                    ->group(function () {
                        Route::get('/', [
                            \App\Http\Controllers\Me\EventController::class,
                            'getUserCreatedEvents'
                        ]);
                        Route::post('/', [
                            \App\Http\Controllers\Me\EventController::class,
                            'createEvent'
                        ]);
                        Route::get('/stats', [
                            \App\Http\Controllers\Me\EventController::class,
                            'getCreatedEventsStats'
                        ]);

                        Route::prefix('{eventId}')
                            ->group(function () {
                                Route::get('/', [
                                    \App\Http\Controllers\Me\EventController::class,
                                    'getEvent'
                                ]);
                                Route::put('/', [
                                    \App\Http\Controllers\Me\EventController::class,
                                    'updateEvent'
                                ]);
                                Route::delete('/', [
                                    \App\Http\Controllers\Me\EventController::class,
                                    'deleteEvent'
                                ]);
                                Route::get('/stats', [
                                    \App\Http\Controllers\Me\EventController::class,
                                    'getAnEventStats'
                                ]);
                                Route::post('/send-email-invitations', [
                                    \App\Http\Controllers\Me\EventController::class,
                                    'sendEmailInvitations'
                                ]);
                                Route::post('/send-whatsapp-invitations', [
                                    \App\Http\Controllers\Me\EventController::class,
                                    'sendWhatsappInvitations'
                                ]);
                                Route::post('/send-whatsapp-reminders', [
                                    \App\Http\Controllers\Me\EventController::class,
                                    'sendWhatsAppEventReminder'
                                ]);

                                Route::prefix('tables')
                                    ->group(function () {
                                        Route::get('/', [
                                            \App\Http\Controllers\Me\TableController::class,
                                            'getEventTables'
                                        ]);
                                        Route::post('/', [
                                            \App\Http\Controllers\Me\TableController::class,
                                            'createTables'
                                        ]);

                                        Route::prefix('{tableId}')
                                            ->group(function () {
                                                Route::put('/', [
                                                    \App\Http\Controllers\Me\TableController::class,
                                                    'updateTable'
                                                ]);
                                                Route::delete('/', [
                                                    \App\Http\Controllers\Me\TableController::class,
                                                    'deleteTable'
                                                ]);
                                            });
                                    });

                                Route::prefix('guests')
                                    ->group(function () {
                                        Route::get('/', [
                                            \App\Http\Controllers\Me\GuestController::class,
                                            'getEventGuests'
                                        ]);
                                        Route::post('/', [
                                            \App\Http\Controllers\Me\GuestController::class,
                                            'createGuests'
                                        ]);

                                        Route::prefix('{guestId}')
                                            ->group(function () {
                                                Route::get('/', [
                                                    \App\Http\Controllers\Me\GuestController::class,
                                                    'getGuest'
                                                ]);
                                                Route::put('/', [
                                                    \App\Http\Controllers\Me\GuestController::class,
                                                    'updateGuest'
                                                ]);
                                                Route::delete('/', [
                                                    \App\Http\Controllers\Me\GuestController::class,
                                                    'deleteGuest'
                                                ]);

                                                Route::post('/confirm-presence', [
                                                    \App\Http\Controllers\Me\GuestController::class,
                                                    'confirmGuestPresence'
                                                ]);
                                                Route::post('/confirm-absence', [
                                                    \App\Http\Controllers\Me\GuestController::class,
                                                    'confirmGuestAbsence'
                                                ]);
                                            });
                                    });

                                Route::prefix('scanners')
                                    ->group(function () {
                                        Route::get('/', [
                                            \App\Http\Controllers\Me\ScannerController::class,
                                            'getEventScanners'
                                        ]);
                                        Route::post('/', [
                                            \App\Http\Controllers\Me\ScannerController::class,
                                            'createScanners'
                                        ]);
                                        Route::get('/search', [
                                            \App\Http\Controllers\Me\ScannerController::class,
                                            'searchForNewScanner'
                                        ]);
                                        Route::delete('/{scannerId}', [
                                            \App\Http\Controllers\Me\ScannerController::class,
                                            'deleteScanner'
                                        ]);
                                    });
                            });
                    });
            });

        Route::prefix('scanner/{eventId}')
            ->group(function () {
                Route::get('/stats', [
                    \App\Http\Controllers\Me\ScannerController::class,
                    'getScannerStats'
                ]);
                Route::prefix('{guestId}')
                    ->group(function () {
                        Route::post('/', [
                            \App\Http\Controllers\Me\ScannerController::class,
                            'saveScan'
                        ]);
                        Route::get('/', [
                            \App\Http\Controllers\Me\ScannerController::class,
                            'getGuestDetails'
                        ]);
                    });
            });
    });
