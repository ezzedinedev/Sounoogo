<?php

namespace App\Providers;

use App\Domain\Billing\Models\Subscription;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Learning\Models\Course;
use App\Domain\Listening\Models\Conversation;
use App\Policies\BookingPolicy;
use App\Policies\ConversationPolicy;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // Alias stables en base plutôt que des noms de classes (les namespaces peuvent évoluer).
        Relation::enforceMorphMap([
            'user' => User::class,
            'booking' => Booking::class,
            'subscription' => Subscription::class,
            'course' => Course::class,
        ]);

        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);

        // Les administrateurs ont toutes les permissions.
        Gate::before(fn (User $user) => $user->hasRole(Role::Admin->value) ? true : null);

        $this->configureRateLimiting();
        $this->configureApiDocumentation();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('messages', fn (Request $request) => Limit::perMinute(40)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('sos', fn (Request $request) => Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()));
    }

    private function configureApiDocumentation(): void
    {
        Scramble::configure()
            ->routes(fn ($route) => str_starts_with($route->uri, 'api/v1'))
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(SecurityScheme::http('bearer'));
            });
    }
}
