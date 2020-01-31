<?php

namespace InetStudio\FavoritesPackage\Favorites\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InetStudio\AdminPanel\Base\Services\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteModelContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Services\ItemsServiceContract;
use InetStudio\ACL\Users\Contracts\Services\Front\ItemsServiceContract as UsersServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    /**
     * @var UsersServiceContract 
     */
    protected $usersService;
    
    /**
     * ItemsService constructor.
     *
     * @param  UsersServiceContract  $usersService
     * @param  FavoriteModelContract  $model
     */
    public function __construct(UsersServiceContract $usersService, FavoriteModelContract $model)
    {
        parent::__construct($model);
        
        $this->usersService = $usersService;
    }

    /**
     * Добавляем материал в избранное пользователя.
     * 
     * @param $item
     * @param  string  $collection
     * @param  null  $userId
     *
     * @return mixed
     */
    public function addToFavorites($item, string $collection, $userId = null)
    {
        $this->addFavoritesRelations($item);
        $userId = $this->usersService->getUserIdOrHash($userId);

        $favorite = $item->favorites()
            ->where(
                [
                    'user_id' => $userId,
                    'collection' => $collection,
                ]
            )
            ->first();

        if (! $favorite) {
            $item->favorites()
                ->create(
                    [
                        'user_id' => $userId,
                        'collection' => $collection,
                    ]
                );
        }

        $this->updateFavoriteCount($item, $collection, 1);

        return $item;
    }

    /**
     * Удаляем материал из избранного у пользователя.
     *
     * @param $item
     * @param string $collection
     * @param int|null $userId
     *
     * @return mixed
     */
    public function removeFromFavorites($item, string $collection, $userId = null)
    {
        $this->addFavoritesRelations($item);
        $userId = $this->usersService->getUserIdOrHash($userId);
        
        $favorite = $item->favorites()
            ->where(
                [
                    'user_id' => $userId,
                    'collection' => $collection,
                ]
            )
            ->first();

        if ($favorite) {
            $favorite->delete();
        }

        $this->updateFavoriteCount($item, $collection, -1);

        return $item;
    }

    /**
     * Переключаем состояние материала в избранном у пользователя.
     *
     * @param $item
     * @param string $collection
     * @param int|null $userId
     *
     * @return mixed
     */
    public function toggleFavoriteOf($item, string $collection, $userId = null)
    {
        $this->addFavoritesRelations($item);
        $userId = $this->usersService->getUserIdOrHash($userId);

        $favorite = $item->favorites()
            ->where(
                [
                    'user_id' => $userId,
                    'collection' => $collection,
                ]
            )
            ->exists();

        if ($favorite) {
            $this->removeFromFavorites($item, $collection, $userId);
        } else {
            $this->addToFavorites($item, $collection, $userId);
        }

        return $item;
    }

    /**
     * Добавил ли текущий пользователь материал в избранное.
     *
     * @param $item
     * @param int|null $userId
     *
     * @return bool
     */
    public function isFavorited($item, $userId): bool
    {
        $this->addFavoritesRelations($item);
        $userId = $this->usersService->getUserIdOrHash($userId);

        return $item->favorites()
            ->where(
                [
                    'user_id' => $userId,
                ]
            )
            ->exists();
    }

    /**
     * Обновляем счетчик избранного.
     *
     * @param $item
     * @param string $collection
     * @param int $count
     *
     * @return mixed
     */
    public function updateFavoriteCount($item, string $collection, int $count)
    {
        $this->addFavoritesRelations($item);
        $counter = $item->favoritesTotal()
            ->where(
                [
                    'collection' => $collection,
                ]
            )
            ->first();

        if (! $counter) {
            $counter = $item->favoritesTotal()
                ->create(
                    [
                        'count' => 0,
                        'collection' => $collection,
                    ]
                );
        }

        if ($count < 0) {
            $counter->decrement('count');
        } else {
            $counter->increment('count');
        }

        return $item;
    }

    /**
     * Удаляем оценки у определенного типа материала.
     *
     * @param string $itemType
     *
     * @throws BindingResolutionException
     */
    public function removeFavoriteTotalOfType(string $itemType): void
    {
        if (class_exists($itemType)) {
            $item = new $itemType;
            $itemType = $item->getMorphClass();
        }

        $counters = app()
            ->make('InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteTotalModelContract')
            ->where('favoritable_type', $itemType);

        $counters->delete();
    }

    /**
     * Удаляем материал из избранного всех пользователей.
     *
     * @param $item
     *
     * @throws BindingResolutionException
     */
    public function removeModelFavorites($item): void
    {
        $this->model::where(
            [
                'favoritable_id' => $item->getKey(),
                'favoritable_type' => $item->getMorphClass(),
            ]
        )->delete();

        app()
            ->make('InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteTotalModelContract')
            ->where(
                [
                    'favoritable_id' => $item->getKey(),
                    'favoritable_type' => $item->getMorphClass(),
                ]
            )->delete();
    }

    /**
     * Получаем всех пользователей, которые добавили материал в избранное.
     *
     * @param $item
     *
     * @return Collection
     */
    public function collectFavoritersOf($item): Collection
    {
        $this->addFavoritesRelations($item);
        $userModel = $this->usersService->resolveUserModel();

        $favoritersIds = $item->favorites->pluck('user_id');

        return $userModel::whereKey($favoritersIds)->get();
    }

    /**
     * Получаем счетчики по типу материала.
     *
     * @param string $itemType
     *
     * @return array
     */
    public function fetchFavoritesCounters(string $itemType): array
    {
        if (class_exists($itemType)) {
            $item = new $itemType;
            $itemType = $item->getMorphClass();
        }

        $favoritesCount = $this->model
            ->select(
                [
                    'favoritable_id',
                    'favoritable_type',
                    'collection',
                    DB::raw('COUNT(*) AS count'),
                ]
            )
            ->where('favoritable_type', $itemType)
            ->groupBy('favoritable_id', 'favoritable_type', 'collection');

        return $favoritesCount->get()->toArray();
    }

    /**
     * Добавляем объекту связи с избранным.
     *
     * @param $item
     */
    protected function addFavoritesRelations($item): void
    {
        $item::addDynamicRelation(
            'favorites',
            function () use ($item) {
                $favoriteModel = app()->make('InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteModelContract');

                return $item->morphMany(get_class($favoriteModel), 'favoritable');
            }
        );

        $item::addDynamicRelation(
            'favoritesTotal',
            function () use ($item) {
                $favoriteTotalModel = app()->make('InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteTotalModelContract');

                return $item->morphOne(get_class($favoriteTotalModel), 'favoritable');
            }
        );
    }
}
