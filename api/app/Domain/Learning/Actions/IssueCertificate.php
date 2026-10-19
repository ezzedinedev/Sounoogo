<?php

namespace App\Domain\Learning\Actions;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Enrollment;

/** Délivre l'attestation d'une formation terminée. Une seule par formation : le nom n'est plus modifiable. */
final class IssueCertificate
{
    public function __invoke(Enrollment $enrollment, string $holderName): Certificate
    {
        abort_if($enrollment->completed_at === null, 409, 'Termine toutes les leçons pour obtenir ton attestation.');

        return Certificate::firstOrCreate(
            ['user_id' => $enrollment->user_id, 'course_id' => $enrollment->course_id],
            [
                'code' => Certificate::generateCode(),
                'holder_name' => trim(preg_replace('/\s+/', ' ', $holderName) ?? $holderName),
                'issued_at' => now(),
            ],
        );
    }
}
