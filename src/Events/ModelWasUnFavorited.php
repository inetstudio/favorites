<?php

namespace InetStudio\Favorites\Events;

use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;

/**
 * Class ModelWasUnFavorited
 * @package InetStudio\Favorites\Events
 */
class ModelWasUnFavorited
{
    public $model;
    public $user_id;

    /**
     * ModelWasUnFavorited constructor.
     * @param FavoritableContract $favoritable
     *
     * @param $user_id
     */
    public function __construct(FavoritableContract $favoritable, $user_id)
    {
        $this->model = $favoritable;
        $this->user_id = $user_id;
    }
}
