import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/brand_mark.dart';
import '../../../emergency/presentation/widgets/emergency_sheet.dart';
import '../../../listening/presentation/controllers/listening_controller.dart';
import '../controllers/home_controller.dart';

class HomeTab extends GetView<HomeController> {
  const HomeTab({super.key});

  @override
  Widget build(BuildContext context) {
    final listening = Get.find<ListeningController>();

    return Scaffold(
      body: SafeArea(
        bottom: false,
        child: ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.md, AppSpacing.page, 120),
          children: [
            Row(children: [const BrandMark(size: 36), const Spacer(), const SosButton()]),
            const SizedBox(height: AppSpacing.xl),
            Text.rich(
              TextSpan(
                children: [
                  TextSpan(text: '${controller.greeting},\n', style: AppTextStyles.display),
                  TextSpan(text: controller.pseudonym, style: AppTextStyles.displayAccent),
                ],
              ),
            ),
            const SizedBox(height: AppSpacing.sm),
            const Text('Comment te sens-tu aujourd\'hui ? Ici, tu peux tout dire.', style: AppTextStyles.bodySecondary),
            const SizedBox(height: AppSpacing.xl),
            Obx(() {
              final current = listening.conversation.value;
              final active = current != null && !current.isClosed;
              return _HeroCard(
                title: active ? 'Reprendre la conversation' : 'Parler à un écoutant',
                subtitle: active ? 'Ton écoutant t\'attend.' : 'Anonyme, gratuit, sans jugement.',
                onTap: active ? listening.resume : () => controller.goTo(HomeController.listening),
              );
            }),
            const SizedBox(height: AppSpacing.lg),
            Row(
              children: [
                Expanded(
                  child: _QuickAction(
                    icon: Icons.medical_services_outlined,
                    title: 'Consulter',
                    subtitle: 'Psychologues et coachs',
                    onTap: () => controller.goTo(HomeController.professionals),
                  ),
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: _QuickAction(
                    icon: Icons.auto_stories_outlined,
                    title: 'Progresser',
                    subtitle: 'Parcours confiance en soi',
                    onTap: () => controller.goTo(HomeController.courses),
                  ),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.xl),
            const _Commitments(),
            const SizedBox(height: AppSpacing.lg),
            Material(
              color: AppColors.sosSoft,
              borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
              child: ListTile(
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
                onTap: () => EmergencySheet.show(alertSupervisor: false),
                leading: const Icon(Icons.health_and_safety_outlined, color: AppColors.sos),
                title: const Text('En danger maintenant ?', style: AppTextStyles.label),
                subtitle: const Text('Voir les numéros d\'urgence', style: AppTextStyles.caption),
                trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.sos),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _HeroCard extends StatelessWidget {
  const _HeroCard({required this.title, required this.subtitle, required this.onTap});

  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      borderRadius: BorderRadius.circular(AppSpacing.radiusLg),
      clipBehavior: Clip.antiAlias,
      child: Ink(
        decoration: const BoxDecoration(gradient: AppColors.brandGradient),
        child: InkWell(
          onTap: onTap,
          child: Stack(
            children: [
              Positioned(
                right: -30,
                top: -30,
                child: Container(
                  width: 140,
                  height: 140,
                  decoration: BoxDecoration(shape: BoxShape.circle, color: AppColors.white.withValues(alpha: 0.07)),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(AppSpacing.xl),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const BrandMark(size: 44, inverse: true),
                    const SizedBox(height: AppSpacing.xxl),
                    Text(title, style: AppTextStyles.title.copyWith(color: AppColors.white)),
                    const SizedBox(height: AppSpacing.xs),
                    Row(
                      children: [
                        Expanded(
                          child: Text(subtitle, style: AppTextStyles.body.copyWith(color: AppColors.lilac200)),
                        ),
                        Container(
                          padding: const EdgeInsets.all(AppSpacing.md),
                          decoration: const BoxDecoration(color: AppColors.white, shape: BoxShape.circle),
                          child: const Icon(Icons.arrow_forward_rounded, color: AppColors.violet800),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _QuickAction extends StatelessWidget {
  const _QuickAction({required this.icon, required this.title, required this.subtitle, required this.onTap});

  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(AppSpacing.sm + 2),
                decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusSm)),
                child: Icon(icon, color: AppColors.violet700),
              ),
              const SizedBox(height: AppSpacing.lg),
              Text(title, style: AppTextStyles.subtitle),
              const SizedBox(height: 2),
              Text(subtitle, style: AppTextStyles.caption),
            ],
          ),
        ),
      ),
    );
  }
}

class _Commitments extends StatelessWidget {
  const _Commitments();

  @override
  Widget build(BuildContext context) {
    const items = [('0 F', 'pour parler'), ('0', 'donnée demandée'), ('100 %', 'anonyme')];
    return Container(
      padding: const EdgeInsets.symmetric(vertical: AppSpacing.xl, horizontal: AppSpacing.md),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [AppColors.violet100, AppColors.white],
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
        ),
        borderRadius: BorderRadius.circular(AppSpacing.radiusLg),
        border: Border.all(color: AppColors.violet100),
      ),
      child: Row(
        children: [
          for (final (figure, label) in items)
            Expanded(
              child: Column(
                children: [
                  Text(figure, style: AppTextStyles.figure.copyWith(fontSize: 34)),
                  const SizedBox(height: AppSpacing.xs),
                  Text(label, style: AppTextStyles.caption, textAlign: TextAlign.center),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
