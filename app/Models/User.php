<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'username', 'email', 'phone', 'password', 'opd_id', 'must_change_password', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->hasRole('admin_super');
    }

    public function isBupati(): bool
    {
        return $this->hasRole('bupati');
    }

    public function isSekda(): bool
    {
        return $this->hasRole('sekda');
    }

    public function isAsisten(): bool
    {
        return $this->hasRole('asisten');
    }

    public function isKepalaDinas(): bool
    {
        return $this->hasRole('kepala_dinas');
    }

    public function isKepalaBidang(): bool
    {
        return $this->hasRole('kepala_bidang');
    }

    public function isKabag(): bool
    {
        return $this->hasRole('kabag');
    }

    public function canSeeAllOpd(): bool
    {
        return $this->hasAnyRole(['admin_super', 'bupati', 'sekda', 'asisten']);
    }

    public function canScore(): bool
    {
        return $this->hasAnyRole(['admin_super', 'bupati']);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function indikatorsDibuat(): HasMany
    {
        return $this->hasMany(Indikator::class, 'dibuat_oleh');
    }

    public function realisasiDiinput(): HasMany
    {
        return $this->hasMany(Realisasi::class);
    }

    public function persetujuan(): HasMany
    {
        return $this->hasMany(Persetujuan::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
