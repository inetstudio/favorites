<?php

namespace InetStudio\FavoritesPackage\Favorites\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\FavoritesPackage\Favorites\Exceptions\ModelInvalidException;
use InetStudio\FavoritesPackage\Favorites\Contracts\Services\ItemsServiceContract;

/**
 * Class RecountCommand.
 */
class RecountCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $signature = 'inetstudio:favorites-package:favorites:recount {model?}';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Recount favorites for the models';

    /**
     * FavoritesService service.
     *
     * @var ItemsServiceContract
     */
    protected $itemsService;

    /**
     * RecountCommand constructor.
     *
     * @param  ItemsServiceContract  $itemsService
     */
    public function __construct(ItemsServiceContract $itemsService)
    {
        parent::__construct();

        $this->itemsService = $itemsService;
    }

    /**
     * Запуск команды.
     *
     * @throws BindingResolutionException
     * @throws ModelInvalidException
     */
    public function handle(): void
    {
        $model = $this->argument('model');

        if (empty($model)) {
            $this->recountFavoritesOfAllModelTypes();
        } else {
            $this->recountFavoritesOfModelType($model);
        }
    }

    /**
     * Пересчитать счетчик избранного для всех типов.
     *
     * @throws BindingResolutionException
     * @throws ModelInvalidException
     */
    protected function recountFavoritesOfAllModelTypes(): void
    {
        $favoritableTypes = $this->itemsService->getModel()
            ->select(['favoritable_type'])
            ->groupBy('favoritable_type')
            ->get();

        foreach ($favoritableTypes as $favorite) {
            $this->recountFavoritesOfModelType($favorite->favoritable_type);
        }
    }

    /**
     * Пересчитать счетчик избранного для определенного типа.
     *
     * @param string $modelType
     *
     * @throws BindingResolutionException
     * @throws ModelInvalidException
     */
    protected function recountFavoritesOfModelType($modelType): void
    {
        $modelType = $this->normalizeModelType($modelType);

        $counters = $this->itemsService->fetchFavoritesCounters($modelType);

        $this->itemsService->removeFavoriteTotalOfType($modelType);

        $table = app()->make('InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteTotalModelContract')->getTable();
        foreach ($counters as $counter) {
            DB::table($table)->insert($counter);
        }

        $this->info('All [' . $modelType . '] favorites has been recounted.');
    }

    /**
     * Получить тип модели.
     *
     * @param string $modelType
     *
     * @return string
     *
     * @throws ModelInvalidException
     */
    protected function normalizeModelType($modelType)
    {
        $morphMap = Relation::morphMap();

        if (class_exists($modelType)) {
            $model = new $modelType;
            $modelType = $model->getMorphClass();
        } else {
            if (! isset($morphMap[$modelType])) {
                throw new ModelInvalidException("[$modelType] class and morph map are not found.");
            }

            $modelClass = $morphMap[$modelType];
            $model = new $modelClass;
        }

        return $modelType;
    }
}
