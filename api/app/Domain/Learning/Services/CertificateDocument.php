<?php

namespace App\Domain\Learning\Services;

use App\Domain\Learning\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as Document;
use Illuminate\Support\Carbon;

/** Rendu PDF de l'attestation. */
final class CertificateDocument
{
    public function render(Certificate $certificate): Document
    {
        $certificate->loadMissing('course');
        $course = $certificate->course;
        $completedAt = $course->enrollments()->where('user_id', $certificate->user_id)->value('completed_at');

        return Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'lessons' => $course->lessons()->count(),
            'minutes' => (int) $course->lessons()->sum('duration_minutes'),
            'completedAt' => $completedAt ? Carbon::parse($completedAt) : $certificate->issued_at,
            'logo' => $this->logo(),
        ])->setPaper('a4', 'landscape');
    }

    /** Logo en ligne ; dompdf ne gère pas les dégradés SVG, la bulle passe en violet uni. */
    private function logo(): string
    {
        $svg = str_replace('url(#sunoogo-bubble)', '#5b2fb3', (string) file_get_contents(public_path('images/brand/logo-mark.svg')));

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    public function filename(Certificate $certificate): string
    {
        return 'attestation-sunoogo-'.$certificate->course->slug.'.pdf';
    }
}
