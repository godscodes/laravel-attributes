<?php

declare(strict_types=1);

namespace Rinvex\Attributes\Providers;

use Illuminate\Support\ServiceProvider;
use Rinvex\Attributes\Models\Attribute;
use Rinvex\Support\Traits\ConsoleTools;
use Rinvex\Attributes\Models\AttributeEntity;
use Rinvex\Attributes\Console\Commands\MigrateCommand;
use Rinvex\Attributes\Console\Commands\PublishCommand;
use Rinvex\Attributes\Console\Commands\RollbackCommand;

class AttributesServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commandss = [
        'command.rinvex.attributes.migrate',
        'command.rinvex.attributes.publish',
        'command.rinvex.attributes.rollback',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.attributes');

        // Bind eloquent models to IoC container
        $this->registerModels([
            'rinvex.attributes.attribute' => Attribute::class,
            'rinvex.attributes.attribute_entity' => AttributeEntity::class,
        ]);

        // Register attributes entities
        $this->app->singleton('rinvex.attributes.entities', function ($app) {
            return collect();
        });
        $this->app->singleton('command.rinvex.attributes.migrate', function ($app) {
            return new MigrateCommand; // Replace with the actual command class
        });
        $this->app->singleton('command.rinvex.attributes.publish', function ($app) {
            return new PublishCommand; // Replace with the actual command class
        });
        $this->app->singleton('command.rinvex.attributes.rollback', function ($app) {
            return new RollbackCommand; // Replace with the actual command class
        });

        // Register console commands
        $this->commands($this->commandss);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // // Publish Resources
        // $this->publishesConfig('rinvex/laravel-attributes');
        // $this->publishesMigrations('rinvex/laravel-attributes');
        // ! $this->autoloadMigrations('rinvex/laravel-attributes') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('rinvex.attributes.php'),
        ], 'rinvex/attributes::config');
        $this->publishes([
            __DIR__.'/../../database/migrations/rinvex/attributes/' => database_path('migrations')
        ], 'rinvex/attributes::migrations');
        if (! $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }
    }
}
