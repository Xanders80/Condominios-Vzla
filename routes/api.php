<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta de login comentada para evitar conflicto con la ruta web
// Si necesitas autenticación por API, usa un nombre de ruta diferente como 'api.login'
// Route::post('/login', 'Auth\LoginController@login')->name('api.login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
