<?php

namespace InetStudio\Favorites\Models\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use InetStudio\Favorites\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use InetStudio\Favorites\Contracts\Models\FavoriteModelContract;
use InetStudio\Favorites\Contracts\Models\FavoriteTotalModelContract;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;
use InetStudio\Favorites\Contracts\Services\FavoritesServiceContract;

/**
 * Trait Favoritable
 * @package InetStudio\Favorites\Models\Traits
 */
trait Favoritable
{
    /**
     * Загрузка трейта.
     *
     * @return void
     */
    public static function bootFavoritable(): void
    {
        static::observe(ModelObserver::class);
    }

    /**
     * Получаем все добавления в избранное.
     *
     * @return MorphMany
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(app(FavoriteModelContract::class), 'favoritable');
    }

    /**
     * Получаем количество добавлений в избранное.
     *
     * @return MorphOne
     */
    public function favoritesTotal(): MorphOne
    {
        return $this->morphOne(app(FavoriteTotalModelContract::class), 'favoritable');
    }

    /**
     * Получаем всех пользователей, которые добавили материал в избранное.
     *
     * @return Collection
     */
    public function collectFavoriters(): Collection
    {
        return app(FavoritesServiceContract::class)->collectFavoritersOf($this);
    }

    /**
     * Добавил ли текущий пользователь материал в избранное.
     *
     * @return bool
     */
    public function getFavoritedAttribute(): bool
    {
        return $this->favorited();
    }

    /**
     * Получаем все материалы, которые добавил в избранное пользователь.
     *
     * @param Builder $query
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return Builder
     */
    public function scopeWhereFavoritedBy(Builder $query, string $collection = 'default', $userId = null): Builder
    {
        return app(FavoritesServiceContract::class)
            ->scopeWhereFavoritedBy($query, $collection, $userId);
    }

    /**
     * Получаем все материалы, отсортированные по количеству добавлений в избранное.
     *
     * @param Builder $query
     * @param string $collection
     * @param string $direction
     *
     * @return Builder
     */
    public function scopeOrderByFavorites(Builder $query, string $collection = 'default', string $direction = 'desc'): Builder
    {
        return app(FavoritesServiceContract::class)
            ->scopeOrderByFavorites($query, $collection, $direction);
    }

    /**
     * Добавляем материал в избранное пользователя.
     *
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return FavoritableContract
     */
    public function favorite(string $collection = 'default', $userId = null): FavoritableContract
    {
        return app(FavoritesServiceContract::class)->addToFavorites($this, $collection, $userId);
    }

    /**
     * Удаляем материал из избранного у пользователя.
     *
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return FavoritableContract
     */
    public function unFavorite(string $collection = 'default', $userId = null): FavoritableContract
    {
        return app(FavoritesServiceContract::class)->removeFromFavorites($this, $collection, $userId);
    }

    /**
     * Переключаем состояние материала в избранном у пользователя.
     *
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return FavoritableContract
     */
    public function favoriteToggle(string $collection = 'default', $userId = null): FavoritableContract
    {
        return app(FavoritesServiceContract::class)->toggleFavoriteOf($this, $collection, $userId);
    }

    /**
     * Добавил ли текущий пользователь материал в избранное.
     *
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return bool
     */
    public function favorited($userId = null): bool
    {
        return app(FavoritesServiceContract::class)->isFavorited($this, $userId);
    }

    /**
     * Удаляем материал из избранного всех пользователей.
     *
     * @return void
     */
    public function removeFavorites(): void
    {
        app(FavoritesServiceContract::class)->removeModelFavorites($this);
    }
}
