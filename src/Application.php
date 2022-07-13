<?php

namespace StarringJane\WordpressBlade;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationInterface;

class Application extends Container implements ApplicationInterface {
    public function version()
    {
        return '';
    }

    public function basePath($path = '')
    {
        return '';
    }

    public function bootstrapPath($path = '')
    {
        return '';
    }

    public function configPath($path = '')
    {
        return '';
    }

    public function databasePath($path = '')
    {
        return '';
    }

    public function environmentPath($path = '')
    {
        return '';
    }

    public function resourcePath($path = '')
    {
        return '';
    }

    public function storagePath()
    {
        return '';
    }

    public function environment(...$environments)
    {
        return $environments;
    }

    public function runningInConsole()
    {
        return false;
    }

    public function runningUnitTests()
    {
        return false;
    }

    public function isDownForMaintenance()
    {
        return false;
    }

    public function registerConfiguredProviders()
    {
        return [];
    }

    public function register($provider, $force = false)
    {
        //
    }

    public function registerDeferredProvider($provider, $service = null)
    {
        //
    }

    public function resolveProvider($provider)
    {
        //
    }

    public function boot()
    {
        //
    }

    public function booting($callback)
    {
        //
    }

    public function booted($callback)
    {
        //
    }

    public function bootstrapWith(array $bootstrappers)
    {
        //
    }

    public function configurationIsCached()
    {
        return false;
    }

    public function detectEnvironment($callback)
    {
        //
    }

    public function environmentFile()
    {
        //
    }

    public function environmentFilePath()
    {
        //
    }

    public function getCachedConfigPath()
    {
        //
    }

    public function getCachedServicesPath()
    {
        //
    }

    public function getCachedPackagesPath()
    {
        //
    }

    public function getCachedRoutesPath()
    {
        //
    }

    public function getLocale()
    {
        return '';
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getProviders($provider)
    {
        return [];
    }

    public function hasBeenBootstrapped()
    {
        return true;
    }

    public function loadDeferredProviders()
    {
        //
    }

    public function loadEnvironmentFrom($file)
    {
        //
    }

    public function routesAreCached()
    {
        //
    }

    public function setLocale($locale)
    {
        //
    }

    public function shouldSkipMiddleware()
    {
        return true;
    }

    public function terminate()
    {
        //
    }
}
