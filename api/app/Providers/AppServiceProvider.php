<?php

namespace App\Providers;

use App\Domain\Billing\Models\Payment;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Content\Models\Exercise;
use App\Domain\Content\Models\Guide;
use App\Domain\Content\Models\SupportResource;
use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Domain\Listening\Contracts\ListenerCopilot;
use App\Domain\Listening\Contracts\RiskClassifier;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\EmergencyAlert;
use App\Domain\Listening\Models\EmergencyContact;
use App\Domain\Listening\Models\Report;
use App\Domain\Wellbeing\Models\MoodEntry;
use App\Domain\Wellbeing\Models\SafetyPlan;
use App\Http\View\EmergencyContactsComposer;
use App\Infrastructure\AI\ClaudeListenerCopilot;
use App\Infrastructure\AI\ClaudeRiskClassifier;
use App\Infrastructure\AI\DisabledListenerCopilot;
use App\Infrastructure\AI\DisabledRiskClassifier;
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
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // IA : implémentations Claude si elle est activée, sinon des versions neutres (mots-clés seuls, copilote absent).
        $this->app->bind(RiskClassifier::class, fn ($app) => config('sunoogo.ai.enabled')
            ? $app->make(ClaudeRiskClassifier::class)
            : new DisabledRiskClassifier);
        $this->app->bind(ListenerCopilot::class, fn ($app) => config('sunoogo.ai.enabled')
            ? $app->make(ClaudeListenerCopilot::class)
            : new DisabledListenerCopilot);
    }

    public function boot(): void
    {
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // Alias stables en base plutôt que des noms de classes (les namespaces peuvent évoluer).
        Relation::enforceMorphMap([
            'user' => User::class,
            'conversation' => Conversation::class,
            'emergency_alert' => EmergencyAlert::class,
            'emergency_contact' => EmergencyContact::class,
            'report' => Report::class,
            'professional_profile' => ProfessionalProfile::class,
            'booking' => Booking::class,
            'payment' => Payment::class,
            'plan' => Plan::class,
            'subscription' => Subscription::class,
            'course' => Course::class,
            'lesson' => Lesson::class,
            'exercise' => Exercise::class,
            'guide' => Guide::class,
            'support_resource' => SupportResource::class,
            'safety_plan' => SafetyPlan::class,
            'mood_entry' => MoodEntry::class,
        ]);

        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);

        // Les administrateurs ont toutes les permissions.
        Gate::before(fn (User $user) => $user->hasRole(Role::Admin->value) ? true : null);

        View::composer(['components.layouts.site', 'site.*'], EmergencyContactsComposer::class);

        $this->configureRateLimiting();
        $this->configureApiDocumentation();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('messages', fn (Request $request) => Limit::perMinute(40)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('copilot', fn (Request $request) => Limit::perMinute(20)->by($request->user()?->id ?: $request->ip()));
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
