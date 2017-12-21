<?php

namespace InetStudio\Favorites\Contracts\Services;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;

/**
 * Interface FavoritesServiceContract
 * @package InetStudio\Favorites\Contracts\Services
 */
interface FavoritesServiceContract
{
    /**
     * Добавляем материал в избранное пользователя.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int|null $userId
     *
     * @return FavoritableContract
     */
    public function addToFavorites(FavoritableContract $favoritable, string $collection, $userId): FavoritableContract;

    /**
     * Удаляем материал из избранного у пользователя.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int|null $userId
     *
     * @return FavoritableContract
     */
    public function removeFromFavorites(FavoritableContract $favoritable, string $collection, $userId): FavoritableContract;

    /**
     * Переключаем состояние материала в избранном у пользователя.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int|null $userId
     *
     * @return FavoritableContract
     */
    public function toggleFavoriteOf(FavoritableContract $favoritable, string $collection, $userId): FavoritableContract;

    /**
     * Добавил ли текущий пользователь материал в избранное.
     *
     * @param FavoritableContract $favoritable
     * @param int|null $userId
     *
     * @return bool
     */
    public function isFavorited(FavoritableContract $favoritable, $userId): bool;

    /**
     * Обновляем счетчик избранного.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int $count
     *
     * @return FavoritableContract
     */
    public function updateFavoriteCount(FavoritableContract $favoritable, string $collection, int $count): FavoritableContract;

    /**
     * Удаляем оценки у определенного типа материала.
     *
     * @param string $favoritableType
     *
     * @return void
     */
    public function removeFavoriteTotalOfType(string $favoritableType): void;

    /**
     * Удаляем материал из избранного всех пользователей.
     *
     * @param FavoritableContract $favoritable
     *
     * @return void
     */
    public function removeModelFavorites(FavoritableContract $favoritable): void;

    /**
     * Получаем всех пользователей, которые добавили материал в избранное.
     *
     * @param FavoritableContract $favoritable
     *
     * @return Collection
     */
    public function collectFavoritersOf(FavoritableContract $favoritable): Collection;

    /**
     * Получаем все материалы, которые добавил в избранное пользователь.
     *
     * @param Builder $query
     * @param string $collection
     * @param int|null $userId
     *
     * @return Builder
     */
    public function scopeWhereFavoritedBy(Builder $query, string $collection = 'default', $userId): Builder;

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
     * Получаем счетчики по типу материала.
     *
     * @param string $favoritableType
     *
     * @return array
     */
    public function fetchFavoritesCounters(string $favoritableType): array;
}
