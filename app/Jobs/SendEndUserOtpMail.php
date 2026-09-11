<?php

namespace App\Jobs;

use App\Models\MailLog;
use App\Models\MailSetting;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SendEndUserOtpMail
{
    public int $tries = 2;
    public int $timeout = 20;

    public function __construct(
        private readonly int $userId,
        private readonly int $mailLogId,
        private readonly int $otp
    ) {
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        $mailLog = MailLog::find($this->mailLogId);

        if (! $user || ! $mailLog || ! $user->official_email) {
            return;
        }

        $setting = MailSetting::query()
            ->where('is_active', true)
            ->orderByDesc('mail_setting_id')
            ->first();

        if ($setting) {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $setting->host ?: config('mail.mailers.smtp.host'));
            Config::set('mail.mailers.smtp.port', $setting->port ?: config('mail.mailers.smtp.port'));
            Config::set('mail.mailers.smtp.encryption', $setting->encryption ?: config('mail.mailers.smtp.encryption'));
            Config::set('mail.mailers.smtp.username', $setting->username ?: config('mail.mailers.smtp.username'));
            Config::set('mail.mailers.smtp.password', $setting->password ?: config('mail.mailers.smtp.password'));
            Config::set('mail.mailers.smtp.timeout', 8);
            Config::set('mail.from.address', $setting->from_address ?: config('mail.from.address'));
            Config::set('mail.from.name', $setting->from_name ?: config('mail.from.name'));
        }

        $subject = 'Your EMRI Issue Tracker login OTP';
        $body = "Hello {$user->user_name},\n\nYour login OTP is: {$this->otp}\nThis OTP will expire in 15 minutes.\n\nIf you did not request this, please ignore this email.";

        if ($mailLog) {
            $mailLog->body = $body;
            $mailLog->save();
        }

        try {
            Mail::raw($body, function ($message) use ($user, $subject, $setting): void {
                $message->to($user->official_email)->subject($subject);
                if ($setting?->from_address) {
                    $message->from($setting->from_address, $setting->from_name ?: 'EMRI Issue Tracker');
                }
            });

            $mailLog->status = 'sent';
            $mailLog->sent_at = now();
            $mailLog->save();
        } catch (\Throwable $exception) {
            $mailLog->status = 'failed';
            $mailLog->error_message = $exception->getMessage();
            $mailLog->save();
            throw $exception;
        }
    }
}
