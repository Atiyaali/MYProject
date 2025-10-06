<?php

namespace App\Filament\Resources\Batches\Schemas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Campaign;
use Filament\Forms\Components\RichEditor;
class BatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('compain_id')
                    ->label('Select compain')
                    ->required()
                    ->native(false)
                    ->options(campaign::pluck('name', 'id')),


             TextInput::make('name')
                    ->required()
                    ->unique(),


               RichEditor::make('remarks')
                    ->columnSpanFull(),
            ]);
    }
}
