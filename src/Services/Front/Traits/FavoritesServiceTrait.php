<?php

namespace InetStudio\Favorites\Services\Front\Traits;

/**
 * Trait FavoritesServiceTrait.
 */
trait FavoritesServiceTrait
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
        return $this->repository->getItemsFavoritedByUser($userID, $params);
    }
}
