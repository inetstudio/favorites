<?php

namespace InetStudio\Favorites\Repositories\Traits;

/**
 * Trait FavoritesRepositoryTrait.
 */
trait FavoritesRepositoryTrait
{
    /**
     * Получаем сохраненные объекты пользователя.
     *
     * @param mixed $userID
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsFavoritedByUser($userID, array $params = [])
    {
        $builder = $this->getItemsQuery($params)
            ->whereFavoritedBy($this->favoritesType, $userID);

        $items = $builder->get();

        return $items;
    }
}
