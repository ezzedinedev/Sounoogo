<?php

namespace App\Domain\Learning\Enums;

/**
 * Jalons de progression dans les formations. Ils saluent la régularité sans culpabiliser :
 * aucun badge ne se perd, et une série interrompue ne retire rien de ce qui a été obtenu.
 */
enum Badge: string
{
    case FirstLesson = 'first_lesson';
    case ThreeDayStreak = 'streak_3';
    case SevenDayStreak = 'streak_7';
    case TenLessons = 'lessons_10';
    case FirstCourse = 'first_course';
    case ThreeCourses = 'three_courses';

    public function label(): string
    {
        return match ($this) {
            self::FirstLesson => 'Premier pas',
            self::ThreeDayStreak => 'Trois jours de suite',
            self::SevenDayStreak => 'Une semaine d\'élan',
            self::TenLessons => 'Dix leçons',
            self::FirstCourse => 'Parcours terminé',
            self::ThreeCourses => 'Trois parcours',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FirstLesson => 'Tu as terminé ta première leçon.',
            self::ThreeDayStreak => 'Tu as pris du temps pour toi trois jours d\'affilée.',
            self::SevenDayStreak => 'Sept jours d\'affilée : une vraie habitude.',
            self::TenLessons => 'Dix leçons terminées, toutes formations confondues.',
            self::FirstCourse => 'Tu es allé·e au bout d\'une formation.',
            self::ThreeCourses => 'Trois formations terminées.',
        };
    }

    /** Le jalon est-il atteint pour ces compteurs ? */
    public function isReachedBy(int $lessons, int $courses, int $bestStreak): bool
    {
        return match ($this) {
            self::FirstLesson => $lessons >= 1,
            self::ThreeDayStreak => $bestStreak >= 3,
            self::SevenDayStreak => $bestStreak >= 7,
            self::TenLessons => $lessons >= 10,
            self::FirstCourse => $courses >= 1,
            self::ThreeCourses => $courses >= 3,
        };
    }
}
