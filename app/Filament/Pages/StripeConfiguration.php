<?php

namespace App\Filament\Pages;
use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
// use Filament\Forms\Components\Actions\Action;
use Filament\Actions\Action;
use Stripe\StripeClient;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use SebastianBergmann\Type\CallableType;

class StripeConfiguration extends Page
{
    protected string $view = 'filament.pages.stripe-configuration';
     use InteractsWithForms;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Administration';
    // protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    // protected static string $view = 'filament.pages.stripe-configuration';

    // protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];
    public ?array $test = [];
    public ?array $live = [];

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString("<small>Stripe Configuration (Test and Live mode)</small>");
    }


    public static function getTableColumns(): array
    {

        $columns = DB::getSchemaBuilder()->getColumnListing('participants');
        return array_combine($columns, $columns);
    }

    public function mount(): void
    {
        $setting = Setting::latest()->first();

        if ($setting) {
            $this->test = json_decode($setting->stripe_test, true);
            $this->live = json_decode($setting->stripe_live, true);
            $this->data = [
                'stripe_active'   => $setting->stripe_active,
                'stripe_test_mode'   => $setting->stripe_test_mode,
                'stripe_test_secret_key' => $this->test['stripe_test_secret_key'] ?? '',
                'stripe_test_publishable_key' => $this->test['stripe_test_publishable_key'] ?? '',
                'stripe_test_webhook_key' => $this->test['stripe_test_webhook_key'] ?? '',
                // 'stripe_test_statement_descriptor' => $setting->stripe_test['stripe_test_statement_descriptor'] ?? '',
                // 'stripe_test_account' => $setting->stripe_test['stripe_test_account'] ?? '',
                // 'stripe_test_event_code' => $setting->stripe_test['stripe_test_event_code'] ?? '',
                'stripe_live_secret_key' => $this->live['stripe_live_secret_key'] ?? '',
                'stripe_live_publishable_key' => $this->live['stripe_live_publishable_key'] ?? '',
                'stripe_live_webhook_key' => $this->live['stripe_live_webhook_key'] ?? '',
                // 'stripe_live_statement_descriptor' => $setting->stripe_live['stripe_live_statement_descriptor'] ?? '',
                // 'stripe_live_account' => $setting->stripe_live['stripe_live_account'] ?? '',
                // 'stripe_live_event_code' => $setting->stripe_live['stripe_live_event_code'] ?? '',
                // 'stripe_test' => $setting->stripe_test,

            ];
        }

        $this->form->fill($this->data);
    }
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([

                Checkbox::make('stripe_active')
                    ->required()
                    ->live(),

                Toggle::make('stripe_test_mode')
                    ->required()
                    ->live(),


                Forms\Components\TextInput::make('stripe_test_secret_key')
                    ->required()
                    ->visible(fn(callable $get): bool => $get('stripe_test_mode')),

                Forms\Components\TextInput::make('stripe_test_publishable_key')
                    ->required()
                    ->visible(fn(callable $get): bool => $get('stripe_test_mode')),

                Forms\Components\TextInput::make('stripe_test_webhook_key')
                    ->required()
                    ->visible(fn(callable $get): bool => $get('stripe_test_mode')),



                //  Live Stripe Configuration

                Forms\Components\TextInput::make('stripe_live_secret_key')
                    ->required()
                    ->visible(fn(callable $get): bool => !$get('stripe_test_mode')),

                Forms\Components\TextInput::make('stripe_live_publishable_key')
                    ->required()
                    ->visible(fn(callable $get): bool => !$get('stripe_test_mode')),

                Forms\Components\TextInput::make('stripe_live_webhook_key')
                    ->required()
                    ->visible(fn(callable $get): bool => !$get('stripe_test_mode'))
                    ->suffixAction(
                        Action::make('webhook_key')
                            ->icon('heroicon-m-strikethrough')
                            ->requiresConfirmation()
                            ->action(function (callable $get, $state) {
                                // dd($state, $get('stripe_live_secret_key'));
                                static::webhookKey($get('stripe_live_secret_key'));
                            }),
                    ),


            ])
            ->statePath('data');
    }

    public function updateStripe()
    {
        $data = $this->form->getState();

        // dd($data);
        // $test = $data->except('stripe_test_mode');
        unset($data['stripe_test_mode']);
        unset($data['stripe_active']);
        $stripeActive = $this->form->getState()['stripe_active'] == true ? 1 : 0;

        // dd($stripeActive);
        $stripeTestMode = $this->form->getState()['stripe_test_mode'];
        // dd($data, $stripeTestMode);

        $setting = Setting::latest()->first();

        if ($setting) {

            if ($stripeTestMode == true) {

                $setting->update([
                    'stripe_active' => $stripeActive,
                    'stripe_test_mode' => $stripeTestMode,
                    'stripe_test' => json_encode($data),

                ]);
            } else {

                $setting->update([
                    'stripe_active' => $stripeActive,

                    'stripe_test_mode' => $stripeTestMode,

                    'stripe_live' => json_encode($data),

                ]);
            }

            Notification::make()
                ->title('Settings Updated')
                ->success()
                ->send();
        } else {

            if ($stripeTestMode == true) {
                Setting::create([
                    'stripe_active' => $stripeActive,

                    'stripe_test_mode' => $stripeTestMode,
                    'stripe_test' => json_encode($data),
                ]);
            } else {
                Setting::create([
                    'stripe_active' => $stripeActive,

                    'stripe_test_mode' => $stripeTestMode,
                    'stripe_live' => json_encode($data),

                ]);
            }

            Notification::make()
                ->title('Settings Created')
                ->success()
                ->send();
        }
    }

    public function webhookKey($secret_key)
    {

        $setting = Setting::latest()->first();

        try {

            if (
                ($setting->stripe_live['stripe_live_webhook_key'] == null && !empty($setting->stripe_live['stripe_live_secret_key']))
                || (!empty($setting->stripe_live['stripe_live_secret_key']) && !empty($secret_key) && (($setting->stripe_live['stripe_live_secret_key']) != $secret_key))
            ) {

                $stripe = new StripeClient(($secret_key));
                $liveRes = $stripe->webhookEndpoints->create([
                    'url' => \config('app.url') . "/api/stripe/webhook",
                    'enabled_events' => ['checkout.session.completed', 'payment_intent.payment_failed', 'payment_intent.canceled'],
                ]);

                if ($liveRes) {

                    $stripeLiveData = $setting->stripe_live;

                    $stripeLiveData['stripe_live_webhook_key'] = $liveRes->secret;

                    $setting->update(['stripe_live' => $stripeLiveData]);
                }
            }
        } catch (\Exception $e) {

            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
