<?php

namespace InetStudio\FavoritesPackage\Favorites\Models;

use Illuminate\Database\Eloquent\Model;
use InetStudio\ACL\Users\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteModelContract;

/**
 * Class FavoriteModel.
 */
class FavoriteModel extends Model implements FavoriteModelContract
{
    /**
     * Тип сущности.
     */
    const ENTITY_TYPE = 'favorite';

    /**
     * Имя "updated at" колонки.
     */
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
        'favoritable_id',
        'favoritable_type',
        'user_id',
        'collection',
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
     * Сеттер атрибута favoritable_type.
     *
     * @param $value
     */
    public function setFavoritableTypeAttribute($value)
    {
        $this->attributes['favoritable_type'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута favoritable_id.
     *
     * @param $value
     */
    public function setFavoritableIdAttribute($value)
    {
        $this->attributes['favoritable_id'] = (int) trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута user_id.
     *
     * @param $value
     */
    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = (int) trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута collection.
     *
     * @param $value
     */
    public function setCollectionAttribute($value)
    {
        $this->attributes['collection'] = trim(strip_tags($value));
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return self::ENTITY_TYPE;
    }

    use HasUser;

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
