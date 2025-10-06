<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
class SmtpConfiguration extends Page implements HasForms
{
    use InteractsWithForms, HasPageShield;
    protected string $view = 'filament.pages.smtp-configuration';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Administration';
    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->hasRole('super_admin') ?? false;
    // }


    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString("<small>SMTP Configuration</small>");
    }

    public function mount(): void
    {
        $setting = Setting::latest()->first();

        if ($setting) {

            $this->data = json_decode($setting->smtp, true);
        }

        $this->form->fill($this->data);
    }
    public function form(Schema $form): Schema
    {
        return $form
            ->columns(2)
            ->schema([

                Forms\Components\TextInput::make('mail_host')
                    ->required()
                    ->dehydrateStateUsing(function (string $state): string {
                        try {

                            decrypt($state);

                            return $state;
                        } catch (\Exception $e) {

                            return encrypt($state);
                        }
                    }),


                Forms\Components\TextInput::make('mail_port')
                    ->integer()
                    ->required(),

                Forms\Components\TextInput::make('mail_username')
                    ->required()
                    ->dehydrateStateUsing(function (string $state): string {
                        try {

                            decrypt($state);

                            return $state;
                        } catch (\Exception $e) {

                            return encrypt($state);
                        }
                    }),



                Forms\Components\TextInput::make('mail_password')
                    ->required()
                    ->dehydrateStateUsing(function (string $state): string {
                        try {

                            decrypt($state);

                            return $state;
                        } catch (\Exception $e) {

                            return encrypt($state);
                        }
                    }),


                Forms\Components\Select::make('mail_encryption')
                    ->options([
                        'tls' => 'TLS',
                        'ssl' => 'SSL',
                    ])
                    ->native(false)
                    ->required(),


                Forms\Components\TextInput::make('mail_from_address')
                    ->required(),


                Forms\Components\TextInput::make('mail_from_name')
                    ->required(),





            ])
            ->statePath('data');
    }

    public function updateSmtp()
    {
        $data = $this->form->getState();


        $setting = Setting::latest()->first();

        if ($setting) {


            $setting->update([
                'smtp' => json_encode($data, true)
            ]);

            Notification::make()
                ->title('Settings Updated')
                ->success()
                ->send();
        } else {


            Setting::create([
                'smtp' => json_encode($data, true)

            ]);

            Notification::make()
                ->title('Settings Created')
                ->success()
                ->send();
        }
    }
}
