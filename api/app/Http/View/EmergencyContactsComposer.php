<?php

namespace App\Http\View;

use App\Domain\Listening\Services\EmergencyService;
use Illuminate\View\View;

/** Les numéros d'urgence sont visibles sur toutes les pages du site. */
class EmergencyContactsComposer
{
    public function __construct(private readonly EmergencyService $emergency) {}

    public function compose(View $view): void
    {
        $view->with('emergencyContacts', once(fn () => $this->emergency->contactsFor(null)));
    }
}
