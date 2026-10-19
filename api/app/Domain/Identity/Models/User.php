<?php

namespace App\Domain\Identity\Models;

use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Subscription;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Identity\Enums\Role;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Listening\Models\Conversation;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $pseudonym
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property Carbon|null $email_verified_at
 * @property string|null $password
 * @property bool $is_anonymous
 * @property string|null $recovery_code_hash
 * @property string $country
 * @property string $locale
 * @property bool $is_available
 * @property Carbon|null $suspended_at
 * @property Carbon|null $last_seen_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'pseudonym', 'name', 'email', 'phone', 'password', 'is_anonymous',
    'recovery_code_hash', 'country', 'locale', 'is_available',
])]
#[Hidden(['password', 'remember_token', 'recovery_code_hash'])]
#[UseFactory(UserFactory::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    private const PSEUDONYM_WORDS = [
        'Baobab', 'Karité', 'Néré', 'Tamarin', 'Savane', 'Harmattan', 'Colibri',
        'Calao', 'Fleuve', 'Étoile', 'Aube', 'Soleil', 'Lune', 'Brise', 'Source',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_anonymous' => 'boolean',
            'is_available' => 'boolean',
            'suspended_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public static function generatePseudonym(): string
    {
        return Arr::random(self::PSEUDONYM_WORDS).'-'.random_int(100, 999);
    }

    public function isOneOf(Role ...$roles): bool
    {
        return $this->hasAnyRole(array_map(fn (Role $role) => $role->value, $roles));
    }

    /** Rôle principal (un utilisateur n'a qu'un rôle dans SUNOOGO). */
    public function primaryRole(): ?Role
    {
        return Role::tryFrom((string) $this->getRoleNames()->first());
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    /** Nom affiché aux autres utilisateurs : jamais l'identité réelle d'un membre. */
    public function displayName(): string
    {
        if ($this->isOneOf(Role::Member)) {
            return $this->pseudonym;
        }

        return $this->name ?? $this->pseudonym;
    }

    /** @return HasMany<Conversation, $this> */
    public function memberConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'member_id');
    }

    /** @return HasMany<Conversation, $this> */
    public function listenerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'listener_id');
    }

    /** @return HasOne<ProfessionalProfile, $this> */
    public function professionalProfile(): HasOne
    {
        return $this->hasOne(ProfessionalProfile::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'member_id');
    }

    /** @return HasMany<Enrollment, $this> */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /** @return HasMany<Subscription, $this> */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', SubscriptionStatus::Active)
            ->where('ends_at', '>', now())
            ->latest('ends_at')
            ->first();
    }
}
