<?php

namespace App\Domain\Learning\Enums;

/** Comment le membre a obtenu l'accès à une formation. */
enum EnrollmentAccess: string
{
    case Free = 'free';
    case Purchase = 'purchase';
    case Subscription = 'subscription';
}
