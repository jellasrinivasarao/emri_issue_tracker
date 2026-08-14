<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use App\Models\MailSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'official_email' => ['required', 'email'],
            'otp' => ['nullable', 'digits:6'],
            'password' => ['nullable', 'confirmed', 'min:6'],
        ]);

        $user = User::where('official_email', $request->official_email)->first();

        if (! $user) {
            return back()
                ->withErrors(['official_email' => 'No user found with this email address.'])
                ->withInput();
        }

        if ($request->filled('otp') && $request->filled('password')) {
            if (! $user->password_reset_otp || ! $user->password_reset_otp_expires_at) {
                return back()
                    ->withErrors(['otp' => 'Please request a fresh OTP first.'])
                    ->withInput()
                    ->with('show_reset', true);
            }

            if ($user->password_reset_otp_expires_at->isPast()) {
                return back()
                    ->withErrors(['otp' => 'The OTP has expired. Please request a new one.'])
                    ->withInput()
                    ->with('show_reset', true);
            }

            if (! Hash::check($request->otp, $user->password_reset_otp)) {
                return back()
                    ->withErrors(['otp' => 'The provided OTP is incorrect.'])
                    ->withInput()
                    ->with('show_reset', true);
            }

            $user->password_hash = Hash::make($request->password);
            $user->password_reset_otp = null;
            $user->password_reset_otp_expires_at = null;
            $user->password_changed_at = now();
            $user->save();

            return redirect()->route('login')->with('status', 'Password changed successfully. Please login with your new password.');
        }

        $otp = random_int(100000, 999999);
        $user->password_reset_otp = Hash::make((string) $otp);
        $user->password_reset_otp_expires_at = now()->addMinutes(15);
        $user->save();

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
            Config::set('mail.from.address', $setting->from_address ?: config('mail.from.address'));
            Config::set('mail.from.name', $setting->from_name ?: config('mail.from.name'));
        }

        $subject = 'Your EMRI Issue Tracker OTP';
        $body = "Hello {$user->user_name},\n\n";
        $body .= "Your OTP for resetting the password is: {$otp}\n";
        $body .= "This OTP will expire in 15 minutes.\n\n";
        $body .= "If you did not request this, please ignore this email.";

        $mailLog = new MailLog();
        $mailLog->user_id = $user->user_id;
        $mailLog->to_address = $user->official_email;
        $mailLog->subject = $subject;
        $mailLog->body = $body;
        $mailLog->status = 'pending';
        $mailLog->mailer = 'smtp';
        $mailLog->save();

        try {
            Mail::raw($body, function ($message) use ($user, $subject, $setting) {
                $message->to($user->official_email)
                    ->subject($subject);

                if ($setting && $setting->from_address) {
                    $message->from($setting->from_address, $setting->from_name ?: 'EMRI Issue Tracker');
                }
            });

            $mailLog->status = 'sent';
            $mailLog->sent_at = now();
            $mailLog->save();
        } catch (\Throwable $e) {
            $mailLog->status = 'failed';
            $mailLog->error_message = $e->getMessage();
            $mailLog->save();

            return back()
                ->with('status', 'OTP request recorded. We were unable to send the email, please retry shortly.')
                ->withInput()
                ->with('show_reset', true);
        }

        return back()
            ->with('status', 'OTP has been sent to your email. Please enter it below to reset your password.')
            ->withInput()
            ->with('show_reset', true);
    }
}
