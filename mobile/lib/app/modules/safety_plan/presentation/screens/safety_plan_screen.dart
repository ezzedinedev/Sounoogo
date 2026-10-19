import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/accent_title.dart';
import '../../../../core/widgets/fade_slide_in.dart';
import '../../../../core/widgets/state_views.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../domain/entities/safety_plan.dart';
import '../controllers/safety_plan_controller.dart';
import '../widgets/safety_plan_sections.dart';

/// Lecture du plan de sécurité, pensée pour un moment de crise : étapes claires, appel en un geste.
class SafetyPlanScreen extends GetView<SafetyPlanController> {
  const SafetyPlanScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Mon plan de sécurité'),
        actions: [
          Obx(
            () => controller.plan.value == null
                ? const SizedBox.shrink()
                : IconButton(
                    tooltip: 'Modifier',
                    onPressed: () => Get.toNamed(AppRoutes.safetyPlanEdit),
                    icon: const Icon(Icons.edit_outlined),
                  ),
          ),
        ],
      ),
      body: Obx(() {
        final plan = controller.plan.value;
        if (!controller.isLoaded.value) {
          return controller.errorMessage.value != null
              ? ErrorState(message: controller.errorMessage.value!, onRetry: controller.load)
              : const SkeletonList();
        }
        return plan == null || plan.isEmpty ? const _Intro() : _Plan(plan);
      }),
    );
  }
}

class _Intro extends StatelessWidget {
  const _Intro();

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.all(AppSpacing.page),
      children: [
        const AccentTitle('Un plan pour les', accent: 'moments difficiles', large: true),
        const SizedBox(height: AppSpacing.md),
        const Text(
          'Quand ça va mal, il est dur de réfléchir. Prépare maintenant, au calme, ce qui pourra t\'aider : '
          'tes signaux d\'alerte, ce qui t\'apaise, les personnes à appeler. Ton plan reste privé et disponible même sans connexion.',
          style: AppTextStyles.bodySecondary,
        ),
        const SizedBox(height: AppSpacing.xl),
        for (final section in SafetyPlanSection.values)
          FadeSlideIn(
            index: section.step,
            child: ListTile(
              contentPadding: EdgeInsets.zero,
              leading: _StepBadge(section.step),
              title: Text(section.title, style: AppTextStyles.label),
            ),
          ),
        const SizedBox(height: AppSpacing.xl),
        FilledButton(onPressed: () => Get.toNamed(AppRoutes.safetyPlanEdit), child: const Text('Créer mon plan')),
        const SizedBox(height: AppSpacing.md),
        const Text(
          'Inspiré du plan de sécurité de Stanley et Brown, utilisé par les professionnels de la prévention du suicide.',
          style: AppTextStyles.caption,
          textAlign: TextAlign.center,
        ),
      ],
    );
  }
}

class _Plan extends StatelessWidget {
  const _Plan(this.plan);

  final SafetyPlan plan;

  @override
  Widget build(BuildContext context) {
    final emergency = Get.find<SessionService>().emergencyContacts;

    return ListView(
      padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
      children: [
        const Text('Suis les étapes une à une, à ton rythme.', style: AppTextStyles.bodySecondary),
        const SizedBox(height: AppSpacing.lg),
        for (final section in SafetyPlanSection.values)
          if (section.isContacts
              ? section.contactsOf(plan).isNotEmpty || section == SafetyPlanSection.professionalContacts
              : section.textsOf(plan).isNotEmpty)
            FadeSlideIn(
              index: section.step,
              child: _SectionCard(
                section: section,
                children: [
                  for (final text in section.textsOf(plan)) _Bullet(text),
                  for (final contact in section.contactsOf(plan)) _CallTile(name: contact.name, phone: contact.phone),
                  if (section == SafetyPlanSection.professionalContacts)
                    for (final contact in emergency) _CallTile(name: contact.label, phone: contact.phone, urgent: true),
                ],
              ),
            ),
      ],
    );
  }
}

class _SectionCard extends StatelessWidget {
  const _SectionCard({required this.section, required this.children});

  final SafetyPlanSection section;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.md),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  _StepBadge(section.step),
                  const SizedBox(width: AppSpacing.md),
                  Expanded(child: Text(section.title, style: AppTextStyles.subtitle)),
                ],
              ),
              const SizedBox(height: AppSpacing.md),
              ...children,
            ],
          ),
        ),
      ),
    );
  }
}

class _StepBadge extends StatelessWidget {
  const _StepBadge(this.step);

  final int step;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 32,
      height: 32,
      alignment: Alignment.center,
      decoration: const BoxDecoration(gradient: AppColors.brandGradient, shape: BoxShape.circle),
      child: Text('$step', style: AppTextStyles.label.copyWith(color: AppColors.white)),
    );
  }
}

class _Bullet extends StatelessWidget {
  const _Bullet(this.text);

  final String text;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.sm),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.only(top: 8),
            child: CircleAvatar(radius: 3, backgroundColor: AppColors.violet500),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(child: Text(text, style: AppTextStyles.body)),
        ],
      ),
    );
  }
}

class _CallTile extends StatelessWidget {
  const _CallTile({required this.name, required this.phone, this.urgent = false});

  final String name;
  final String phone;
  final bool urgent;

  @override
  Widget build(BuildContext context) {
    final color = urgent ? AppColors.sos : AppColors.violet700;

    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.sm),
      child: Material(
        color: urgent ? AppColors.sosSoft : AppColors.violet50,
        borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
        child: InkWell(
          borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
          onTap: () => launchUrl(Uri(scheme: 'tel', path: phone)),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg, vertical: AppSpacing.md),
            child: Row(
              children: [
                Expanded(child: Text(name, style: AppTextStyles.label)),
                Text(phone, style: AppTextStyles.label.copyWith(color: color)),
                const SizedBox(width: AppSpacing.sm),
                Icon(Icons.call_rounded, color: color, size: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
