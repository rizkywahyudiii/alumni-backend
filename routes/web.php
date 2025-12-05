<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'API OK']);
});

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['status' => 'OK']);
});


require __DIR__.'/auth.php';
