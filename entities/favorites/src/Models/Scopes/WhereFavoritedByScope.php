<?php

namespace Packages\StatusesPackage\Statuses\Scopes\Front;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class WhereFavoritedByScope.
 */
Class WhereFavoritedByScope implements Scope
{
    /**
     * @var string
     */
    protected $collection;

    /**
     * @var
     */
    protected $userId;

    /**
     * WhereFavoritedByScope constructor.
     *
     * @param  string  $collection
     * @param  null  $userId
     */
    public function __construct(string $collection, $userId)
    {
        $this->collection = $collection;
        $this->userId = $userId;
    }

    /**
     * @param  Builder  $builder
     * @param  Model  $model
     *
     * @return Builder|void
     */
    public function apply(Builder $builder, Model $model)
    {
        return $builder->whereHas('favorites', function (Builder $innerQuery) {
            $innerQuery->where('collection', $this->collection);
            $innerQuery->where('user_id', $this->userId);
        });
    }
}
