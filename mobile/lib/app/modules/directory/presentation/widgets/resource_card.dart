import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/support_resource.dart';

class ResourceCard extends StatelessWidget {
  const ResourceCard({super.key, required this.resource, this.elevated = false});

  final SupportResource resource;

  /// Sur la carte, la fiche flotte au-dessus du fond.
  final bool elevated;

  Future<void> _directions() => launchUrl(
    Uri.https('www.google.com', '/maps/dir/', {'api': '1', 'destination': '${resource.latitude},${resource.longitude}'}),
    mode: LaunchMode.externalApplication,
  );

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: elevated ? 8 : null,
      shadowColor: AppColors.violet950.withValues(alpha: 0.25),
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    resource.categoryLabel.toUpperCase(),
                    style: AppTextStyles.caption.copyWith(color: AppColors.violet700, fontWeight: FontWeight.w600, letterSpacing: 0.6),
                  ),
                ),
                if (resource.isFree)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm, vertical: 2),
                    decoration: BoxDecoration(color: AppColors.successSoft, borderRadius: BorderRadius.circular(AppSpacing.radiusPill)),
                    child: Text(
                      'Gratuit',
                      style: AppTextStyles.caption.copyWith(color: AppColors.success, fontWeight: FontWeight.w600),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: AppSpacing.xs),
            Text(resource.name, style: AppTextStyles.subtitle),
            if (resource.description != null) ...[
              const SizedBox(height: AppSpacing.xs),
              Text(resource.description!, style: AppTextStyles.bodySecondary),
            ],
            const SizedBox(height: AppSpacing.sm),
            _Line(icon: Icons.place_outlined, text: [resource.address, resource.city].whereType<String>().join(', ')),
            if (resource.openingHours != null) _Line(icon: Icons.schedule_rounded, text: resource.openingHours!),
            const SizedBox(height: AppSpacing.md),
            Wrap(
              spacing: AppSpacing.sm,
              runSpacing: AppSpacing.sm,
              children: [
                if (resource.phone != null)
                  FilledButton.icon(
                    onPressed: () => launchUrl(Uri(scheme: 'tel', path: resource.phone)),
                    icon: const Icon(Icons.call_rounded, size: 18),
                    label: Text(resource.phone!),
                    style: FilledButton.styleFrom(minimumSize: const Size(0, 42)),
                  ),
                if (resource.isOnMap)
                  OutlinedButton.icon(
                    onPressed: _directions,
                    icon: const Icon(Icons.directions_rounded, size: 18),
                    label: const Text('Itinéraire'),
                    style: OutlinedButton.styleFrom(minimumSize: const Size(0, 42)),
                  ),
                if (resource.website != null)
                  TextButton.icon(
                    onPressed: () => launchUrl(Uri.parse(resource.website!), mode: LaunchMode.externalApplication),
                    icon: const Icon(Icons.open_in_new_rounded, size: 18),
                    label: const Text('Site'),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _Line extends StatelessWidget {
  const _Line({required this.icon, required this.text});

  final IconData icon;
  final String text;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: AppSpacing.xs),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: AppColors.textSecondary),
          const SizedBox(width: AppSpacing.xs + 2),
          Expanded(child: Text(text, style: AppTextStyles.caption)),
        ],
      ),
    );
  }
}
