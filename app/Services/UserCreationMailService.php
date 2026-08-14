<?php

namespace App\Services;

use App\Models\MailLog;
use App\Models\MailSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserCreationMailService
{
    public function send(User $user, ?string $password = null, ?User $createdBy = null): array
    {
        $toAddress = trim((string) $user->official_email);
        if ($toAddress === '') {
            return [
                'success' => false,
                'status' => 'skipped',
                'message' => 'No email address provided.',
            ];
        }

        $setting = MailSetting::query()
            ->where('is_active', true)
            ->orderByDesc('mail_setting_id')
            ->first();

        $loginPage = rtrim(config('mail.app_url', config('app.url')), '/');
        // If the configured app URL already ends with '/login', don't append another '/login'
        if (preg_match('#/login$#i', $loginPage)) {
            $loginUrl = $loginPage;
        } else {
            $loginUrl = $loginPage . '/login';
        }

        $subject = 'Your EMRI Issue Tracker account has been created';
        $body = "Hello {$user->user_name},\n\n";
        $body .= "Your EMRI Issue Tracker account has been created successfully.\n";
        $body .= "Login ID: {$user->login_id}\n";
        if ($password) {
            $body .= "Temporary Password: {$password}\n";
        }
        $body .= "\nYou can log in here: {$loginUrl}\n";
        $body .= "\nPlease log in and change your password after first sign-in.";

        $mailLog = new MailLog();
        $mailLog->user_id = $user->user_id;
        $mailLog->to_address = $toAddress;
        $mailLog->subject = $subject;
        $mailLog->body = $body;
        $mailLog->status = 'pending';
        $mailLog->mailer = 'smtp';
        $mailLog->save();

        try {
            $mailConfig = [
                'host' => $setting?->host ?: config('mail.mailers.smtp.host', env('MAIL_HOST')),
                'port' => $setting?->port ?: config('mail.mailers.smtp.port', env('MAIL_PORT')),
                'encryption' => $setting?->encryption ?: config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION')),
                'username' => $setting?->username ?: config('mail.mailers.smtp.username', env('MAIL_USERNAME')),
                'password' => $setting?->password ?: config('mail.mailers.smtp.password', env('MAIL_PASSWORD')),
            ];

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $mailConfig['host'],
                'mail.mailers.smtp.port' => $mailConfig['port'],
                'mail.mailers.smtp.encryption' => $mailConfig['encryption'],
                'mail.mailers.smtp.username' => $mailConfig['username'],
                'mail.mailers.smtp.password' => $mailConfig['password'],
                'mail.from.address' => $setting?->from_address ?: config('mail.from.address', env('MAIL_FROM_ADDRESS')),
                'mail.from.name' => $setting?->from_name ?: config('mail.from.name', env('MAIL_FROM_NAME', 'EMRI Issue Tracker')),
            ]);

            $ccAddresses = $this->resolveCcAddresses($createdBy);

            Mail::raw($body, function ($message) use ($toAddress, $subject, $setting, $ccAddresses): void {
                $message->to($toAddress)
                    ->subject($subject);

                if (! empty($ccAddresses)) {
                    $message->cc($ccAddresses);
                }

                if ($setting?->from_address) {
                    $message->from($setting->from_address, $setting->from_name ?: 'EMRI Issue Tracker');
                }
            });

            $mailLog->status = 'sent';
            $mailLog->sent_at = now();
            $mailLog->save();

            return [
                'success' => true,
                'status' => 'sent',
                'message' => 'Mail sent successfully.',
                'log' => $mailLog,
            ];
        } catch (\Throwable $e) {
            $mailLog->status = 'failed';
            $mailLog->error_message = $e->getMessage();
            $mailLog->save();

            return [
                'success' => false,
                'status' => 'failed',
                'message' => $e->getMessage(),
                'log' => $mailLog,
            ];
        }
    }

    protected function resolveCcAddresses(?User $createdBy): array
    {
        if (! $createdBy) {
            return [];
        }

        $ccAddresses = [];

        $roleNames = $createdBy->roles->pluck('role_name')->map(fn ($roleName) => strtolower((string) $roleName))->toArray();

        if ($createdBy->official_email) {
            $ccAddresses[] = $createdBy->official_email;
        }

        if (in_array('central admin', $roleNames, true)) {
            $ccAddresses[] = 'srinivasarao_jella@emri.in';
        }

        return array_values(array_unique(array_filter($ccAddresses, fn ($value) => is_string($value) && trim($value) !== '')));
    }
}
