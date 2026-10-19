import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';

/// Indicateur de saisie du code : un point par chiffre.
class PinDots extends StatelessWidget {
  const PinDots({super.key, required this.length, this.filled = 0, this.error = false, this.color = AppColors.violet700});

  final int length;
  final int filled;
  final bool error;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Semantics(
      label: '$filled chiffre${filled > 1 ? 's' : ''} saisi${filled > 1 ? 's' : ''}',
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          for (var i = 0; i < length; i++)
            AnimatedContainer(
              duration: const Duration(milliseconds: 150),
              margin: const EdgeInsets.symmetric(horizontal: AppSpacing.sm),
              width: 14,
              height: 14,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: i < filled ? (error ? AppColors.sos : color) : Colors.transparent,
                border: Border.all(color: error ? AppColors.sos : color, width: 2),
              ),
            ),
        ],
      ),
    );
  }
}

/// Pavé numérique à grandes touches (cibles de 72 px).
class PinPad extends StatelessWidget {
  const PinPad({super.key, required this.onDigit, required this.onDelete, this.extra, this.foreground = AppColors.textPrimary});

  final ValueChanged<String> onDigit;
  final VoidCallback onDelete;

  /// Touche en bas à gauche (empreinte, par exemple).
  final Widget? extra;
  final Color foreground;

  @override
  Widget build(BuildContext context) {
    Widget key(String digit) => _Key(
      label: digit,
      foreground: foreground,
      onTap: () {
        HapticFeedback.selectionClick();
        onDigit(digit);
      },
    );

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        for (final row in const [
          ['1', '2', '3'],
          ['4', '5', '6'],
          ['7', '8', '9'],
        ])
          Row(mainAxisAlignment: MainAxisAlignment.spaceEvenly, children: [for (final digit in row) key(digit)]),
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          children: [
            SizedBox.square(dimension: 72, child: extra),
            key('0'),
            SizedBox.square(
              dimension: 72,
              child: IconButton(
                tooltip: 'Effacer',
                onPressed: onDelete,
                icon: Icon(Icons.backspace_outlined, color: foreground),
              ),
            ),
          ],
        ),
      ],
    );
  }
}

class _Key extends StatelessWidget {
  const _Key({required this.label, required this.onTap, required this.foreground});

  final String label;
  final VoidCallback onTap;
  final Color foreground;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(AppSpacing.xs),
      child: Material(
        color: foreground.withValues(alpha: 0.06),
        shape: const CircleBorder(),
        child: InkWell(
          customBorder: const CircleBorder(),
          onTap: onTap,
          child: SizedBox.square(
            dimension: 72,
            child: Center(
              child: Text(label, style: AppTextStyles.title.copyWith(color: foreground, fontSize: 26)),
            ),
          ),
        ),
      ),
    );
  }
}
