<?php

namespace InetStudio\Favorites\Observers;

use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;

/**
 * Class ModelObserver
 * @package InetStudio\Favorites\Observers
 */
class ModelObserver
{
    /**
     * Событие "объект удален".
     *
     * @param FavoritableContract $favoritable
     */
    public function deleted(FavoritableContract $favoritable): void
    {
        if (! $this->removeFavoritesOnDelete($favoritable)) {
            return;
        }

        $favoritable->removeFavorites();
    }

    /**
     * Проверяем, нужно ли удалять избранное при удалении модели.
     *
     * @param FavoritableContract $favoritable
     *
     * @return bool
     */
    protected function removeFavoritesOnDelete(FavoritableContract $favoritable): bool
    {
        return isset($favoritable->removeFavoritesOnDelete) ? $favoritable->removeFavoritesOnDelete : true;
    }
}
