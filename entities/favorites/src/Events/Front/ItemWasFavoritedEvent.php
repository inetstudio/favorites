<?php

namespace InetStudio\FavoritesPackage\Favorites\Events\Front;

use InetStudio\FavoritesPackage\Favorites\Contracts\Events\Front\ItemWasFavoritedEventContract;

/**
 * Class ItemWasFavoritedEvent.
 */
class ItemWasFavoritedEvent implements ItemWasFavoritedEventContract
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
