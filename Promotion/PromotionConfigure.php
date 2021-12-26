<?php

namespace Promotion;

use Illuminate\Support\Facades\Artisan;

class PromotionConfigure
{

    public static $runsMigrations = true;
    private static $namespace = "User";


    public static function ignoreMigrations(): self
    {
        static::$runsMigrations = false;

        return new static;
    }


    public static function seed()
    {

        $files = scandir(__DIR__ . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "seeders");
        unset($files[array_search('.', $files)]);
        unset($files[array_search('..', $files)]);
        foreach ($files as $file) {
            Artisan::call('db:seed', ['--class' => self::$namespace . "\database\seeders\\" . str_replace('.php', '', $file)]);
        }
    }
}
