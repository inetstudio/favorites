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
     * @param int $userID
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsFavoritedByUser(int $userID, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(array_merge($extColumns, ['publish_date']), $with)
            ->orderBy('publish_date', 'DESC')
            ->whereFavoritedBy($this->favoritesType, $userID);

        if ($returnBuilder) {
            return $builder;
        }

        $items = $builder->get();

        return $items;
    }
}
