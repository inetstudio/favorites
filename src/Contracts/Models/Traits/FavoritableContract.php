<?php

namespace InetStudio\Favorites\Contracts\Models\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface FavoritableContract
 * @package InetStudio\Favorites\Contracts\Models\Traits
 */
interface FavoritableContract
{
    /**
     * Получаем значение первичного ключа.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Получаем имя класса для полиморфного отношения.
     *
     * @return string
     */
    public function getMorphClass();

    /**
     * Получаем все добавления в избранное.
     *
     * @return MorphMany
     */
    public function favorites(): MorphMany;

    /**
     * Получаем количество добавлений в избранное.
     *
     * @return MorphOne
     */
    public function favoritesTotal(): MorphOne;

    /**
     * Получаем всех пользователей, которые добавили материал в избранное.
     *
     * @return Collection
     */
    public function collectFavoriters(): Collection;

    /**
     * Добавил ли текущий пользователь материал в избранное.
     *
     * @return bool
     */
    public function getFavoritedAttribute(): bool;

    /**
     * Получаем все материалы, которые добавил в избранное пользователь.
     *
     * @param Builder $query
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return Builder
     */
    public function scopeWhereFavoritedBy(Builder $query, string $collection = 'default', $userId = null): Builder;

    /**
     * Получаем все материалы, отсортированные по количеству добавлений в избранное.
     *
     * @param Builder $query
     * @param string $collection
     * @param string $direction
     *
     * @return Builder
     */
    public function scopeOrderByFavorites(Builder $query, string $collection = 'default', string $direction = 'desc'): Builder;

    /**
     * Добавляем материал в избранное пользователя.
     *
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return FavoritableContract
     */
    public function favorite(string $collection = 'default', $userId = null): FavoritableContract;

    /**
     * Удаляем материал из избранного у пользователя.
     *
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return FavoritableContract
     */
    public function unFavorite(string $collection = 'default', $userId = null): FavoritableContract;

    /**
     * Переключаем состояние материала в избранном у пользователя.
     *
     * @param string $collection
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return FavoritableContract
     */
    public function favoriteToggle(string $collection = 'default', $userId = null): FavoritableContract;

    /**
     * Добавил ли текущий пользователь материал в избранное.
     *
     * @param int|null $userId Если пусто, то берется текущий пользователь.
     *
     * @return bool
     */
    public function favorited($userId = null): bool;

    /**
     * Удаляем материала из избранного всех пользователей.
     *
     * @return void
     */
    public function removeFavorites(): void;
}
