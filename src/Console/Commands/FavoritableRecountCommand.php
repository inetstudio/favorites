<?php

namespace InetStudio\Favorites\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Relation;
use InetStudio\Favorites\Exceptions\ModelInvalidException;
use InetStudio\Favorites\Contracts\Models\FavoriteModelContract;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;
use InetStudio\Favorites\Contracts\Services\FavoritesServiceContract;
use InetStudio\Favorites\Contracts\Models\FavoriteTotalModelContract;

/**
 * Class FavoritableRecountCommand
 * @package InetStudio\Favorites\Console\Commands
 */
class FavoritableRecountCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $signature = 'inetstudio:favorites:recount {model?}';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Recount favorites for the models';

    /**
     * FavoritesService service.
     *
     * @var FavoritesServiceContract
     */
    protected $service;

    /**
     * Запуск команды.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     *
     * @throws ModelInvalidException
     */
    public function handle(Dispatcher $events): void
    {
        $model = $this->argument('model');
        $this->service = app(FavoritesServiceContract::class);

        if (empty($model)) {
            $this->recountFavoritesOfAllModelTypes();
        } else {
            $this->recountFavoritesOfModelType($model);
        }
    }

    /**
     * Пересчитать счетчик избранного для всех типов.
     *
     * @return void
     *
     * @throws ModelInvalidException
     */
    protected function recountFavoritesOfAllModelTypes(): void
    {
        $favoritableTypes = app(FavoriteModelContract::class)->select(['favoritable_type'])->groupBy('favoritable_type')->get();

        foreach ($favoritableTypes as $favorite) {
            $this->recountFavoritesOfModelType($favorite->favoritable_type);
        }
    }

    /**
     * Пересчитать счетчик избранного для определенного типа.
     *
     * @param string $modelType
     *
     * @return void
     *
     * @throws ModelInvalidException
     */
    protected function recountFavoritesOfModelType($modelType): void
    {
        $modelType = $this->normalizeModelType($modelType);

        $counters = $this->service->fetchFavoritesCounters($modelType);

        $this->service->removeFavoriteTotalOfType($modelType);

        foreach ($counters as $counter) {
            DB::table(app(FavoriteTotalModelContract::class)->getTable())->insert($counter);
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

        if (! $model instanceof FavoritableContract) {
            throw new ModelInvalidException("[$modelType] not implements Favoritable contract.");
        }

        return $modelType;
    }
}
