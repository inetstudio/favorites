<?php

namespace InetStudio\Favorites\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InetStudio\Favorites\Contracts\Models\FavoriteModelContract;

class FavoriteModel extends Model implements FavoriteModelContract
{
    const UPDATED_AT = null;

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'favorites';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'collection',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

    /**
     * Обратное отношение с моделью пользователя
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        $userClassName = Config::get('auth.model');

        if (is_null($userClassName)) {
            $userClassName = Config::get('auth.providers.users.model');
        }

        return $this->belongsTo($userClassName);
    }

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
