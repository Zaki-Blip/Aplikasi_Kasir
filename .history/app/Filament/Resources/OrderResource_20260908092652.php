<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->hiddenLabel()
                    ->default(now())
                    ->disabled()
                    ->prefix('Date: '),
                Section::make()
                    ->description('Customer Information')
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer','name')
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function($state, Set $set){
                                $customer= Customer::find($state);
                                $set('phone',$customer->phone ?? null);
                                $set('address',$customer->address ?? null);
                            }),
                        TextInput::make('phone')
                            ->disabled(),
                        TextInput::make('address')
                            ->disabled()
                    ])->columns(3),
                Section::make()
                    ->description('Order Detail')
                    ->schema([
                        Repeater::make('orderdetail')
                        ->relationship()
                        ->schema([
                            Select::make('produk_id')
                            ->relationship('produk','name')
                            ->reactive()
                            ->afterStateUpdated(function($state, Set $set, Get $get){
                                $produk = Produk::find($state);
                                $price =$produk->price ?? 0;
                                $set('price',$price);
                                $qty=$get('qty') ?? 1;
                                $set ('qty',$qty);
                                $subtotal=$price * $qty;
                                $set ('subtotal',$subtotal);

                                $items=$get ('../../orderdetail') ??[];
                                $total=collect($items)->sum(fn(item)=>$items('subtotal'));
                                $set ('../../total_price',$total);
                            }),
                            TextInput::make('price'),
                            TextInput::make('qty')
                                ->numeric()
                                ->default(1)
                                ->afterStateUpdated(function($state ,Set $set, Get $get){
                                    $price = $get('price') ?? 0;
                                    $set('subtotal', $price * $state);
                                }),
                            TextInput::make('subtotal')
                        ])->columns(3)
                    ]),
                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
