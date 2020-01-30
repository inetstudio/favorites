<?php

namespace InetStudio\FavoritesPackage\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand.
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:favorites-package:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup favorites package';

    /**
     * Инициализация команд.
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Favorites setup',
                'command' => 'inetstudio:favorites-package:favorites:setup',
            ],
        ];
    }
}
