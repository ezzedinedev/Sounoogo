import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../emergency/presentation/widgets/emergency_sheet.dart';
import '../controllers/profile_controller.dart';
import '../../../../core/widgets/fade_slide_in.dart';

class ProfileTab extends GetView<ProfileController> {
  const ProfileTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Mon espace')),
      body: Obx(() {
        final user = controller.user.value;
        if (user == null) {
          return const SizedBox.shrink();
        }
        return ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, 120),
          children: FadeSlideIn.stagger([
            Container(
              padding: const EdgeInsets.all(AppSpacing.xl),
              decoration: BoxDecoration(gradient: AppColors.brandGradient, borderRadius: BorderRadius.circular(AppSpacing.radiusLg)),
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 28,
                    backgroundColor: AppColors.white.withValues(alpha: 0.15),
                    child: Text(user.pseudonym[0], style: AppTextStyles.title.copyWith(color: AppColors.white)),
                  ),
                  const SizedBox(width: AppSpacing.lg),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(user.pseudonym, style: AppTextStyles.title.copyWith(color: AppColors.white)),
                        Text(
                          user.isAnonymous ? 'Compte anonyme' : (user.email ?? user.phone ?? ''),
                          style: AppTextStyles.caption.copyWith(color: AppColors.lilac200),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            if (user.isAnonymous) ...[
              const SizedBox(height: AppSpacing.lg),
              Container(
                padding: const EdgeInsets.all(AppSpacing.lg),
                decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Sécurise ton espace', style: AppTextStyles.label),
                    const SizedBox(height: AppSpacing.xs),
                    const Text(
                      'Ajoute un e-mail ou un numéro pour ne jamais perdre l\'accès. Ton pseudonyme reste ta seule identité visible.',
                      style: AppTextStyles.caption,
                    ),
                    const SizedBox(height: AppSpacing.md),
                    OutlinedButton(
                      onPressed: () => Get.toNamed(AppRoutes.upgrade),
                      style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(44)),
                      child: const Text('Ajouter des identifiants'),
                    ),
                  ],
                ),
              ),
            ],
            const SizedBox(height: AppSpacing.xl),
            _Section(
              children: [
                if (user.preferredListener != null)
                  _Tile(
                    icon: Icons.favorite_border_rounded,
                    label: 'Écoutant préféré : ${user.preferredListener!.displayName}',
                    trailing: user.preferredListener!.isAvailable ? 'Disponible' : 'Absent',
                    onTap: _confirmForgetListener,
                  ),
                _Tile(icon: Icons.event_note_outlined, label: 'Mes consultations', onTap: () => Get.toNamed(AppRoutes.bookings)),
                _Tile(icon: Icons.insights_outlined, label: 'Mon journal d\'humeur', onTap: () => Get.toNamed(AppRoutes.mood)),
                _Tile(icon: Icons.shield_moon_outlined, label: 'Mon plan de sécurité', onTap: () => Get.toNamed(AppRoutes.safetyPlan)),
                _Tile(
                  icon: Icons.lock_outline_rounded,
                  label: 'Confidentialité et affichage',
                  onTap: () => Get.toNamed(AppRoutes.settings),
                ),
                _Tile(
                  icon: Icons.workspace_premium_outlined,
                  label: 'Abonnement',
                  trailing: user.subscription?.isActive ?? false ? user.subscription!.plan?.name : 'Gratuit',
                  onTap: () => Get.toNamed(AppRoutes.subscription),
                ),
                _Tile(icon: Icons.call_outlined, label: 'Numéros d\'urgence', onTap: () => EmergencySheet.show(alertSupervisor: false)),
              ],
            ),
            const SizedBox(height: AppSpacing.lg),
            _Section(
              children: [
                _Tile(icon: Icons.logout_rounded, label: 'Se déconnecter', onTap: controller.signOut),
                _Tile(icon: Icons.delete_outline_rounded, label: 'Supprimer mon compte', danger: true, onTap: _confirmDelete),
              ],
            ),
            const SizedBox(height: AppSpacing.xl),
            const Text('SUNOOGO ne remplace pas un service d\'urgence.', style: AppTextStyles.caption, textAlign: TextAlign.center),
          ]),
        );
      }),
    );
  }

  Future<void> _confirmForgetListener() async {
    final forget = await Get.dialog<bool>(
      AlertDialog(
        title: const Text('Ne plus demander cet écoutant ?'),
        content: const Text('Tes prochaines conversations seront confiées au premier écoutant disponible.'),
        actions: [
          TextButton(onPressed: () => Get.back(result: false), child: const Text('Garder')),
          TextButton(onPressed: () => Get.back(result: true), child: const Text('Ne plus demander')),
        ],
      ),
    );
    if (forget ?? false) {
      await controller.forgetPreferredListener();
    }
  }

  Future<void> _confirmDelete() async {
    final confirmed = await Get.dialog<bool>(
      AlertDialog(
        title: const Text('Supprimer ton compte ?'),
        content: const Text('Tes conversations, réservations et progression seront définitivement effacées.'),
        actions: [
          TextButton(onPressed: () => Get.back(result: false), child: const Text('Annuler')),
          TextButton(
            onPressed: () => Get.back(result: true),
            style: TextButton.styleFrom(foregroundColor: AppColors.sos),
            child: const Text('Supprimer'),
          ),
        ],
      ),
    );
    if (confirmed ?? false) {
      await controller.deleteAccount();
    }
  }
}

class _Section extends StatelessWidget {
  const _Section({required this.children});

  final List<Widget> children;

  @override
  Widget build(BuildContext context) => Card(
    clipBehavior: Clip.antiAlias,
    child: Column(
      children: [
        for (final (i, child) in children.indexed) ...[if (i > 0) const Divider(indent: 56), child],
      ],
    ),
  );
}

class _Tile extends StatelessWidget {
  const _Tile({required this.icon, required this.label, required this.onTap, this.trailing, this.danger = false});

  final IconData icon;
  final String label;
  final VoidCallback onTap;
  final String? trailing;
  final bool danger;

  @override
  Widget build(BuildContext context) {
    final color = danger ? AppColors.sos : AppColors.textPrimary;
    return ListTile(
      onTap: onTap,
      leading: Icon(icon, color: danger ? AppColors.sos : AppColors.violet700),
      title: Text(label, style: AppTextStyles.label.copyWith(color: color)),
      trailing: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (trailing != null) Text(trailing!, style: AppTextStyles.caption),
          const Icon(Icons.chevron_right_rounded, color: AppColors.textMuted),
        ],
      ),
    );
  }
}
