<?php

arch('le domaine ne dépend pas de la couche HTTP')
    ->expect('App\Domain')
    ->not->toUse(['App\Http\Controllers', 'App\Http\Requests']);

arch('les actions sont des classes finales invocables')
    ->expect('App\Domain\*\Actions')
    ->toBeFinal()
    ->toHaveMethod('__invoke');

arch('pas de fonctions de debug oubliées')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->not->toBeUsed();
