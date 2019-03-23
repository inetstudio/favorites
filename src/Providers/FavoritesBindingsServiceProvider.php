<?php

namespace InetStudio\Favorites\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class FavoritesBindingsServiceProvider.
 */
class FavoritesBindingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Favorites\Contracts\Events\Front\FavoritesListChangedContract' => 'InetStudio\Favorites\Events\Front\FavoritesListChanged',
        'InetStudio\Favorites\Contracts\Models\FavoriteModelContract' => 'InetStudio\Favorites\Models\FavoriteModel',
        'InetStudio\Favorites\Contracts\Models\FavoriteTotalModelContract' => 'InetStudio\Favorites\Models\FavoriteTotalModel',
        'InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract' => 'InetStudio\Favorites\Models\Traits\Favoritable',
        'InetStudio\Favorites\Contracts\Services\FavoritesServiceContract' => 'InetStudio\Favorites\Services\FavoritesService',
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
