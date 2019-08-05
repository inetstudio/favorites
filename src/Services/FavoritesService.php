<?php

namespace InetStudio\Favorites\Services;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Contracts\Auth\Authenticatable;
use InetStudio\Favorites\Contracts\Models\FavoriteModelContract;
use InetStudio\Favorites\Contracts\Models\FavoriteTotalModelContract;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;
use InetStudio\Favorites\Contracts\Services\FavoritesServiceContract;

/**
 * Class FavoritesService
 * @package InetStudio\Favorites\Services
 */
class FavoritesService implements FavoritesServiceContract
{
    public $availableTypes = [];

    /**
     * FavoritesService constructor.
     */
    public function __construct()
    {
        $this->availableTypes = config('favorites.favoritable', []);
    }

    /**
     * Проверяем материал на возможность добавления в избранное.
     *
     * @param string $type
     * @param int $id
     *
     * @return array
     */
    public function checkIsFavoritable(string $type, int $id): array
    {
        if (! isset($this->availableTypes[$type])) {
            return [
                'success' => false,
                'message' => trans('favorites::errors.materialIncorrectType'),
            ];
        }

        $model = new $this->availableTypes[$type]();

        if (! is_null($id) && $id > 0 && $item = $model::find($id)) {
            $interfaces = class_implements($item);

            if (isset($interfaces[FavoritableContract::class])) {
                return [
                    'success' => true,
                    'item' => $item,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => trans('favorites::errors.notImplementFavoritable'),
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => trans('favorites::errors.materialNotFound'),
            ];
        }
    }

    /**
     * Добавляем материал в избранное пользователя.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int|null $userId
     *
     * @return FavoritableContract
     */
    public function addToFavorites(FavoritableContract $favoritable, string $collection, $userId): FavoritableContract
    {
        $userId = $this->getFavoriterUserId($userId);

        $favorite = $favoritable->favorites()->where([
            'user_id' => $userId,
            'collection' => $collection,
        ])->first();

        if (! $favorite) {
            $favoritable->favorites()->create([
                'user_id' => $userId,
                'collection' => $collection,
            ]);
        }

        return $favoritable;
    }

    /**
     * Удаляем материал из избранного у пользователя.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int|null $userId
     *
     * @return FavoritableContract
     */
    public function removeFromFavorites(FavoritableContract $favoritable, string $collection, $userId): FavoritableContract
    {
        $favorite = $favoritable->favorites()->where([
            'user_id' => $this->getFavoriterUserId($userId),
            'collection' => $collection,
        ])->first();

        if ($favorite) {
            $favorite->delete();
        }

        return $favoritable;
    }

    /**
     * Переключаем состояние материала в избранном у пользователя.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int|null $userId
     *
     * @return FavoritableContract
     */
    public function toggleFavoriteOf(FavoritableContract $favoritable, string $collection, $userId): FavoritableContract
    {
        $userId = $this->getFavoriterUserId($userId);

        $favorite = $favoritable->favorites()->where([
            'user_id' => $userId,
            'collection' => $collection,
        ])->exists();

        if ($favorite) {
            $this->removeFromFavorites($favoritable, $collection, $userId);
        } else {
            $this->addToFavorites($favoritable, $collection, $userId);
        }

        return $favoritable;
    }

    /**
     * Добавил ли текущий пользователь материал в избранное.
     *
     * @param FavoritableContract $favoritable
     * @param int|null $userId
     *
     * @return bool
     */
    public function isFavorited(FavoritableContract $favoritable, $userId): bool
    {
        $userId = $this->getFavoriterUserId($userId);

        return $favoritable->favorites()->where([
            'user_id' => $userId,
        ])->exists();
    }

    /**
     * Обновляем счетчик избранного.
     *
     * @param FavoritableContract $favoritable
     * @param string $collection
     * @param int $count
     *
     * @return FavoritableContract
     */
    public function updateFavoriteCount(FavoritableContract $favoritable, string $collection, int $count): FavoritableContract
    {
        $counter = $favoritable->favoritesTotal()->where([
            'collection' => $collection,
        ])->first();

        if (! $counter) {
            $counter = $favoritable->favoritesTotal()->create([
                'count' => 0,
                'collection' => $collection,
            ]);
        }

        if ($count < 0) {
            $counter->decrement('count');
        } else {
            $counter->increment('count');
        }

        return $favoritable;
    }

    /**
     * Удаляем оценки у определенного типа материала.
     *
     * @param string $favoritableType
     *
     * @return void
     */
    public function removeFavoriteTotalOfType(string $favoritableType): void
    {
        if (class_exists($favoritableType)) {
            $favoritable = new $favoritableType;
            $favoritableType = $favoritable->getMorphClass();
        }

        $counters = app(FavoriteTotalModelContract::class)->where('favoritable_type', $favoritableType);

        $counters->delete();
    }

    /**
     * Удаляем материал из избранного всех пользователей.
     *
     * @param FavoritableContract $favoritable
     *
     * @return void
     */
    public function removeModelFavorites(FavoritableContract $favoritable): void
    {
        app(FavoriteModelContract::class)->where([
            'favoritable_id' => $favoritable->getKey(),
            'favoritable_type' => $favoritable->getMorphClass(),
        ])->delete();

        app(FavoriteTotalModelContract::class)->where([
            'favoritable_id' => $favoritable->getKey(),
            'favoritable_type' => $favoritable->getMorphClass(),
        ])->delete();
    }

    /**
     * Получаем всех пользователей, которые добавили материал в избранное.
     *
     * @param FavoritableContract $favoritable
     *
     * @return Collection
     */
    public function collectFavoritersOf(FavoritableContract $favoritable): Collection
    {
        $userModel = $this->resolveUserModel();

        $favoritersIds = $favoritable->favorites->pluck('user_id');

        return $userModel::whereKey($favoritersIds)->get();
    }

    /**
     * Получаем все материалы, которые добавил в избранное пользователь.
     *
     * @param Builder $query
     * @param string $collection
     * @param int|null $userId
     *
     * @return Builder
     */
    public function scopeWhereFavoritedBy(Builder $query, string $collection = 'default', $userId): Builder
    {
        $userId = $this->getFavoriterUserId($userId);

        return $query->whereHas('favorites', function (Builder $innerQuery) use ($collection, $userId) {
            $innerQuery->where('user_id', $userId);
            $innerQuery->where('collection', $collection);
        });
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
        $favoritable = $query->getModel();

        return $query
            ->select($favoritable->getTable() . '.*', 'favorites_total.count')
            ->leftJoin('favorites_total', function (JoinClause $join) use ($favoritable, $collection) {
                $join
                    ->on('favorites_total.favoritable_id', '=', "{$favoritable->getTable()}.{$favoritable->getKeyName()}")
                    ->where('favorites_total.favoritable_type', '=', $favoritable->getMorphClass())
                    ->where('favorites_total.collection', '=', $collection);
            })
            ->orderBy('favorites_total.count', $direction);
    }

    /**
     * Получаем счетчики по типу материала.
     *
     * @param string $favoritableType
     *
     * @return array
     */
    public function fetchFavoritesCounters(string $favoritableType): array
    {
        $favoritesCount = app(FavoriteModelContract::class)
            ->select([
                'favoritable_id',
                'favoritable_type',
                'collection',
                \DB::raw('COUNT(*) AS count'),
            ])
            ->where('favoritable_type', $favoritableType);

        $favoritesCount->groupBy('favoritable_id', 'favoritable_type', 'collection');

        return $favoritesCount->get()->toArray();
    }

    /**
     * Получаем id пользователя.
     *
     * @param int $userId
     *
     * @return string
     */
    public function getFavoriterUserId($userId): string
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if (! $userId) {
            $cookieData = request()->cookie('guest_user_hash');

            if ($cookieData) {
                return $cookieData;
            } else {
                $uuid = Uuid::uuid4()->toString();

                Cookie::queue('guest_user_hash', $uuid, 5256000);

                return $uuid;
            }
        }

        return $userId;
    }

    /**
     * Получаем id авторизованного пользователя.
     *
     * @return int
     */
    protected function loggedInUserId(): ?int
    {
        return auth()->id();
    }

    /**
     * Получаем имя класса пользователя.
     *
     * @return Authenticatable
     */
    private function resolveUserModel(): Authenticatable
    {
        return config('auth.providers.users.model');
    }
}
