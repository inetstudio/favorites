<?php

namespace InetStudio\FavoritesPackage\Favorites\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteTotalModelContract;

/**
 * Class FavoriteTotalModel.
 */
class FavoriteTotalModel extends Model implements FavoriteTotalModelContract
{
    /**
     * Тип сущности.
     */
    const ENTITY_TYPE = 'favorite_total';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
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
        'favoritable_id',
        'favoritable_type',
        'count',
        'collection',
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
     * Сеттер атрибута collection.
     *
     * @param $value
     */
    public function setCollectionAttribute($value)
    {
        $this->attributes['collection'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута count.
     *
     * @param $value
     */
    public function setCountAttribute($value)
    {
        $this->attributes['count'] = (int) trim(strip_tags($value));
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
