<?php

namespace App\Domain\Listening\Exceptions;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class ConversationClosed extends ConflictHttpException
{
    public function __construct()
    {
        parent::__construct('Cette conversation est terminée.');
    }
}
