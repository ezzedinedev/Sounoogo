import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';

/// Couverture d'une formation, avec repli élégant si l'image ne charge pas.
class CourseCover extends StatelessWidget {
  const CourseCover({super.key, this.url, this.height = 150, this.heroTag});

  final String? url;
  final double height;

  /// La couverture s'agrandit de la carte vers l'en-tête de la fiche.
  final Object? heroTag;

  @override
  Widget build(BuildContext context) {
    const fallback = DecoratedBox(
      decoration: BoxDecoration(gradient: AppColors.brandGradient),
      child: Center(child: Icon(Icons.auto_stories_outlined, color: AppColors.white, size: 36)),
    );

    final cover = url == null
        ? fallback
        : Image.network(
            url!,
            fit: BoxFit.cover,
            errorBuilder: (_, _, _) => fallback,
            // Fondu à l'arrivée de l'image plutôt qu'une apparition brutale.
            frameBuilder: (_, child, frame, loadedSync) => loadedSync
                ? child
                : AnimatedOpacity(opacity: frame == null ? 0 : 1, duration: const Duration(milliseconds: 400), child: child),
          );

    return SizedBox(
      height: height,
      width: double.infinity,
      child: heroTag == null ? cover : Hero(tag: heroTag!, child: cover),
    );
  }
}
