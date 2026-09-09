<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::where('status','new')->count())
                ->description('New orders waiting to be processed')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([7,2,10,3,15,4,17])
                ->color('info'),
            Stat::make('Processing Orders', Order::where('status','processing')->count())
                ->description('Orders currently being processed')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart([7,2,10,3,15,4,17])
                ->color('warning'),
            Stat::make('Total Revenue', number_format::where('status','completed')->count())
                ->description('Orders Succesfully Completed')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([7,2,10,3,15,4,17])
                ->color('success')
        ];
    }
}
