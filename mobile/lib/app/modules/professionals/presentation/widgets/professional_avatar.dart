import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_text_styles.dart';

class ProfessionalAvatar extends StatelessWidget {
  const ProfessionalAvatar({super.key, required this.initials, this.size = 52, this.heroTag});

  final String initials;
  final double size;

  /// Lien visuel entre la liste et la fiche : l'avatar glisse d'un écran à l'autre.
  final Object? heroTag;

  @override
  Widget build(BuildContext context) {
    final avatar = Container(
      width: size,
      height: size,
      alignment: Alignment.center,
      decoration: const BoxDecoration(color: AppColors.violet100, shape: BoxShape.circle),
      child: Text(
        initials,
        style: AppTextStyles.label.copyWith(color: AppColors.violet800, fontSize: size * 0.32),
      ),
    );
    return heroTag == null ? avatar : Hero(tag: heroTag!, child: avatar);
  }
}
