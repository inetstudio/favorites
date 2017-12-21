<?php

namespace InetStudio\Favorites\Providers;

use Illuminate\Support\ServiceProvider;
use InetStudio\Favorites\Models\FavoriteModel;
use InetStudio\Favorites\Models\FavoriteTotalModel;
use InetStudio\Favorites\Services\FavoritesService;
use InetStudio\Favorites\Observers\FavoriteObserver;
use InetStudio\Favorites\Console\Commands\SetupCommand;
use InetStudio\Favorites\Contracts\Models\FavoriteModelContract;
use InetStudio\Favorites\Console\Commands\FavoritableRecountCommand;
use InetStudio\Favorites\Contracts\Services\FavoritesServiceContract;
use InetStudio\Favorites\Contracts\Models\FavoriteTotalModelContract;

class FavoritesServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerTranslations();
        $this->registerObservers();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
                FavoritableRecountCommand::class,
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/favorites.php' => config_path('favorites.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateFavoritesTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_favorites_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_favorites_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация переводов.
     *
     * @return void
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'favorites');
    }

    /**
     * Регистрация наблюдателей.
     *
     * @return void
     */
    protected function registerObservers(): void
    {
        $this->app->make(FavoriteModelContract::class)->observe(FavoriteObserver::class);
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->bind(FavoriteModelContract::class, FavoriteModel::class);
        $this->app->bind(FavoriteTotalModelContract::class, FavoriteTotalModel::class);
        $this->app->singleton(FavoritesServiceContract::class, FavoritesService::class);
    }
}
