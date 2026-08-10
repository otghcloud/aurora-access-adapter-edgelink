<?php

declare(strict_types=1);

namespace OTGH\AccessControl\EdgelinkAdapter;

use Illuminate\Support\ServiceProvider;
use OTGH\AccessControl\Core\Services\AccessControl\AccessControlCapabilityRegistry;
use OTGH\AccessControl\Core\Services\AccessControl\OutputAdapterRegistry;

class EdgelinkAdapterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EdgelinkOutputAdapter::class);

        $this->app->afterResolving(OutputAdapterRegistry::class, function (OutputAdapterRegistry $registry): void {
            $registry->register($this->app->make(EdgelinkOutputAdapter::class));
        });

        $this->app->afterResolving(AccessControlCapabilityRegistry::class, function (AccessControlCapabilityRegistry $registry): void {
            $registry->registerBindingAdapterType('edgelink', 'EDGELINK');
            $registry->registerSourceType('edgelink', 'EDGELINK');
        });
    }

    public function boot(): void {}
}
