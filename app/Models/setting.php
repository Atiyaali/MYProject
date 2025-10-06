<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
use HasFactory , LogsActivity;
  public $fillable = [
        'fields',
        'banner',
        'favicon',
        'smtp',
        'form_builder',
        'stripe_test_mode',
        'stripe_test',
        'stripe_live',
        'stripe_active',
    ];




    // public function getFieldsAttribute()
    // {
    //     return json_decode($this->fields);
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
    public static function getSmtp()
    {
        $settings = self::first();

        $smtp =   json_decode($settings->smtp);

        if (!$smtp)
            dd('Please configure smtp first.');

        return (object)[

            'mail_host' => decrypt($smtp->mail_host),
            'mail_port' => $smtp->mail_port,
            'mail_username' => decrypt($smtp->mail_username),
            'mail_password' => decrypt($smtp->mail_password),
            'mail_encryption' => $smtp->mail_encryption,
            'mail_from_address' => $smtp->mail_from_address,
            'mail_from_name' => $smtp->mail_from_name,
        ];
    }

    public static function configureSmtp()
    {
        $smtp = self::getSmtp();

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $smtp->mail_host);
        Config::set('mail.mailers.smtp.port', $smtp->mail_port);
        Config::set('mail.mailers.smtp.encryption', $smtp->mail_encryption);
        Config::set('mail.mailers.smtp.username', $smtp->mail_username);
        Config::set('mail.mailers.smtp.password', $smtp->mail_password);
        Config::set('mail.from.address', $smtp->mail_from_address);
        Config::set('mail.from.name', $smtp->mail_from_name);
    }
}
