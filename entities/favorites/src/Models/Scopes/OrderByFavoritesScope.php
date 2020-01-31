<?php

namespace InetStudio\FavoritesPackage\Favorites\Models\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use InetStudio\FavoritesPackage\Favorites\Contracts\Models\Scopes\OrderByFavoritesScopeContract;

/**
 * Class OrderByFavoritesScope.
 */
Class OrderByFavoritesScope implements OrderByFavoritesScopeContract
{
    /**
     * @var string
     */
    protected $collection;

    /**
     * @var string
     */
    protected $direction;

    /**
     * WhereFavoritedByScope constructor.
     *
     * @param  string  $collection
     * @param  string  $direction
     */
    public function __construct(string $collection, string $direction = 'desc')
    {
        $this->collection = $collection;
        $this->direction = $direction;
    }

    /**
     * @param  Builder  $builder
     * @param  Model  $model
     *
     * @return Builder|\Illuminate\Database\Query\Builder|void
     */
    public function apply(Builder $builder, Model $model)
    {
        $item = $builder->getModel();

        return $builder
            ->select($item->getTable() . '.*', 'favorites_total.count')
            ->leftJoin('favorites_total', function (JoinClause $join) use ($item) {
                $join
                    ->on('favorites_total.favoritable_id', '=', "{$item->getTable()}.{$item->getKeyName()}")
                    ->where('favorites_total.favoritable_type', '=', $item->getMorphClass())
                    ->where('favorites_total.collection', '=', $this->collection);
            })
            ->orderBy('favorites_total.count', $this->direction);
    }
}
