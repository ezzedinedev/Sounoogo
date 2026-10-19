# Avant l'ouverture au public

Ces points ne peuvent pas être réglés dans le code : ils demandent une décision, un compte ou une validation
humaine. Rien ne doit être ouvert au public tant que la section « Sécurité des personnes » n'est pas cochée.

## Sécurité des personnes

- [ ] **Numéros d'urgence** vérifiés un par un au Burkina Faso (back-office → Numéros d'urgence), avec les horaires réels.
- [ ] **Contenus cliniques relus** par l'équipe clinique : exercices, guides « Aider un proche », formations
      (marqués « à relire » dans `database/seeders/ContentSeeder.php`).
- [ ] **Mots à risque** (`config/sunoogo.php`, `risk_keywords`) complétés avec l'équipe, y compris en mooré et en dioula.
- [ ] **Protocole d'alerte** : qui reçoit les alertes la nuit et le week-end, délais de prise en charge visés.
- [ ] **Ressources de proximité** : chaque fiche vérifiée (adresse, téléphone, gratuité) avant publication.

## Comptes et services

- [ ] **MoneyFusion** : lien API du tableau de bord marchand (`MONEYFUSION_API_URL`), URL de webhook
      `https://<domaine>/api/v1/payments/webhooks/moneyfusion` déclarée, adresses IP sources autorisées.
- [ ] **Firebase** (notifications) : projet créé, `google-services.json` / `GoogleService-Info.plist`, compte de
      service pour `FIREBASE_CREDENTIALS`, capacité *Push Notifications* activée sur l'app iOS.
- [ ] **Jitsi Meet** auto-hébergé (`JITSI_URL`), avec l'authentification JWT (`JITSI_APP_ID`, `JITSI_APP_SECRET`).
- [ ] **E-mail** sortant (rappels aux professionnels, alertes de sauvegarde) : `MAIL_*`, `BACKUP_NOTIFY_EMAIL`.
- [ ] **Sentry** (facultatif) : `SENTRY_LARAVEL_DSN` côté API, `--dart-define=SENTRY_DSN=…` côté app.
- [ ] **IA** (facultatif) : `SUNOOGO_AI_ENABLED`, `ANTHROPIC_API_KEY`, et mention dans la politique de confidentialité.

## Exploitation

- [ ] **Sauvegardes** : disque hors serveur (`BACKUP_DISKS=backups,s3`), `BACKUP_ARCHIVE_PASSWORD` défini et conservé
      hors du serveur, **restauration testée** une fois.
- [ ] **HTTPS** devant nginx (certificat), `APP_URL` et `SESSION_SECURE_COOKIE=true`.
- [ ] `APP_DEBUG=false`, `APP_ENV=production`, clé `APP_KEY` générée et sauvegardée (sans elle, les messages chiffrés sont perdus).
- [ ] **Commission** de la plateforme sur les consultations (`SUNOOGO_COMMISSION_PERCENT`) et versement minimum décidés.

## Application mobile

- [ ] Build Android et iOS sur une machine de développement (`flutter build appbundle`, `flutter build ipa`),
      signature, fiches des stores.
- [ ] Test sur de vrais téléphones d'entrée de gamme (Android 8+), en 3G.
- [ ] Déclarations des stores : données de santé, messages vocaux (micro), notifications.

## Juridique

- [ ] Politique de confidentialité relue (données de santé, hébergement, sous-traitants : MoneyFusion, Firebase,
      Sentry, Anthropic si l'IA est activée).
- [ ] Conditions d'utilisation, et cadre d'intervention des écoutants (ce n'est pas un service médical d'urgence).
- [ ] Déclaration auprès de la Commission de l'informatique et des libertés (CIL) du Burkina Faso.
