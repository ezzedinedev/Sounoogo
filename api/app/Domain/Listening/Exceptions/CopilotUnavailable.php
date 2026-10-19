<?php

namespace App\Domain\Listening\Exceptions;

use RuntimeException;

class CopilotUnavailable extends RuntimeException
{
    public static function disabled(): self
    {
        return new self('Le copilote n\'est pas activé sur cette plateforme.');
    }

    public static function failed(): self
    {
        return new self('Le copilote ne répond pas pour le moment. Réessaie dans un instant.');
    }
}
