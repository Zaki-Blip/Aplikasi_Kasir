<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Produk;
use App\Models\SubCategory;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $activenavigationIcon = 'heroicon-s-squares-2x2';

    protected static ?string $navigationGroup = 'Produk Management';
    public static function generateSku(Get $get, Set $set): void{
        $brand = Brand::find($get('brand_id'));
        $category = Category::find($get('category_id'));
        $subcategory = SubCategory::find($get('subcategory_id'));

        if(!$category || !$subcategory || $brand){
            return;
        }

        //ambil 3 huruf pertama

        $catCode = strtoupper(substr($category->name,0,3));
        $subCode = strtoupper(substr($category->name,0,3));
        $braCode = strtoupper(substr($category->name,0,3));

        $lastSku = Produk::where('category_id',$category->id)
        ->where('subcategory_id', $subcategory->id)
        ->where('brand_id', $brand->id)
        ->orderBy('id','desc')
        ->value('sku');

        $nextNumber = 1;

        if($lastSku){
            $parts=explode('-',$lastSku);
            $lastNumber = intval(end($parts));
            $nextNumber = $lastNumber +1;
        }
        $sku = sprintf('%s-%s-%s-03d',$catCode,$subCode,$braCode,$nextNumber);
        $set ('sku',$sku);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('base_price')
                            ->numeric()
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('stok')
                            ->required()
                            ->numeric(),
                        TextInput::make('sku')
                            ->numeric()
                            ->prefix('IDR'),
                        TextInput::make('barcode')
                            ->numeric()
                            ->prefix('IDR'),
                        Group::make([
                            Forms\Components\Toggle::make('is_active')
                            ->required(),
                            Forms\Components\Toggle::make('in_stock')
                            ->required(),
                        ]),

                        RichEditor::make('description')
                            ->columnSpanFull(),


                    ])->columns(3)
                        ->description('Product Detail'),
                ])->columnSpan(2),
                Section::make([
                    Select::make('brand_id')
                        ->relationship('brand', 'name',fn($query)=>$query->where('is_active',true))
                        ->reactive()
                        ->afterStateUpdated(function(Get $get, Set $set){
                            static::generateSku($get,$set);
                        }),
                    Select::make('category_id')
                        ->relationship('category', 'name',fn($query)=>$query->where('is_active',true))
                        ->reactive()
                        ->afterStateUpdated(function(Get $get, Set $set){
                            static::generateSku($get,$set);
                        }),
                    Select::make('subcategory_id')
                    ->label('Sub Category')
                    ->options(function(Get $get){
                        $CategoryId = $get('category_id');

                        if(!$CategoryId)return[];

                        return SubCategory::where('category_id',$CategoryId)
                            ->pluck('name','id');
                    })->reactive()
                        ->disabled(fn(callable $get)=>$get('category_id')===null)
                        ->dehydrated()
                        ->afterStateUpdated(function(Get $get, Set $set){
                            static::generateSku($get,$set);
                        }),
                    Forms\Components\FileUpload::make('image')
                            ->image()
                            ->maxSize(10240),
                ])->columnSpan(1)
                    ->description('Association'),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->money('idr')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->money('idr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('subcategory.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),

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
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'view' => Pages\ViewProduk::route('/{record}'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
