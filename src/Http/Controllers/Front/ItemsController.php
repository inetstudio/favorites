<?php

namespace InetStudio\Favorites\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use InetStudio\AdminPanel\Base\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\Favorites\Contracts\Services\FavoritesServiceContract;
use InetStudio\Favorites\Contracts\Http\Controllers\Front\ItemsControllerContract;

/**
 * Class ItemsController.
 */
class ItemsController extends Controller implements ItemsControllerContract
{
    /**
     * Сохраняем материал в избранном.
     *
     * @param  FavoritesServiceContract  $favoritesService
     * @param  string  $type
     * @param $id
     *
     * @return JsonResponse
     *
     * @throws BindingResolutionException
     */
    public function save(FavoritesServiceContract $favoritesService, string $type, $id): JsonResponse
    {
        $check = $favoritesService->checkIsFavoritable(mb_strtolower($type), (int) $id);

        if (! $check['success']) {
            return response()->json($check);
        }

        $check['item']->favorite($type);

        event(app()->make('InetStudio\Favorites\Contracts\Events\Front\FavoritesListChangedContract'));

        return response()->json([
            'success' => 'success',
            'message' => trans('favorites::messages.favorited'),
        ]);
    }

    /**
     * Удаляем материал из избранного.
     *
     * @param FavoritesServiceContract $favoritesService
     * @param string $type
     * @param $id
     *
     * @return JsonResponse
     *
     * @throws BindingResolutionException
     */
    public function remove(FavoritesServiceContract $favoritesService, string $type, $id): JsonResponse
    {
        $check = $favoritesService->checkIsFavoritable(mb_strtolower($type), (int) $id);

        if (! $check['success']) {
            return response()->json($check);
        }

        $check['item']->unFavorite($type);

        event(app()->make('InetStudio\Favorites\Contracts\Events\Front\FavoritesListChangedContract'));

        return response()->json([
            'success' => 'success',
            'message' => trans('favorites::messages.favorited_remove'),
        ]);
    }
}
