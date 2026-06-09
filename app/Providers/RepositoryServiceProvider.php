<?php

namespace App\Providers;

use App\Domain\Repositories\CommodityRepositoryInterface;
use App\Domain\Repositories\PredictionRepositoryInterface;
use App\Domain\Repositories\PriceRecordRepositoryInterface;
use App\Domain\Repositories\RegionRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCommodityRepository;
use App\Infrastructure\Repositories\EloquentPredictionRepository;
use App\Infrastructure\Repositories\EloquentPriceRecordRepository;
use App\Infrastructure\Repositories\EloquentRegionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CommodityRepositoryInterface::class, EloquentCommodityRepository::class);
        $this->app->bind(RegionRepositoryInterface::class, EloquentRegionRepository::class);
        $this->app->bind(PriceRecordRepositoryInterface::class, EloquentPriceRecordRepository::class);
        $this->app->bind(PredictionRepositoryInterface::class, EloquentPredictionRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
