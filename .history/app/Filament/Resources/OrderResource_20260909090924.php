<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\OrderDetailRelationManager;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
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

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->hiddenLabel()
                    ->dehydrated()
                    ->default(now())
                    ->disabled()
                    ->prefix('Date: ')
                    ->columnSpanFull(),
                Group::make()
                    ->schema([

                Section::make()
                    ->description('Customer Information')
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer','name')
                            ->label('Name')
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function($state, Set $set){
                                $customer= Customer::find($state);
                                $set('phone',$customer->phone ?? null);
                                $set('address',$customer->address ?? null);

                            }),
                        Placeholder::make('phone')
                            ->content(fn(Get $get)=>Customer::find($get('customer_id'))?->phone ?? '-'),
                        Placeholder::make('address')
                            ->content(fn(Get $get)=>Customer::find($get('customer_id'))?->address ?? '-'),
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
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->afterStateUpdated(function($state, Set $set, Get $get){
                                $produk = Produk::find($state);
                                $price =$produk->price ?? 0;
                                $set('price',$price);
                                $qty=$get('qty') ?? 1;
                                $set ('qty',$qty);
                                $subtotal=$price * $qty;
                                $set ('subtotal',$subtotal);

                                $items=$get ('../../orderdetail') ??[];
                                $total=collect($items)->sum(fn($item)=>$items('subtotal'));
                                $set ('../../total_price',$total);

                                $discount =$get ('../../discount');
                                $discount_amount = $total * $discount /100;
                                $set ('../../discount_amount',$discount_amount);
                                $set ('../../total_payment', $total - $discount_amount);
                            }),
                            TextInput::make('price')
                                ->disabled()
                                ->readOnly()
                                ->numeric()
                                ->dehydrated()
                                ->formatStateUsing(fn($state, Get $get)
                                => $state ?? Produk::find($get('product_id'))?->price ?? 0)
                                ->prefix('IDR'),,
                            TextInput::make('qty')
                                ->numeric()
                                ->default(1)
                                ->reactive()
                                ->afterStateUpdated(function($state ,Set $set, Get $get){
                                    $price = $get('price') ?? 0;
                                    $set('subtotal', $price * $state);

                                    $items=$get ('../../orderdetail') ??[];
                                    $total=collect($items)->sum(fn($item)=>$items('subtotal'));
                                    $set ('../../total_price',$total);

                                    $discount =$get ('../../discount');
                                    $discount_amount = $total * $discount /100;
                                    $set ('../../discount_amount',$discount_amount);
                                    $set ('../../total_payment', $total - $discount_amount);
                                }),
                            TextInput::make('subtotal')
                                ->disabled()
                                ->numeric()
                                ->dehydrated()
                                ->default(0)
                                ->prefix('IDR'),
                        ])->columns(4)
                        ->hiddenLabel(),
                    ]),
            ])->columnsSpan(2),
                Section::make()
                    ->description('Payment Information')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed'
                            ])->default('new')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('total_price')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),
                        TextInput::make('discount')
                            ->columnSpan(1)
                            ->afterStateUpdated(function($state, Get $get, Set $set){
                                $discount=floatval($state)??0;
                                $total_price=$get ('total_price')??0;
                                $discount_amount= $total_price * $discount / 100;
                                $set ('discount_amount', $discount_amount);
                                $set ('total_payment', $total_price - $discount_amount);
                            }),
                        TextInput::make('discount_amount')
                            ->columnSpan(3)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_payment')
                            ->columnSpanFull()
                            ->disabled()
                            ->dehydrated(),

                    ])->columnSpan(1)
                    ->columns(4),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
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
            OrderDetailRelationManager::class
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
