<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class
        ];
    }
    #[Override]
    public function getTabs(): array
    {
        return [
            null => Tab::make('All')
            'new' => Tab::make()->query(fn($query)=>$query->where('status','new'))
            'processing' => Tab::make()->query(fn($query)=>$query->where('status','new'))
            'C' => Tab::make()->query(fn($query)=>$query->where('status','new'))
            'new' => Tab::make()->query(fn($query)=>$query->where('status','new'))
        ];
    }
}
