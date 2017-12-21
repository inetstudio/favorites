<?php

namespace InetStudio\Favorites\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface FavoriteTotalModelContract
 * @package InetStudio\Favorites\Contracts\Models
 */
interface FavoriteTotalModelContract
{
    /**
     * Полиморфное отношение с остальными моделями.
     *
     * @return MorphTo
     */
    public function favoritable(): MorphTo;
}
