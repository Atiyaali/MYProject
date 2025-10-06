<?php

namespace App\Filament\Pages;
use App\Models\Setting;
use App\Models\Participant;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Testing\Fluent\Concerns\Has;

class Form extends Page
{
    use InteractsWithForms , HasPageShield;
    protected string $view = 'filament.pages.form';
    protected static ?string $navigationLabel = 'Form Builder';
    protected static string|UnitEnum|null $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 3;
    // protected static ?string $navigationGroup = 'Administration';
    // protected static string $view = 'filament.pages.form-page';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public ?array $data = [];

    public function mount(): void  
    {
        $setting = Setting::latest()->first();

        if ($setting) {
            $this->data = json_decode($setting->form_builder, true) ?? [];
        }

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('form_fields')
                    ->label('Add Fields')
                    ->default([])
                    ->schema([
                        Select::make('field_name')
                            ->label('Field Name')
                            ->reactive()
                            ->required()
                            ->options(
                                collect(Participant::$step)
                                    ->mapWithKeys(fn($f) => [$f => ucwords(str_replace('_', ' ', $f))])
                                    ->toArray()
                            )
                            ->default(null)
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->rule(function () {
                                return function (string $attribute, $value, $fail) {

                                    $allFieldNames = collect($this->data['form_fields'] ?? [])
                                        ->pluck('field_name')
                                        ->filter()
                                        ->toArray();

                                    if (count($allFieldNames) !== count(array_unique($allFieldNames))) {
                                        $fail("Duplicate field names are not allowed.");
                                    }
                                };
                            }),

                        Select::make('field_type')
                            ->label('Field Type')
                            ->reactive()
                            ->required()
                            ->options([
                                'text' => 'Text Input',
                                'textarea' => 'Textarea',
                                'select' => 'Select',
                                'number' => 'Number',
                                'email' => 'Email',
                                'checkbox' => 'Checkbox',
                            ]),

                        Repeater::make('options')
                            ->label('Options')
                            ->schema([
                                TextInput::make('option_label')
                                    ->label('Option Label')
                                    ->required(),
                                TextInput::make('option_value')
                                    ->label('Option Value')
                                    ->required(),
                            ])
                            ->createItemButtonLabel('Add Option')
                            ->disableItemMovement()
                            ->disableItemDeletion(false)
                            ->hidden(fn($get) => $get('field_type') !== 'select')
                            ->reactive(),

                        Toggle::make('required')
                            ->label('Required')
                            ->default(true),
                    ])
                    ->createItemButtonLabel('Add Field')
                    ->disableItemMovement()
                    ->disableItemDeletion(false)
                    ->reactive(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();

        $setting = Setting::latest()->first();

        if ($setting) {
            $setting->update([
                'form_builder' => json_encode($data, true),
            ]);

            Notification::make()
                ->title('Settings Updated')
                ->success()
                ->send();
        } else {
            Setting::create([
                'form_builder' => json_encode($data, true),
            ]);

            Notification::make()
                ->title('Settings Created')
                ->success()
                ->send();
        }
    }
}
