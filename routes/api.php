<?php

use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
=======
Route::post('/midtrans/callback', [PaymentCallbackController::class, 'handleNotification']);
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
Route::post('/midtrans-callback', [PaymentCallbackController::class, 'handleNotification']);