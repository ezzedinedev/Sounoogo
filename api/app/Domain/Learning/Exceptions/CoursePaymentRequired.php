<?php

namespace App\Domain\Learning\Exceptions;

use App\Domain\Learning\Models\Course;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CoursePaymentRequired extends HttpException
{
    public function __construct(public readonly Course $course)
    {
        parent::__construct(402, 'Cette formation nécessite un achat ou un abonnement.');
    }
}
