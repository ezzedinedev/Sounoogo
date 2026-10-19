import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../domain/entities/mood_level.dart';

/// Visage dessiné (pas d'emoji) : rendu identique sur tous les téléphones, aux couleurs de la marque.
class MoodFace extends StatelessWidget {
  const MoodFace({super.key, required this.level, this.size = 44, this.selected = false});

  final MoodLevel level;
  final double size;
  final bool selected;

  @override
  Widget build(BuildContext context) {
    return Semantics(
      label: level.label,
      selected: selected,
      child: AnimatedScale(
        scale: selected ? 1.08 : 1,
        duration: const Duration(milliseconds: 260),
        curve: Curves.easeOutBack,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          curve: Curves.easeOutCubic,
          width: size,
          height: size,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: selected ? AppColors.violet700 : AppColors.violet50,
            border: Border.all(color: selected ? AppColors.violet700 : AppColors.violet100, width: 1.5),
          ),
          child: CustomPaint(
            painter: _FacePainter(level: level, color: selected ? AppColors.white : AppColors.violet700),
          ),
        ),
      ),
    );
  }
}

class _FacePainter extends CustomPainter {
  _FacePainter({required this.level, required this.color});

  final MoodLevel level;
  final Color color;

  @override
  void paint(Canvas canvas, Size size) {
    final s = size.width;
    final stroke = Paint()
      ..color = color
      ..style = PaintingStyle.stroke
      ..strokeWidth = s * 0.065
      ..strokeCap = StrokeCap.round;
    final fill = Paint()..color = color;

    // Yeux
    canvas
      ..drawCircle(Offset(s * 0.36, s * 0.41), s * 0.05, fill)
      ..drawCircle(Offset(s * 0.64, s * 0.41), s * 0.05, fill);

    // Bouche : courbure de -1 (triste) à 1 (sourire)
    final curve = (level.value - 3) / 2;
    final y = s * 0.66;
    final mouth = Path()
      ..moveTo(s * 0.33, y - curve * s * 0.04)
      ..quadraticBezierTo(s * 0.5, y + curve * s * 0.16, s * 0.67, y - curve * s * 0.04);
    if (curve == 0) {
      canvas.drawLine(Offset(s * 0.36, y), Offset(s * 0.64, y), stroke);
    } else if (level == MoodLevel.veryGood) {
      // Grand sourire ouvert
      final smile = Path()
        ..moveTo(s * 0.31, s * 0.58)
        ..quadraticBezierTo(s * 0.5, s * 0.86, s * 0.69, s * 0.58)
        ..close();
      canvas.drawPath(smile, fill);
    } else {
      canvas.drawPath(mouth, stroke);
    }

    // Sourcils inquiets pour « très difficile » : relevés vers le centre.
    if (level == MoodLevel.veryLow) {
      final brow = stroke..strokeWidth = s * 0.05;
      canvas
        ..drawLine(Offset(s * 0.27, s * 0.31), Offset(s * 0.41, s * 0.26), brow)
        ..drawLine(Offset(s * 0.59, s * 0.26), Offset(s * 0.73, s * 0.31), brow);
    }
  }

  @override
  bool shouldRepaint(_FacePainter old) => old.level != level || old.color != color;
}
