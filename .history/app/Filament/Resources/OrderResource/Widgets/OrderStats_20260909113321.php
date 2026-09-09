<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', '192,1k')
                ->description('New orders waiting to be')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7,2,10,3,15,4,17])
                ->color('success')
        ];
    }
}
