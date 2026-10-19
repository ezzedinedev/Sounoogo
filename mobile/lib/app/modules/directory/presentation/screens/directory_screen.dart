import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/fade_slide_in.dart';
import '../../../../core/widgets/state_views.dart';
import '../../../emergency/presentation/widgets/emergency_sheet.dart';
import '../controllers/directory_controller.dart';
import '../widgets/resource_card.dart';
import '../widgets/resource_map.dart';

/// Annuaire des ressources de proximité, en liste ou sur une carte.
class DirectoryScreen extends GetView<DirectoryController> {
  const DirectoryScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Près de toi'),
        actions: [
          Obx(
            () => IconButton(
              tooltip: controller.showMap.value ? 'Afficher la liste' : 'Afficher la carte',
              onPressed: () => controller.showMap.toggle(),
              icon: AnimatedSwitcher(
                duration: const Duration(milliseconds: 200),
                child: Icon(
                  controller.showMap.value ? Icons.view_list_rounded : Icons.map_outlined,
                  key: ValueKey(controller.showMap.value),
                ),
              ),
            ),
          ),
          const SizedBox(width: AppSpacing.sm),
        ],
      ),
      body: Obx(() {
        final directory = controller.directory.value;
        if (directory == null) {
          return controller.errorMessage.value != null
              ? ErrorState(message: controller.errorMessage.value!, onRetry: controller.load)
              : const SkeletonList();
        }

        final filters = Padding(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, 0, AppSpacing.sm),
          child: SizedBox(
            height: 40,
            child: ListView(
              scrollDirection: Axis.horizontal,
              children: [
                _Chip(label: 'Toutes les villes', selected: controller.city.value == null, onTap: () => controller.filterCity(null)),
                for (final city in directory.cities)
                  _Chip(label: city, selected: controller.city.value == city, onTap: () => controller.filterCity(city)),
                const SizedBox(width: AppSpacing.md),
                for (final category in directory.categories)
                  _Chip(
                    label: category.label,
                    selected: controller.category.value == category.value,
                    onTap: () => controller.filterCategory(controller.category.value == category.value ? null : category.value),
                    outlined: true,
                  ),
                const SizedBox(width: AppSpacing.page),
              ],
            ),
          ),
        );

        return Column(
          children: [
            filters,
            Expanded(
              child: AnimatedSwitcher(
                duration: const Duration(milliseconds: 300),
                child: controller.showMap.value
                    ? Stack(
                        key: const ValueKey('map'),
                        children: [
                          ResourceMap(
                            resources: directory.resources,
                            selected: controller.selected.value,
                            onSelect: (r) => controller.selected.value = r,
                          ),
                          Positioned(
                            left: AppSpacing.md,
                            right: AppSpacing.md,
                            bottom: AppSpacing.lg,
                            child: AnimatedSwitcher(
                              duration: const Duration(milliseconds: 260),
                              transitionBuilder: (child, animation) => SlideTransition(
                                position: Tween(begin: const Offset(0, 0.3), end: Offset.zero).animate(animation),
                                child: FadeTransition(opacity: animation, child: child),
                              ),
                              child: controller.selected.value == null
                                  ? const SizedBox.shrink()
                                  : ResourceCard(
                                      key: ValueKey(controller.selected.value!.id),
                                      resource: controller.selected.value!,
                                      elevated: true,
                                    ),
                            ),
                          ),
                        ],
                      )
                    : RefreshIndicator(
                        key: const ValueKey('list'),
                        onRefresh: controller.load,
                        child: ListView(
                          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
                          children: [
                            if (directory.resources.isEmpty)
                              const EmptyState(
                                icon: Icons.travel_explore_rounded,
                                title: 'Pas encore de ressource ici',
                                message: 'L\'équipe complète l\'annuaire ville par ville, en vérifiant chaque structure.',
                              )
                            else
                              for (final (index, resource) in directory.resources.indexed)
                                FadeSlideIn(
                                  index: index,
                                  child: Padding(
                                    padding: const EdgeInsets.only(bottom: AppSpacing.md),
                                    child: ResourceCard(resource: resource),
                                  ),
                                ),
                            const SizedBox(height: AppSpacing.md),
                            Material(
                              color: AppColors.sosSoft,
                              borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
                              child: ListTile(
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
                                onTap: () => EmergencySheet.show(alertSupervisor: false),
                                leading: const Icon(Icons.health_and_safety_outlined, color: AppColors.sos),
                                title: const Text('En danger maintenant ?', style: AppTextStyles.label),
                                subtitle: const Text('Les numéros d\'urgence, sans attendre', style: AppTextStyles.caption),
                                trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.sos),
                              ),
                            ),
                          ],
                        ),
                      ),
              ),
            ),
          ],
        );
      }),
    );
  }
}

class _Chip extends StatelessWidget {
  const _Chip({required this.label, required this.selected, required this.onTap, this.outlined = false});

  final String label;
  final bool selected;
  final VoidCallback onTap;
  final bool outlined;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(right: AppSpacing.sm),
      child: outlined
          ? FilterChip(label: Text(label), selected: selected, onSelected: (_) => onTap())
          : ChoiceChip(
              label: Text(label),
              selected: selected,
              showCheckmark: false,
              labelStyle: AppTextStyles.label.copyWith(color: selected ? AppColors.white : AppColors.textPrimary),
              onSelected: (_) => onTap(),
            ),
    );
  }
}
