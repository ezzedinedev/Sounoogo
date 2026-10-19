import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/widgets/accent_title.dart';
import '../../../core/widgets/emergency_sheet.dart';
import '../controllers/listening_controller.dart';
import '../models/topic_model.dart';

/// Onglet « Écoute » : reprendre la conversation en cours ou en ouvrir une.
class ListeningTab extends GetView<ListeningController> {
  const ListeningTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Écoute'), actions: const [SosButton(), SizedBox(width: AppSpacing.page)]),
      body: Obx(() {
        final current = controller.conversation.value;

        return ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
          children: [
            if (current != null && !current.isClosed) ...[
              _ResumeCard(waiting: current.isWaiting, listener: current.listenerName, onTap: controller.resume),
              const SizedBox(height: AppSpacing.xl),
            ],
            const AccentTitle('De quoi as-tu envie', accent: 'de parler ?', large: true),
            const SizedBox(height: AppSpacing.md),
            const Text('Choisis un thème si tu veux, ce n\'est pas obligatoire. Tu pourras tout dire ensuite.', style: AppTextStyles.bodySecondary),
            const SizedBox(height: AppSpacing.xl),
            Wrap(
              spacing: AppSpacing.sm,
              runSpacing: AppSpacing.sm,
              children: [
                for (final topic in TopicModel.all)
                  ChoiceChip(
                    label: Text(topic.label),
                    selected: controller.selectedTopic.value == topic.value,
                    showCheckmark: false,
                    labelStyle: AppTextStyles.label.copyWith(
                      color: controller.selectedTopic.value == topic.value ? AppColors.white : AppColors.textPrimary,
                    ),
                    onSelected: (selected) => controller.selectedTopic.value = selected ? topic.value : null,
                  ),
              ],
            ),
            const SizedBox(height: AppSpacing.xxl),
            if (controller.errorMessage.value != null) ...[
              Text(controller.errorMessage.value!, style: AppTextStyles.caption.copyWith(color: AppColors.sos)),
              const SizedBox(height: AppSpacing.md),
            ],
            FilledButton.icon(
              onPressed: controller.isLoading.value || (current != null && !current.isClosed) ? null : controller.start,
              icon: const Icon(Icons.chat_bubble_outline_rounded),
              label: const Text('Parler à un écoutant'),
            ),
            const SizedBox(height: AppSpacing.xxl),
            const _HowItWorks(),
          ],
        );
      }),
    );
  }
}

class _ResumeCard extends StatelessWidget {
  const _ResumeCard({required this.waiting, required this.onTap, this.listener});

  final bool waiting;
  final String? listener;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.violet700,
      borderRadius: BorderRadius.circular(AppSpacing.radiusLg),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(AppSpacing.radiusLg),
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.xl),
          child: Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(waiting ? 'Un écoutant arrive' : 'Conversation en cours', style: AppTextStyles.subtitle.copyWith(color: AppColors.white)),
                    const SizedBox(height: AppSpacing.xs),
                    Text(
                      waiting ? 'Tu peux déjà écrire, il lira tout.' : 'Avec $listener',
                      style: AppTextStyles.body.copyWith(color: AppColors.white.withValues(alpha: 0.8)),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.all(AppSpacing.md),
                decoration: const BoxDecoration(color: AppColors.white, shape: BoxShape.circle),
                child: const Icon(Icons.arrow_forward_rounded, color: AppColors.violet800),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _HowItWorks extends StatelessWidget {
  const _HowItWorks();

  @override
  Widget build(BuildContext context) {
    const steps = [
      (Icons.visibility_off_outlined, 'Anonyme', 'L\'écoutant ne voit que ton pseudonyme.'),
      (Icons.school_outlined, 'Écoutants formés', 'Ils t\'écoutent sans juger et sans diagnostiquer.'),
      (Icons.shield_outlined, 'Toujours protégé', 'Si tu es en danger, un superviseur est prévenu.'),
    ];

    return Container(
      padding: const EdgeInsets.all(AppSpacing.xl),
      decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusLg)),
      child: Column(
        children: [
          for (final (icon, title, text) in steps)
            Padding(
              padding: const EdgeInsets.symmetric(vertical: AppSpacing.sm),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(icon, color: AppColors.violet700, size: 22),
                  const SizedBox(width: AppSpacing.md),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(title, style: AppTextStyles.label),
                        Text(text, style: AppTextStyles.caption),
                      ],
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
