<?php

namespace InetStudio\FavoritesPackage\Favorites\Events\Front;

use InetStudio\FavoritesPackage\Favorites\Contracts\Events\Front\ItemWasUnFavoritedEventContract;

/**
 * Class ItemWasUnFavoritedEvent.
 */
class ItemWasUnFavoritedEvent implements ItemWasUnFavoritedEventContract
{
    /**
     * @var
     */
    public $item;

    /**
     * @var
     */
    public $userId;

    /**
     * @param $item
     * @param $userId
     */
    public function setPayload($item, $userId)
    {
        $this->item = $item;
        $this->userId = $userId;
    }
}
