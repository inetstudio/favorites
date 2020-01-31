<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'InetStudio\FavoritesPackage\Favorites\Contracts\Http\Controllers\Front',
        'middleware' => ['web'],
    ],
    function () {
        Route::post('/favorites/add/{type}/{id}', 'ItemsControllerContract@add')->name('front.favorites-package.favorites.add');
        Route::post('/favorites/remove/{type}/{id}', 'ItemsControllerContract@remove')->name('front.favorites-package.favorites.remove');
    }
);
