import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/display_settings.dart';
import '../../domain/entities/privacy_settings.dart';
import '../../../../core/widgets/app_toast.dart';
import '../../../notifications/presentation/services/notification_service.dart';
import '../controllers/settings_controller.dart';

/// Confidentialité (mode discret) et affichage.
class SettingsScreen extends GetView<SettingsController> {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Confidentialité et affichage')),
      body: Obx(() {
        final privacy = controller.privacy.value;
        final hasPin = controller.hasPin;

        return ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
          children: [
            const _Heading('Mode discret', 'Pour que personne d\'autre ne tombe sur tes échanges.'),
            _Group(
              children: [
                SwitchListTile(
                  value: privacy.lockEnabled,
                  title: const Text('Verrouiller avec un code', style: AppTextStyles.label),
                  subtitle: const Text('Demandé à chaque retour dans l\'app.', style: AppTextStyles.caption),
                  onChanged: (on) => on ? Get.toNamed(AppRoutes.settingsPin) : _confirmDisable(),
                ),
                if (privacy.lockEnabled) ...[
                  if (controller.biometricsAvailable.value)
                    SwitchListTile(
                      value: privacy.biometricEnabled,
                      title: const Text('Empreinte ou visage', style: AppTextStyles.label),
                      onChanged: (on) => controller.updatePrivacy(privacy.copyWith(biometricEnabled: on)),
                    ),
                  ListTile(
                    title: const Text('Verrouiller', style: AppTextStyles.label),
                    trailing: DropdownButton<LockDelay>(
                      value: privacy.lockDelay,
                      underline: const SizedBox.shrink(),
                      items: [for (final delay in LockDelay.values) DropdownMenuItem(value: delay, child: Text(delay.label))],
                      onChanged: (delay) => delay == null ? null : controller.updatePrivacy(privacy.copyWith(lockDelay: delay)),
                    ),
                  ),
                  ListTile(
                    title: const Text('Changer mon code', style: AppTextStyles.label),
                    trailing: const Icon(Icons.chevron_right_rounded),
                    onTap: () => Get.toNamed(AppRoutes.settingsPin),
                  ),
                ],
                SwitchListTile(
                  value: privacy.quickExitEnabled,
                  title: const Text('Sortie rapide', style: AppTextStyles.label),
                  subtitle: Text(
                    hasPin
                        ? 'Un bouton bascule instantanément sur une calculatrice. Pour revenir : ton code, puis « = ».'
                        : 'Crée d\'abord un code : il sert à quitter la calculatrice.',
                    style: AppTextStyles.caption,
                  ),
                  onChanged: hasPin ? (on) => controller.updatePrivacy(privacy.copyWith(quickExitEnabled: on)) : null,
                ),
                SwitchListTile(
                  value: privacy.hideInRecents,
                  title: const Text('Masquer dans les apps récentes', style: AppTextStyles.label),
                  subtitle: const Text(
                    'Le contenu n\'apparaît pas dans l\'aperçu et les captures d\'écran sont bloquées.',
                    style: AppTextStyles.caption,
                  ),
                  onChanged: (on) => controller.updatePrivacy(privacy.copyWith(hideInRecents: on)),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.xl),
            const _Heading('Rappels', 'Un petit rappel par jour pour faire ton check-in d\'humeur.'),
            const _ReminderGroup(),
            const SizedBox(height: AppSpacing.xl),
            const _Heading('Affichage', 'S\'ajoute à la taille de texte choisie sur le téléphone.'),
            _Group(
              children: [
                Padding(
                  padding: const EdgeInsets.all(AppSpacing.lg),
                  child: MediaQuery.withClampedTextScaling(
                    maxScaleFactor: 1.1,
                    child: SegmentedButton<TextSize>(
                      segments: [for (final size in TextSize.values) ButtonSegment(value: size, label: Text(size.label))],
                      selected: {controller.textSize.value},
                      showSelectedIcon: false,
                      onSelectionChanged: (selection) => controller.setTextSize(selection.first),
                    ),
                  ),
                ),
                const Padding(
                  padding: EdgeInsets.fromLTRB(AppSpacing.lg, 0, AppSpacing.lg, AppSpacing.lg),
                  child: Text('Aperçu : parle librement, quelqu\'un t\'écoute.', style: AppTextStyles.body),
                ),
              ],
            ),
          ],
        );
      }),
    );
  }

  Future<void> _confirmDisable() async {
    final confirmed = await Get.dialog<bool>(
      AlertDialog(
        title: const Text('Désactiver le verrou ?'),
        content: const Text('Ton code sera supprimé, ainsi que la sortie rapide.'),
        actions: [
          TextButton(onPressed: () => Get.back(result: false), child: const Text('Annuler')),
          TextButton(onPressed: () => Get.back(result: true), child: const Text('Désactiver')),
        ],
      ),
    );
    if (confirmed ?? false) {
      await controller.disableLock();
    }
  }
}

class _Heading extends StatelessWidget {
  const _Heading(this.title, this.subtitle);

  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title, style: AppTextStyles.subtitle),
          const SizedBox(height: 2),
          Text(subtitle, style: AppTextStyles.caption),
        ],
      ),
    );
  }
}

class _Group extends StatelessWidget {
  const _Group({required this.children});

  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
        border: Border.all(color: AppColors.border),
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(children: children),
    );
  }
}

class _ReminderGroup extends StatelessWidget {
  const _ReminderGroup();

  @override
  Widget build(BuildContext context) {
    final notifications = Get.find<NotificationService>();

    Future<void> update(bool enabled, TimeOfDay time) async {
      final saved = await notifications.updateReminder(
        notifications.reminder.value.copyWith(enabled: enabled, hour: time.hour, minute: time.minute),
      );
      if (!saved) {
        AppToast.show('Notifications désactivées', 'Autorise les notifications de SUNOOGO dans les réglages du téléphone.');
      }
    }

    return Obx(() {
      final reminder = notifications.reminder.value;
      final time = TimeOfDay(hour: reminder.hour, minute: reminder.minute);
      return _Group(
        children: [
          SwitchListTile(
            value: reminder.enabled,
            title: const Text('Rappel quotidien', style: AppTextStyles.label),
            subtitle: const Text('Le texte reste discret sur l\'écran verrouillé.', style: AppTextStyles.caption),
            onChanged: (on) => update(on, time),
          ),
          if (reminder.enabled)
            ListTile(
              title: const Text('Heure', style: AppTextStyles.label),
              trailing: Text(time.format(context), style: AppTextStyles.label.copyWith(color: AppColors.violet700)),
              onTap: () async {
                final picked = await showTimePicker(context: context, initialTime: time, helpText: 'Heure du rappel');
                if (picked != null) {
                  await update(true, picked);
                }
              },
            ),
        ],
      );
    });
  }
}
