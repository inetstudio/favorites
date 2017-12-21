<?php

namespace InetStudio\Favorites\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InetStudio\Favorites\Contracts\Models\FavoriteTotalModelContract;

class FavoriteTotalModel extends Model implements FavoriteTotalModelContract
{
    public $timestamps = false;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'favorites_total';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'count', 'collection',
    ];

    /**
     * Полиморфное отношение с остальными моделями.
     *
     * @return MorphTo
     */
    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }
}
