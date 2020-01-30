<?php

namespace InetStudio\FavoritesPackage\Favorites\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class BindingsServiceProvider.
 */
class BindingsServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\FavoritesPackage\Favorites\Contracts\Events\Front\ItemWasFavoritedEventContract' => 'InetStudio\FavoritesPackage\Favorites\Events\Front\ItemWasFavoritedEvent',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Events\Front\ItemWasUnFavoritedEventContract' => 'InetStudio\FavoritesPackage\Favorites\Events\Front\ItemWasUnFavoritedEvent',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Http\Controllers\Front\ItemsControllerContract' => 'InetStudio\FavoritesPackage\Favorites\Http\Controllers\Front\ItemsController',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Http\Requests\Front\AddRequestContract' => 'InetStudio\FavoritesPackage\Favorites\Http\Requests\Front\AddRequest',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Http\Requests\Front\RemoveRequestContract' => 'InetStudio\FavoritesPackage\Favorites\Http\Requests\Front\RemoveRequest',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Http\Responses\Front\AddResponseContract' => 'InetStudio\FavoritesPackage\Favorites\Http\Responses\Front\AddResponse',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Http\Responses\Front\RemoveResponseContract' => 'InetStudio\FavoritesPackage\Favorites\Http\Responses\Front\RemoveResponse',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Listeners\RemoveFavoritesListenerContract' => 'InetStudio\FavoritesPackage\Favorites\Listeners\RemoveFavoritesListener',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteModelContract' => 'InetStudio\FavoritesPackage\Favorites\Models\FavoriteModel',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteTotalModelContract' => 'InetStudio\FavoritesPackage\Favorites\Models\FavoriteTotalModel',
        'InetStudio\FavoritesPackage\Favorites\Contracts\Services\ItemsServiceContract' => 'InetStudio\FavoritesPackage\Favorites\Services\ItemsService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
