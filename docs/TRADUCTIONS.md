# Traductions

Le français est la langue de référence. Le mooré et le dioula sont prévus : ils seront ajoutés quand des
locuteurs natifs auront traduit **et relu** les textes. Pas de traduction automatique pour un service d'écoute.

## Application (Flutter)

Les textes sont dans `mobile/lib/l10n/app_fr.arb` et lus avec `context.l10n.<clé>` :

```dart
Text(context.l10n.actionRetry)
```

La migration est progressive : navigation, états d'erreur et panneau d'urgence sont faits ; chaque écran
modifié bascule ses textes dans l'ARB.

Ajouter une langue :

1. copier `app_fr.arb` en `app_mos.arb` (mooré) ou `app_dyu.arb` (dioula) et traduire chaque valeur ;
2. ajouter `Locale('mos')` ou `Locale('dyu')` à `AppLocales.supported` (`lib/app/core/l10n/app_locales.dart`) ;
3. ces langues n'ayant pas de traductions Material intégrées, fournir un délégué de repli sur le français
   pour `MaterialLocalizations` (boutons système, sélecteurs de date) ;
4. `flutter test` vérifie que chaque fichier traduit exactement les mêmes clés que le français.

## API et back-office (Laravel)

Messages du framework (validation, mots de passe, pagination) : `api/lang/fr`, publiés avec
[Laravel Lang](https://laravel-lang.com) (`php artisan lang:update`). Les noms de champs propres à SUNOOGO sont
dans `lang/fr/validation.php`, section `attributes`. Le back-office est réservé à l'équipe et reste en français.
