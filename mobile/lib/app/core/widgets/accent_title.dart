import 'package:flutter/material.dart';

import '../theme/app_text_styles.dart';

/// Titre « à la SUNOOGO » : sans-serif, terminé par un accent en serif italique.
class AccentTitle extends StatelessWidget {
  const AccentTitle(this.text, {super.key, this.accent, this.large = false, this.inverse = false, this.textAlign});

  final String text;
  final String? accent;
  final bool large;
  final bool inverse;
  final TextAlign? textAlign;

  @override
  Widget build(BuildContext context) {
    final base = large ? AppTextStyles.display : AppTextStyles.title;
    final accentStyle = large ? AppTextStyles.displayAccent : AppTextStyles.titleAccent;
    final color = inverse ? Colors.white : null;

    return Text.rich(
      TextSpan(
        style: base.copyWith(color: color),
        children: [
          TextSpan(text: text),
          if (accent != null) ...[
            const TextSpan(text: ' '),
            TextSpan(
              text: accent,
              style: accentStyle.copyWith(color: inverse ? Colors.white.withValues(alpha: 0.85) : null),
            ),
          ],
        ],
      ),
      textAlign: textAlign,
    );
  }
}
