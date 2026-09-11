<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginId = $this->string('login_id')->toString();
        $password = $this->input('password');
        $remember = $this->boolean('remember');
        $validatedEndUserGid = $this->session()->get('validated_end_user_gid');

        if ($validatedEndUserGid && strcasecmp($validatedEndUserGid, $loginId) !== 0) {
            throw ValidationException::withMessages([
                'login_id' => 'Invalid GID or Password.',
            ]);
        }

        $user = User::query()
            ->where('login_id', $loginId)
            ->orWhere('official_email', $loginId)
            ->first();

        $isEndUser = $user?->roles()
            ->whereRaw('LOWER(role_name) = ?', ['end user'])
            ->exists();

        if ($user && Hash::check($password, $user->getAuthPassword()) && (! $validatedEndUserGid || $isEndUser)) {
            Auth::login($user, $remember);
            RateLimiter::clear($this->throttleKey());
            $this->session()->forget('validated_end_user_gid');
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'login_id' => $validatedEndUserGid ? 'Invalid GID or Password.' : trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
                'login_id' => trans('auth.throttle', [
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login_id')).'|'.$this->ip());
    }
}
