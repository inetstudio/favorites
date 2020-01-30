<?php

namespace InetStudio\FavoritesPackage\Favorites\Http\Responses\Front;

use InetStudio\FavoritesPackage\Favorites\Contracts\Services\ItemsServiceContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Requests\Front\RemoveRequestContract;
use InetStudio\ACL\Users\Contracts\Services\Front\ItemsServiceContract as UsersServiceContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Responses\Front\RemoveResponseContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Events\Front\ItemWasUnFavoritedEventContract;

/**
 * Class RemoveResponse.
 */
class RemoveResponse implements RemoveResponseContract
{
    /**
     * @var ItemsServiceContract
     */
    protected $itemsService;

    /**
     * @var UsersServiceContract
     */
    protected $usersService;

    /**
     * @var ItemWasUnFavoritedEventContract
     */
    protected $itemWasUnFavoritedEvent;

    /**
     * RemoveResponse constructor.
     *
     * @param  ItemsServiceContract  $itemsService
     * @param  UsersServiceContract  $usersService
     * @param  ItemWasUnFavoritedEventContract  $itemWasUnFavoritedEvent
     */
    public function __construct(
        ItemsServiceContract $itemsService,
        UsersServiceContract $usersService,
        ItemWasUnFavoritedEventContract $itemWasUnFavoritedEvent
    ) {
        $this->itemsService = $itemsService;
        $this->usersService = $usersService;
        $this->itemWasUnFavoritedEvent = $itemWasUnFavoritedEvent;
    }

    /**
     * Возвращаем ответ при получении объектов.
     *
     * @param  RemoveRequestContract  $request
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $type = $request->get('type');

        $this->itemsService->removeFromFavorites($request->get('item'), $type);

        $this->itemWasUnFavoritedEvent->setPayload(
            $request->get('item'),
            $this->usersService->getUserIdOrHash()
        );
        event($this->itemWasUnFavoritedEvent);

        $data = [
            'success' => 'success',
            'message' => trans('favorites_package_favorites::messages.remove_from_favorites'),
        ];

        return response()->json($data);
    }
}
