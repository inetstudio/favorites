<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'InetStudio\Favorites\Contracts\Http\Controllers\Front',
        'middleware' => ['web'],
    ],
    function () {
        Route::post('/favorites/save/{type}/{id}', 'ItemsControllerContract@save')->name('front.favorites.save');
        Route::post('/favorites/remove/{type}/{id}', 'ItemsControllerContract@remove')->name('front.favorites.remove');
    }
);
