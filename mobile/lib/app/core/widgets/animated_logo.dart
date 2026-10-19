import 'package:flutter/material.dart';

import '../theme/app_colors.dart';

/// Logo SUNOOGO dessiné en code, pour pouvoir l'animer étape par étape :
/// la bulle apparaît, l'horizon se trace, le soleil se lève, puis les rayons.
/// [progress] va de 0 (rien) à 1 (logo complet).
class AnimatedLogo extends StatelessWidget {
  const AnimatedLogo({super.key, required this.progress, this.size = 120});

  final double progress;
  final double size;

  @override
  Widget build(BuildContext context) {
    return SizedBox.square(
      dimension: size,
      child: CustomPaint(painter: _LogoPainter(progress)),
    );
  }
}

class _LogoPainter extends CustomPainter {
  _LogoPainter(this.t);

  final double t;

  /// Avancement local d'une étape entre [start] et [end] de la timeline, avec une courbe.
  double _phase(double start, double end, [Curve curve = Curves.easeOutCubic]) =>
      curve.transform(((t - start) / (end - start)).clamp(0.0, 1.0));

  static Path _bubble() => Path()
    ..moveTo(32, 4)
    ..cubicTo(47.5, 4, 60, 15.4, 60, 30.5)
    ..cubicTo(60, 45.6, 47.5, 57, 32, 57)
    ..cubicTo(28.8, 57, 25.7, 56.5, 22.8, 55.6)
    ..lineTo(11.5, 61)
    ..cubicTo(10.2, 61.6, 8.8, 60.4, 9.2, 59)
    ..lineTo(11.8, 50.3)
    ..cubicTo(7, 45.6, 4, 38.5, 4, 30.5)
    ..cubicTo(4, 15.4, 16.5, 4, 32, 4)
    ..close();

  static const _rays = [
    (Offset(32, 22), Offset(32, 17.5)),
    (Offset(25.1, 24.6), Offset(22.1, 21.4)),
    (Offset(38.9, 24.6), Offset(41.9, 21.4)),
    (Offset(20.6, 30.7), Offset(16.5, 29.3)),
    (Offset(43.4, 30.7), Offset(47.5, 29.3)),
  ];

  @override
  void paint(Canvas canvas, Size size) {
    canvas.scale(size.width / 64);

    // 1. Bulle : apparition avec un léger rebond.
    final bubble = _phase(0, 0.32, Curves.easeOutBack);
    if (bubble > 0) {
      canvas.save();
      canvas.translate(32, 32);
      canvas.scale(0.6 + 0.4 * bubble);
      canvas.translate(-32, -32);
      canvas.drawPath(_bubble(), Paint()..color = AppColors.white.withValues(alpha: _phase(0, 0.2)));
      canvas.restore();
    }

    // 2. Horizon : se trace depuis le centre.
    final horizon = _phase(0.28, 0.5);
    if (horizon > 0) {
      final half = 18 * horizon;
      canvas.drawLine(
        Offset(32 - half, 41),
        Offset(32 + half, 41),
        Paint()
          ..color = AppColors.violet300
          ..strokeWidth = 3
          ..strokeCap = StrokeCap.round,
      );
    }

    // 3. Soleil : se lève derrière l'horizon (masqué sous la ligne).
    final sun = _phase(0.38, 0.68);
    if (sun > 0) {
      canvas.save();
      canvas.clipRect(const Rect.fromLTRB(0, 0, 64, 41));
      canvas.drawCircle(Offset(32, 41 + 11 * (1 - sun)), 11, Paint()..color = AppColors.violet700);
      canvas.restore();
    }

    // 4. Rayons : apparaissent l'un après l'autre, du centre vers les côtés.
    final rayPaint = Paint()
      ..color = AppColors.violet700
      ..strokeWidth = 2.6
      ..strokeCap = StrokeCap.round;
    for (final (index, (from, to)) in _rays.indexed) {
      final ray = _phase(0.6 + index * 0.05, 0.74 + index * 0.05);
      if (ray > 0) {
        canvas.drawLine(from, Offset.lerp(from, to, ray)!, rayPaint);
      }
    }
  }

  @override
  bool shouldRepaint(_LogoPainter oldDelegate) => oldDelegate.t != t;
}
