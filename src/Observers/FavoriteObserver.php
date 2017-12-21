<?php

namespace InetStudio\Favorites\Observers;

use InetStudio\Favorites\Events\ModelWasFavorited;
use InetStudio\Favorites\Events\ModelWasUnFavorited;
use InetStudio\Favorites\Contracts\Models\FavoriteModelContract;
use InetStudio\Favorites\Contracts\Services\FavoritesServiceContract;

/**
 * Class FavoriteObserver
 * @package InetStudio\Favorites\Observers
 */
class FavoriteObserver
{
    /**
     * Событие "объект создан".
     *
     * @param FavoriteModelContract $favorite
     */
    public function created(FavoriteModelContract $favorite): void
    {
        event(new ModelWasFavorited($favorite->favoritable, $favorite->user_id));
        app(FavoritesServiceContract::class)->updateFavoriteCount($favorite->favoritable, $favorite->collection, 1);
    }

    /**
     * Событие "объект удален".
     *
     * @param FavoriteModelContract $favorite
     */
    public function deleted(FavoriteModelContract $favorite): void
    {
        event(new ModelWasUnFavorited($favorite->favoritable, $favorite->user_id));
        app(FavoritesServiceContract::class)->updateFavoriteCount($favorite->favoritable, $favorite->collection, -1);
    }
}
