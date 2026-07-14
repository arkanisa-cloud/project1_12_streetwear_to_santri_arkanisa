<?php

use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans-callback', [PaymentCallbackController::class, 'handleNotification']);