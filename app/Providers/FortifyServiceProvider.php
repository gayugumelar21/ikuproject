<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    /** @var User $user */
                    $user = $request->user();

                    if (! $user->is_active) {
                        auth()->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return redirect()->route('login')->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi administrator.']);
                    }

                    $user->update(['last_login_at' => now()]);

                    if ($user->must_change_password) {
                        return redirect()->route('ganti-password');
                    }

                    return match (true) {
                        $user->hasRole('admin_super') => redirect()->route('dashboard'),
                        $user->hasRole('bupati') => redirect()->route('skoring-bupati.index'),
                        $user->hasRole('sekda') => redirect()->route('rekap.index'),
                        $user->hasRole('asisten') => redirect()->route('rekap.index'),
                        $user->hasRole('kepala_dinas') => redirect()->route('indikator.index'),
                        $user->hasRole('kepala_bidang') => redirect()->route('realisasi.index'),
                        $user->hasRole('kabag') => redirect()->route('realisasi.index'),
                        default => redirect()->route('dashboard'),
                    };
                }
            };
        });
    }

    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('pages::auth.login'));
        Fortify::verifyEmailView(fn () => view('pages::auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('pages::auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('pages::auth.confirm-password'));
        Fortify::registerView(fn () => view('pages::auth.register'));
        Fortify::resetPasswordView(fn () => view('pages::auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('pages::auth.forgot-password'));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
