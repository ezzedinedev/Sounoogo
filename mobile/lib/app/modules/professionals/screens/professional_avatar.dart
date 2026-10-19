import 'package:flutter/material.dart';

import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_text_styles.dart';

class ProfessionalAvatar extends StatelessWidget {
  const ProfessionalAvatar({super.key, required this.initials, this.size = 52});

  final String initials;
  final double size;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      alignment: Alignment.center,
      decoration: const BoxDecoration(color: AppColors.violet100, shape: BoxShape.circle),
      child: Text(initials, style: AppTextStyles.label.copyWith(color: AppColors.violet800, fontSize: size * 0.32)),
    );
  }
}
