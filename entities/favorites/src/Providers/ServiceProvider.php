<?php

namespace InetStudio\FavoritesPackage\Favorites\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use InetStudio\FavoritesPackage\Favorites\Observers\FavoriteObserver;
use InetStudio\FavoritesPackage\Favorites\Contracts\Models\FavoriteModelContract;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerTranslations();
        $this->registerObservers();
    }

    /**
     * Регистрация команд.
     */
    protected function registerConsoleCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            'InetStudio\FavoritesPackage\Favorites\Console\Commands\RecountCommand',
            'InetStudio\FavoritesPackage\Favorites\Console\Commands\SetupCommand',
        ]);
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->publishes(
            [
                __DIR__.'/../../config/favorites_package_favorites.php' => config_path('favorites_package_favorites.php'),
            ],
            'config'
        );

        if (! $this->app->runningInConsole()) {
            return;
        }

        if (Schema::hasTable('favorites')) {
            return;
        }

        $timestamp = date('Y_m_d_His', time());
        $this->publishes(
            [
                __DIR__.'/../../database/migrations/create_favorites_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_favorites_tables.php'),
            ],
            'migrations'
        );
    }

    /**
     * Регистрация путей.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Регистрация переводов.
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'favorites_package_favorites');
    }

    /**
     * Регистрация наблюдателей.
     */
    protected function registerObservers(): void
    {
        $this->app->make(FavoriteModelContract::class)->observe(FavoriteObserver::class);
    }
}
