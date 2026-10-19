import 'package:flutter/material.dart';

import '../../../core/theme/app_colors.dart';

/// Couverture d'une formation, avec repli élégant si l'image ne charge pas.
class CourseCover extends StatelessWidget {
  const CourseCover({super.key, this.url, this.height = 150});

  final String? url;
  final double height;

  @override
  Widget build(BuildContext context) {
    const fallback = DecoratedBox(
      decoration: BoxDecoration(gradient: AppColors.brandGradient),
      child: Center(child: Icon(Icons.auto_stories_outlined, color: AppColors.white, size: 36)),
    );

    return SizedBox(
      height: height,
      width: double.infinity,
      child: url == null ? fallback : Image.network(url!, fit: BoxFit.cover, errorBuilder: (_, _, _) => fallback),
    );
  }
}
