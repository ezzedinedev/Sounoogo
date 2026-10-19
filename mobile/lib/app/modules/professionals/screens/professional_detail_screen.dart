import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/utils/formatters.dart';
import '../../../core/widgets/state_views.dart';
import '../controllers/professional_detail_controller.dart';
import 'professional_avatar.dart';

class ProfessionalDetailScreen extends GetView<ProfessionalDetailController> {
  const ProfessionalDetailScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
      body: Obx(() {
        final pro = controller.professional.value;
        if (pro == null) {
          return controller.errorMessage.value != null
              ? ErrorState(message: controller.errorMessage.value!, onRetry: controller.load)
              : const SkeletonList(count: 3);
        }

        return ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, 120),
          children: [
            Row(
              children: [
                ProfessionalAvatar(initials: pro.initials, size: 72),
                const SizedBox(width: AppSpacing.lg),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(pro.name, style: AppTextStyles.title),
                      Text(pro.title, style: AppTextStyles.bodySecondary),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.xl),
            Container(
              padding: const EdgeInsets.all(AppSpacing.lg),
              decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
              child: Row(
                children: [
                  _Fact(label: 'Séance', value: Formatters.money(pro.sessionPrice)),
                  _Fact(label: 'Durée', value: '${pro.sessionMinutes} min'),
                  if (pro.city != null) _Fact(label: 'Ville', value: pro.city!),
                ],
              ),
            ),
            if (pro.bio != null) ...[
              const SizedBox(height: AppSpacing.xl),
              Text(pro.bio!, style: AppTextStyles.body),
            ],
            const SizedBox(height: AppSpacing.md),
            Text('Spécialités : ${pro.specialty}', style: AppTextStyles.bodySecondary),
            if (pro.languages.isNotEmpty) Text('Langues : ${pro.languages.join(', ')}', style: AppTextStyles.bodySecondary),
            const SizedBox(height: AppSpacing.xl),
            const Text('Format', style: AppTextStyles.subtitle),
            const SizedBox(height: AppSpacing.md),
            Wrap(
              spacing: AppSpacing.sm,
              children: [
                for (final mode in pro.modes)
                  ChoiceChip(
                    label: Text(mode.label),
                    selected: controller.selectedMode.value == mode.value,
                    showCheckmark: false,
                    labelStyle: AppTextStyles.label.copyWith(color: controller.selectedMode.value == mode.value ? AppColors.white : AppColors.textPrimary),
                    onSelected: (_) => controller.selectedMode.value = mode.value,
                  ),
              ],
            ),
            const SizedBox(height: AppSpacing.xl),
            const Text('Choisis un créneau', style: AppTextStyles.subtitle),
            const SizedBox(height: AppSpacing.md),
            if (pro.slots.isEmpty)
              const Text('Aucun créneau disponible pour le moment.', style: AppTextStyles.bodySecondary)
            else
              for (final entry in controller.slotsByDay.entries) ...[
                Text(entry.key[0].toUpperCase() + entry.key.substring(1), style: AppTextStyles.label),
                const SizedBox(height: AppSpacing.sm),
                Wrap(
                  spacing: AppSpacing.sm,
                  runSpacing: AppSpacing.sm,
                  children: [
                    for (final slot in entry.value)
                      ChoiceChip(
                        label: Text(Formatters.time(slot.startsAt)),
                        selected: controller.selectedSlot.value?.id == slot.id,
                        showCheckmark: false,
                        labelStyle: AppTextStyles.label.copyWith(color: controller.selectedSlot.value?.id == slot.id ? AppColors.white : AppColors.textPrimary),
                        onSelected: (_) => controller.selectedSlot.value = slot,
                      ),
                  ],
                ),
                const SizedBox(height: AppSpacing.lg),
              ],
            SwitchListTile(
              value: controller.keepAnonymous.value,
              onChanged: (value) => controller.keepAnonymous.value = value,
              contentPadding: EdgeInsets.zero,
              activeThumbColor: AppColors.violet700,
              title: const Text('Rester anonyme', style: AppTextStyles.label),
              subtitle: const Text('Le professionnel ne verra que ton pseudonyme.', style: AppTextStyles.caption),
            ),
          ],
        );
      }),
      bottomNavigationBar: Obx(() {
        final ready = controller.selectedSlot.value != null && controller.selectedMode.value != null;
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.md),
            child: FilledButton(
              onPressed: ready && !controller.isBooking.value ? controller.bookAndPay : null,
              child: Text(ready ? 'Réserver le ${Formatters.dayTime(controller.selectedSlot.value!.startsAt)}' : 'Choisis un créneau'),
            ),
          ),
        );
      }),
    );
  }
}

class _Fact extends StatelessWidget {
  const _Fact({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: AppTextStyles.caption),
          const SizedBox(height: 2),
          Text(value, style: AppTextStyles.label),
        ],
      ),
    );
  }
}
