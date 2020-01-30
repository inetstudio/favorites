<?php

namespace InetStudio\FavoritesPackage\Favorites\Listeners;

use InetStudio\FavoritesPackage\Favorites\Contracts\Services\ItemsServiceContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Listeners\RemoveFavoritesListenerContract;

/**
 * Class RemoveFavoritesListener.
 */
class RemoveFavoritesListener implements RemoveFavoritesListenerContract
{
    /**
     * @var ItemsServiceContract
     */
    protected $itemsService;

    /**
     * AddItemListener constructor.
     *
     * @param  ItemsServiceContract  $itemsService
     */
    public function __construct(ItemsServiceContract $itemsService)
    {
        $this->itemsService = $itemsService;
    }

    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): void
    {
        $item = $event->item;

        $this->itemsService->removeFavorites($item);
    }
}
