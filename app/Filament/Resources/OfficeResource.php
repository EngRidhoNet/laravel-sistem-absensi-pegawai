<?php

namespace App\Filament\Resources;

use App\Models\Office;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Humaidem\FilamentMapPicker\Fields\OSMMap;
use App\Filament\Resources\OfficeResource\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                ])
                ->columnSpan(2),

            Card::make()
                ->schema([
                    OSMMap::make('location')
                        ->label('Location')
                        ->showMarker()
                        ->draggable()
                        ->extraControl([
                            'zoomDelta' => 1,
                            'zoomSnap' => 0.25,
                            'wheelPxPerZoomLevel' => 60,
                        ])
                        ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set, $record) {
                            if ($record) {
                                $latitude = $record->latitude;
                                $longitude = $record->longitude;

                                if ($latitude && $longitude) {
                                    $set('location', ['lat' => $latitude, 'lng' => $longitude]);
                                }
                            }
                        })
                        ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                            $set('latitude', $state['lat']);
                            $set('longitude', $state['lng']);
                        })
                        ->tilesUrl('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'),
                ])
                ->columnSpan(2),

            Card::make()
                ->schema([
                    TextInput::make('latitude')
                        ->required()
                        ->numeric(),

                    TextInput::make('longitude')
                        ->required()
                        ->numeric(),

                    TextInput::make('radius')
                        ->required()
                        ->numeric(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->searchable(),

            TextColumn::make('latitude')
                ->sortable(),

            TextColumn::make('longitude')
                ->sortable(),

            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('radius')
                ->numeric()
                ->sortable(),
        ])
            ->filters([
                // Add filters here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            // Define relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
