<?php

namespace InetStudio\Favorites\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface FavoriteModelContract
 * @package InetStudio\Favorites\Contracts\Models
 */
interface FavoriteModelContract
{
    /**
     * Полиморфное отношение с остальными моделями.
     *
     * @return MorphTo
     */
    public function favoritable(): MorphTo;
}
