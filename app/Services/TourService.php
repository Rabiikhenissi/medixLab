<?php

namespace App\Services;

use App\Models\User;

/**
 * Builds the guided onboarding tour (spotlight steps) for the current role.
 */
class TourService
{
    /** The translation keys used by the tour UI itself. */
    public const UI_KEYS = [
        'skip' => 'tour.skip',
        'next' => 'tour.next',
        'finish' => 'tour.finish',
        'hint' => 'tour.action_hint',
        'replay' => 'tour.replay',
    ];

    /** Whether a tour exists for the given role code. */
    public static function hasSteps(?string $roleCode): bool
    {
        return $roleCode !== null && count(config("tour.roles.{$roleCode}", [])) > 0;
    }

    /** Whether the tour should start automatically for this user on the current page. */
    public static function shouldAutostart(?User $user, ?string $route): bool
    {
        if (! $user || ! self::hasSteps($user->group?->code)) {
            return false;
        }

        return $user->tour_completed_at === null
            && $route === $user->group->code.'.dashboard';
    }

    /**
     * The translated tour steps for the user's role, ready to be JSON-encoded.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function stepsFor(?User $user): array
    {
        $role = $user?->group?->code;

        if (! self::hasSteps($role)) {
            return [];
        }

        $steps = [];

        foreach (config("tour.roles.{$role}", []) as $key => $step) {
            $steps[] = [
                'key' => $key,
                'target' => $step['target'] ?? null,
                'title' => __($step['title'] ?? ''),
                'text' => __($step['text'] ?? ''),
                'action' => $step['action'] ?? null,
                'placement' => $step['placement'] ?? 'bottom',
            ];
        }

        return $steps;
    }

    /**
     * The translated UI strings used by the tour widget.
     *
     * @return array<string, string>
     */
    public static function uiStrings(): array
    {
        $strings = [];

        foreach (self::UI_KEYS as $key => $translationKey) {
            $strings[$key] = __($translationKey);
        }

        return $strings;
    }
}
