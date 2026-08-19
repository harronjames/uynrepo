<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginBan;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/main';

    /** Temporary IP lockout: 5 failed attempts within 15 minutes. */
    protected int $maxAttempts = 5;

    protected int $decayMinutes = 15;

    public function redirectTo(): string
    {
        return route('admin.main.index');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm(Request $request)
    {
        $this->ensureNotBanned($request);

        return view('auth.login');
    }

    protected function validateLogin(Request $request): void
    {
        $this->ensureNotBanned($request);

        $request->validate([
            $this->username() => 'required|string|email',
            'password'        => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request): bool
    {
        $email = strtolower(trim((string) $request->input($this->username())));
        $allowed = strtolower((string) config('auth.admin_email'));

        if ($email !== $allowed) {
            return false;
        }

        $user = User::query()->where('email', $allowed)->first();

        if (! $user || ! $user->isAdministrator()) {
            return false;
        }

        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $this->registerFailedAttempt($request);

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        LoginBan::query()->where('ip_address', $request->ip())->delete();

        if (! $user->isAdministrator() || strtolower($user->email) !== strtolower((string) config('auth.admin_email'))) {
            $this->guard()->logout();

            abort(403, 'Access denied.');
        }

        return redirect()->route('admin.main.index');
    }

    protected function throttleKey(Request $request): string
    {
        return 'login:ip:' . $request->ip();
    }

    protected function ensureNotBanned(Request $request): void
    {
        $ban = LoginBan::query()->where('ip_address', $request->ip())->first();

        if (! $ban) {
            return;
        }

        if ($ban->isPermanentlyBanned()) {
            abort(403, 'Access permanently blocked.');
        }

        if ($ban->isTemporarilyLocked()) {
            $this->throwLockout($ban->retryAfterSeconds());
        }

        $ban->resetIfLockExpired();
    }

    protected function registerFailedAttempt(Request $request): void
    {
        $ban = LoginBan::query()->firstOrCreate(
            ['ip_address' => $request->ip()],
            ['attempts' => 0]
        );

        $ban->resetIfLockExpired();
        $ban->attempts = (int) $ban->attempts + 1;

        if ($ban->attempts >= $this->maxAttempts) {
            $ban->locked_until = now()->addMinutes($this->decayMinutes);
            $ban->attempts = 0;
            $ban->save();
            $this->throwLockout($this->decayMinutes * 60);
        }

        $ban->save();
    }

    protected function throwLockout(int $seconds): void
    {
        $seconds = max(1, $seconds);

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
