<?php

namespace Bildvitta\IssSupernova;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Bildvitta\IssSupernova\Commands\IssSupernovaCommand;

class IssSupernovaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('iss-supernova')
            ->hasConfigFile();
        /*
            ->hasViews()
            ->hasMigration('create_iss-supernova_table')
            ->hasCommand(IssSupernovaCommand::class);
        */
    }
}
