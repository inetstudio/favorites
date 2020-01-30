<?php

namespace InetStudio\FavoritesPackage\Favorites\Http\Responses\Front;

use InetStudio\FavoritesPackage\Favorites\Contracts\Services\ItemsServiceContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Requests\Front\AddRequestContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Http\Responses\Front\AddResponseContract;
use InetStudio\FavoritesPackage\Favorites\Contracts\Events\Front\ItemWasFavoritedEventContract;
use InetStudio\ACL\Users\Contracts\Services\Front\ItemsServiceContract as UsersServiceContract;

/**
 * Class AddResponse.
 */
class AddResponse implements AddResponseContract
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
     * @var ItemWasFavoritedEventContract 
     */
    protected $itemWasFavoritedEvent;

    /**
     * AddResponse constructor.
     *
     * @param  ItemsServiceContract  $itemsService
     * @param  UsersServiceContract  $usersService
     * @param  ItemWasFavoritedEventContract  $itemWasFavoritedEvent
     */
    public function __construct(
        ItemsServiceContract $itemsService,
        UsersServiceContract $usersService,
        ItemWasFavoritedEventContract $itemWasFavoritedEvent
    ) {
        $this->itemsService = $itemsService;
        $this->usersService = $usersService;
        $this->itemWasFavoritedEvent = $itemWasFavoritedEvent;
    }

    /**
     * Возвращаем ответ при получении объектов.
     *
     * @param  AddRequestContract  $request
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $type = $request->get('type');

        $this->itemsService->addToFavorites($request->get('item'), $type);

        $this->itemWasFavoritedEvent->setPayload(
            $request->get('item'),
            $this->usersService->getUserIdOrHash()
        );
        event($this->itemWasFavoritedEvent);

        $data = [
            'success' => 'success',
            'message' => trans('favorites_package_favorites::messages.add_to_favorites'),
        ];
        
        return response()->json($data);
    }
}
