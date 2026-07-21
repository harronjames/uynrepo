<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginBan;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/main';

    /** Ban after more than 3 failed attempts. */
    protected int $maxAttempts = 3;

    protected int $decayMinutes = 525600; // ~1 year lockout fallback

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

    protected function ensureNotBanned(Request $request): void
    {
        $ban = LoginBan::query()->where('ip_address', $request->ip())->first();

        if ($ban && $ban->isBanned()) {
            abort(403, 'Access permanently blocked.');
        }
    }

    protected function registerFailedAttempt(Request $request): void
    {
        $ban = LoginBan::query()->firstOrCreate(
            ['ip_address' => $request->ip()],
            ['attempts' => 0]
        );

        $ban->attempts = (int) $ban->attempts + 1;

        if ($ban->attempts > 3) {
            $ban->banned_at = now();
        }

        $ban->save();

        if ($ban->isBanned()) {
            abort(403, 'Access permanently blocked after too many failed login attempts.');
        }
    }
}
