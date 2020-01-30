<?php

namespace InetStudio\FavoritesPackage\Favorites\Contracts\Http\Controllers\Front;

use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Requests\Front\AddRequestContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Responses\Front\AddResponseContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Requests\Front\RemoveRequestContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Responses\Front\RemoveResponseContract;

/**
 * Interface ItemsControllerContract.
 */
interface ItemsControllerContract
{
    /**
     * Добавляем материал в избранное.
     *
     * @param  AddRequestContract  $request
     * @param  AddResponseContract  $response
     *
     * @return AddResponseContract
     */
    public function add(AddRequestContract $request, AddResponseContract $response): AddResponseContract;

    /**
     * Удаляем материал из избранного.
     *
     * @param  RemoveRequestContract  $request
     * @param  RemoveResponseContract  $response
     *
     * @return RemoveResponseContract
     */
    public function remove(RemoveRequestContract $request, RemoveResponseContract $response): RemoveResponseContract;
}
