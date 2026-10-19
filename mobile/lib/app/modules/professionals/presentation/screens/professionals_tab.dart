import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../core/widgets/accent_title.dart';
import '../../../../core/widgets/state_views.dart';
import '../controllers/professionals_controller.dart';
import '../../domain/entities/professional.dart';
import '../widgets/professional_avatar.dart';
import '../../../../core/widgets/fade_slide_in.dart';
import '../../../../core/widgets/scale_on_press.dart';

class ProfessionalsTab extends GetView<ProfessionalsController> {
  const ProfessionalsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Consultations'),
        actions: [
          IconButton(
            tooltip: 'Mes consultations',
            onPressed: () => Get.toNamed(AppRoutes.bookings),
            icon: const Icon(Icons.event_note_outlined),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: controller.load,
        child: Obx(() {
          return CustomScrollView(
            slivers: [
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.lg),
                sliver: SliverList.list(
                  children: [
                    const AccentTitle('Un professionnel', accent: 'qui te correspond', large: true),
                    const SizedBox(height: AppSpacing.md),
                    const Text(
                      'Tous vérifiés par notre équipe. Tu peux rester anonyme jusqu\'à la séance.',
                      style: AppTextStyles.bodySecondary,
                    ),
                    const SizedBox(height: AppSpacing.lg),
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(
                        children: [
                          _FilterChip(label: 'Tous', selected: controller.mode.value == null, onTap: () => controller.filterBy(null)),
                          for (final entry in controller.modes.entries)
                            _FilterChip(
                              label: entry.value,
                              selected: controller.mode.value == entry.key,
                              onTap: () => controller.filterBy(entry.key),
                            ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              if (controller.isLoading.value && controller.items.isEmpty)
                const SliverFillRemaining(child: SkeletonList(count: 3, itemHeight: 150))
              else if (controller.errorMessage.value != null)
                SliverFillRemaining(
                  child: ErrorState(message: controller.errorMessage.value!, onRetry: controller.load),
                )
              else if (controller.items.isEmpty)
                const SliverFillRemaining(
                  child: EmptyState(
                    icon: Icons.person_search_outlined,
                    title: 'Aucun professionnel',
                    message: 'Essaie un autre format de consultation.',
                  ),
                )
              else
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, AppSpacing.xxl),
                  sliver: SliverList.separated(
                    itemCount: controller.items.length,
                    separatorBuilder: (_, _) => const SizedBox(height: AppSpacing.md),
                    itemBuilder: (_, index) => FadeSlideIn(index: index, child: _ProfessionalCard(controller.items[index])),
                  ),
                ),
            ],
          );
        }),
      ),
    );
  }
}

class _FilterChip extends StatelessWidget {
  const _FilterChip({required this.label, required this.selected, required this.onTap});

  final String label;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(right: AppSpacing.sm),
      child: ChoiceChip(
        label: Text(label),
        selected: selected,
        showCheckmark: false,
        labelStyle: AppTextStyles.label.copyWith(color: selected ? AppColors.white : AppColors.textPrimary),
        onSelected: (_) => onTap(),
      ),
    );
  }
}

class _ProfessionalCard extends StatelessWidget {
  const _ProfessionalCard(this.professional);

  final Professional professional;

  @override
  Widget build(BuildContext context) {
    return ScaleOnPress(
      child: Card(
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: () => Get.toNamed(AppRoutes.professional(professional.id)),
          child: Padding(
            padding: const EdgeInsets.all(AppSpacing.lg),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    ProfessionalAvatar(initials: professional.initials, heroTag: 'pro-${professional.id}'),
                    const SizedBox(width: AppSpacing.md),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(professional.name, style: AppTextStyles.subtitle),
                          Text(professional.title, style: AppTextStyles.caption),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: AppSpacing.md),
                Text(professional.specialty, style: AppTextStyles.bodySecondary),
                const SizedBox(height: AppSpacing.md),
                Wrap(
                  spacing: AppSpacing.xs + 2,
                  runSpacing: AppSpacing.xs + 2,
                  children: [
                    if (professional.city != null) _Tag(icon: Icons.place_outlined, label: professional.city!),
                    for (final mode in professional.modes) _Tag(label: mode.label),
                  ],
                ),
                const Divider(height: AppSpacing.xl),
                Row(
                  children: [
                    Text(Formatters.money(professional.sessionPrice), style: AppTextStyles.figure.copyWith(fontSize: 26)),
                    Text('  / ${professional.sessionMinutes} min', style: AppTextStyles.caption),
                    const Spacer(),
                    const Icon(Icons.arrow_forward_rounded, color: AppColors.violet700),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _Tag extends StatelessWidget {
  const _Tag({required this.label, this.icon});

  final String label;
  final IconData? icon;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm + 2, vertical: AppSpacing.xs),
      decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusPill)),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (icon != null) ...[Icon(icon, size: 14, color: AppColors.violet700), const SizedBox(width: 2)],
          Text(
            label,
            style: AppTextStyles.caption.copyWith(color: AppColors.violet800, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }
}
