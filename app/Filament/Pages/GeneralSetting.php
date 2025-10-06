<?php

namespace App\Filament\Pages;
use App\Models\Participant;
use App\Events\RefreshChannel;
use App\Events\SettingChannel;
use App\Models\setting;
// use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class GeneralSetting extends Page
{
    use InteractsWithForms , HasPageShield;
    protected string $view = 'filament.pages.setting';
    protected static ?string $navigationLabel = 'General Settings';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Administration';
    // protected static ?string $navigationIcon = 'heroicon-s-cog';
    // protected static ?string $navigationGroup = 'Administration';
    // protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString("<small>Syncing Data Changes Across SPA Interfaces</small>");
    }

    // public static function getTableColumns(): array
    // {

    //     $columns = DB::getSchemaBuilder()->getColumnListing('participants');
    //     return array_combine($columns, $columns);
    // }

    public function mount(): void
    {
        $setting = Setting::latest()->first();

        if ($setting) {
            $this->data = json_decode($setting->fields, true);

            if ($setting->banner) {
                $this->data['banner'] = $setting->banner;
            }

             if ($setting->favicon) {
                $this->data['favicon'] = $setting->favicon;
            }
        }

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\Toggle::make('registration')
                    ->label('Registration')
                    ->helperText(fn($state) => $state ? 'Registration Open' : 'Registration Closed')
                    ->required(),

                Forms\Components\TextInput::make('app_title')
                    ->label('App Title')
                    ->required(),

                Forms\Components\FileUpload::make('banner')
                    ->label('Banner Image')
                    ->image()
                    ->directory('uploads')
                    ->required(),

                Forms\Components\FileUpload::make('favicon')
                    ->label('Favicon')
                    ->image()
                    ->directory('favicon')
                    ->required()
                    ->acceptedFileTypes([
                        'image/x-icon',
                        'image/vnd.microsoft.icon',

                    ]),

                Forms\Components\TextArea::make('success_message')
                    ->label('Success Message')
                    ->required(),

                    Forms\Components\TextArea::make('error_message')
                    ->label('Error Message')
                    ->required(),

                Forms\Components\TextInput::make('button_label')
                    ->label('Button Label')
                    ->required(),

                Forms\Components\TextInput::make('button_url')
                    ->label('Button Url')
                    ->url()
                    ->required(),

                Forms\Components\Toggle::make('route_lock')
                    ->label('Route Lock')
                    ->helperText(fn($state) => $state ? 'On' : 'Off')
                    ->required(),

                Forms\Components\TextInput::make('route_description')
                    ->label('Description')
                    ->required(),

                Forms\Components\Textarea::make('GA')
                    ->label('Google Analytics')
                    ->rows(4),
                    // formType: react to changes and clear steps when not "step"
Forms\Components\Select::make('formType')
    ->label('Form Type')
    ->options([
        '' => 'Select Option',
        'vertical' => 'Vertical',
        'step' => 'Step',
    ])
    ->live()
    ->native(false)
    ->reactive()
    ->required()
    ->afterStateUpdated(function ($state, callable $set) {
        if ($state !== 'step') {
            $set('steps_count', null);
            $set('steps', []);
        }
        else {
              $rows = collect(Participant::$step)
                ->map(fn ($field) => ['field_name' => $field])
                ->toArray();

            $set('vertical_fields', $rows);
        }
    }),

Forms\Components\Select::make('vertical_fields')
    ->label('Vertical Fields')
    ->multiple()            // allow selecting many
    ->reactive()
    ->visible(fn (callable $get) => $get('formType') === 'vertical')
    ->options(function () {
        // Convert each field name into a nice label
        return collect(Participant::$step)->mapWithKeys(
            fn ($field) => [$field => ucwords(str_replace('_', ' ', $field))]
        )->toArray();
    })
    ->required(),
Forms\Components\TextInput::make('steps_count')
    ->label('Number of Steps')
    ->numeric()
    ->minValue(1)
    ->maxValue(10)
    ->reactive()
    ->visible(fn (callable $get) => $get('formType') === 'step')
    ->required(fn (callable $get) => $get('formType') === 'step')
    ->afterStateUpdated(function ($state, callable $set, callable $get) {
        $stepsCount = (int) $state;
        $steps = $get('steps') ?? [];

        if ($stepsCount > count($steps)) {
            for ($i = count($steps); $i < $stepsCount; $i++) {
                $steps[] = ['name' => "Step " . ($i + 1), 'fields' => []];
            }
        } elseif ($stepsCount < count($steps)) {
            $steps = array_slice($steps, 0, $stepsCount);
        }

        $set('steps', $steps);
    }),

Forms\Components\Repeater::make('steps')
    ->label('Steps')
    ->schema([
        Forms\Components\TextInput::make('name')
            ->label('Step Name')
            ->required(),

        Forms\Components\Select::make('fields')
            ->label('Assign Fields')
            ->multiple()
            ->reactive()
            ->options(function (callable $get) {
                $all = collect(Participant::$step)->mapWithKeys(fn ($field) => [
                    $field => ucwords(str_replace('_', ' ', $field)),
                ]);

                // get all current steps state
                $allSteps = $get('../../steps') ?? [];

                // fields already selected in other steps
                $current = (array) $get('fields');
                $used = collect($allSteps)
                    ->pluck('fields')
                    ->flatten()
                    ->reject(fn ($f) => in_array($f, $current))
                    ->all();

                return $all->except($used);
            })
            ->required(),
    ])
    ->visible(fn (callable $get) => $get('formType') === 'step' && (int) $get('steps_count') > 0)
    ->disableItemCreation()
    ->disableItemDeletion()
    ->disableItemMovement()
    ->minItems(fn (callable $get) => (int) ($get('steps_count') ?? 0))
    ->maxItems(fn (callable $get) => (int) ($get('steps_count') ?? 0)),
            ])
            ->statePath('data');
    }

    public function updateSettings()
{
    $data = $this->form->getState();

    $setting = Setting::latest()->first();


    $banner = $data['banner'] ?? null;

    // dd( $data['favicon'], $banner);
    $favicon = $data['favicon'] ?? null;
    unset($data['banner']);
    unset($data['favicon']);

    if ($setting) {
        $updateData = [
            'fields' => json_encode($data, true)
        ];

        if ($banner) {

            $updateData['banner'] = $banner;
        }
         if ($favicon) {

                $updateData['favicon'] = $favicon;
         }


        $setting->update($updateData);

        Notification::make()
            ->title('Settings Updated')
            ->success()
            ->send();
    } else {
        $createData = [
            'fields' => json_encode($data, true)
        ];

        if ($banner) {

            $createData['banner'] = $banner;
        }
         if ($favicon) {

                $createData['favicon'] = $favicon;
            }

        $setting = Setting::create($createData);

        Notification::make()
            ->title('Settings Created')
            ->success()
            ->send();
    }

    event(new SettingChannel(['type' => 'settings', 'data' => $setting]));
}

    public function refreshPage()
    {
        try {
            event(new RefreshChannel());
            Notification::make()
                ->title('Refresh Page')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
