<?php

namespace InetStudio\Favorites\Events;

use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;

/**
 * Class ModelWasFavorited
 * @package InetStudio\Favorites\Events
 */
class ModelWasFavorited
{
    public $model;
    public $user_id;

    /**
     * ModelWasFavorited constructor.
     *
     * @param FavoritableContract $favoritable
     * @param $user_id
     */
    public function __construct(FavoritableContract $favoritable, $user_id)
    {
        $this->model = $favoritable;
        $this->user_id = $user_id;
    }
}
