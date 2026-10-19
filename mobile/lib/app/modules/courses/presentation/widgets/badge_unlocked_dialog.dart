import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/learning_badge.dart';
import 'badge_medallion.dart';

/// Célébration d'un ou plusieurs jalons : la médaille se pose, des rayons de soleil tournent
/// lentement derrière elle et quelques éclats s'envolent. Sobre si les animations sont réduites.
abstract final class BadgeUnlockedDialog {
  static Future<void> show(List<LearningBadge> badges) => Get.dialog<void>(
    _BadgeUnlocked(badges: badges),
    barrierColor: AppColors.violet950.withValues(alpha: 0.55),
  );
}

class _BadgeUnlocked extends StatefulWidget {
  const _BadgeUnlocked({required this.badges});

  final List<LearningBadge> badges;

  @override
  State<_BadgeUnlocked> createState() => _BadgeUnlockedState();
}

class _BadgeUnlockedState extends State<_BadgeUnlocked> with TickerProviderStateMixin {
  late final _entrance = AnimationController(vsync: this, duration: const Duration(milliseconds: 1100));
  late final _rays = AnimationController(vsync: this, duration: const Duration(seconds: 14));
  int _index = 0;

  LearningBadge get _badge => widget.badges[_index];

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (MediaQuery.disableAnimationsOf(context)) {
      _entrance.value = 1;
    } else if (!_entrance.isAnimating && _entrance.value == 0) {
      _entrance.forward();
      _rays.repeat();
    }
  }

  @override
  void dispose() {
    _entrance.dispose();
    _rays.dispose();
    super.dispose();
  }

  void _next() {
    if (_index == widget.badges.length - 1) {
      Get.back<void>();
      return;
    }
    setState(() => _index++);
    if (!MediaQuery.disableAnimationsOf(context)) _entrance.forward(from: 0);
  }

  @override
  Widget build(BuildContext context) {
    final medal = CurvedAnimation(parent: _entrance, curve: const Interval(0, 0.7, curve: Curves.elasticOut));
    final text = CurvedAnimation(parent: _entrance, curve: const Interval(0.35, 1, curve: Curves.easeOutCubic));

    return Center(
      child: Material(
        color: Colors.transparent,
        child: Container(
          width: 320,
          margin: const EdgeInsets.all(AppSpacing.page),
          padding: const EdgeInsets.fromLTRB(AppSpacing.xl, AppSpacing.xl, AppSpacing.xl, AppSpacing.lg),
          decoration: BoxDecoration(color: AppColors.surface, borderRadius: BorderRadius.circular(AppSpacing.radiusLg)),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text('NOUVEAU JALON', style: AppTextStyles.caption.copyWith(color: AppColors.violet700, fontWeight: FontWeight.w700, letterSpacing: 1.4)),
              const SizedBox(height: AppSpacing.md),
              SizedBox.square(
                dimension: 168,
                child: AnimatedBuilder(
                  animation: Listenable.merge([_entrance, _rays]),
                  builder: (_, _) => CustomPaint(
                    painter: _CelebrationPainter(rays: _rays.value, burst: _entrance.value),
                    child: Center(
                      child: Transform.scale(
                        scale: 0.4 + 0.6 * medal.value,
                        child: Opacity(
                          opacity: medal.value.clamp(0, 1),
                          child: BadgeMedallion(badge: _badge, size: 96),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: AppSpacing.md),
              FadeTransition(
                opacity: text,
                child: SlideTransition(
                  position: Tween(begin: const Offset(0, 0.25), end: Offset.zero).animate(text),
                  child: Column(
                    children: [
                      Text(_badge.label, style: AppTextStyles.title.copyWith(fontSize: 24), textAlign: TextAlign.center),
                      const SizedBox(height: AppSpacing.sm),
                      Text(_badge.description, style: AppTextStyles.bodySecondary, textAlign: TextAlign.center),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: AppSpacing.xl),
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  onPressed: _next,
                  child: Text(_index < widget.badges.length - 1 ? 'Jalon suivant' : 'Merci !'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _CelebrationPainter extends CustomPainter {
  _CelebrationPainter({required this.rays, required this.burst});

  /// Rotation des rayons (0 → 1 = un tour).
  final double rays;

  /// Avancement de l'entrée (0 → 1) : les éclats partent du centre et s'estompent.
  final double burst;

  static const _sparkColors = [AppColors.violet300, AppColors.violet500, AppColors.lilac200, AppColors.violet700];

  @override
  void paint(Canvas canvas, Size size) {
    final center = size.center(Offset.zero);
    final radius = size.width / 2;

    // Halo
    canvas.drawCircle(
      center,
      radius * 0.82,
      Paint()..shader = RadialGradient(colors: [AppColors.violet100, AppColors.violet100.withValues(alpha: 0)]).createShader(Rect.fromCircle(center: center, radius: radius * 0.82)),
    );

    // Rayons de soleil
    final rayPaint = Paint()..color = AppColors.violet200.withValues(alpha: 0.55 * burst.clamp(0, 1));
    const count = 12;
    for (var i = 0; i < count; i++) {
      final angle = rays * 2 * math.pi + i * 2 * math.pi / count;
      final path = Path()
        ..moveTo(center.dx, center.dy)
        ..lineTo(center.dx + math.cos(angle - 0.09) * radius, center.dy + math.sin(angle - 0.09) * radius)
        ..lineTo(center.dx + math.cos(angle + 0.09) * radius, center.dy + math.sin(angle + 0.09) * radius)
        ..close();
      canvas.drawPath(path, rayPaint);
    }

    // Éclats
    if (burst > 0 && burst < 1) {
      final travel = Curves.easeOutCubic.transform(burst);
      final fade = 1 - Curves.easeIn.transform(burst);
      for (var i = 0; i < 16; i++) {
        final angle = i * 2 * math.pi / 16 + (i.isEven ? 0.2 : -0.1);
        final distance = radius * (0.35 + 0.6 * travel) * (i.isEven ? 1 : 0.8);
        final offset = center + Offset(math.cos(angle), math.sin(angle)) * distance;
        canvas.drawCircle(offset, i % 3 == 0 ? 4 : 2.5, Paint()..color = _sparkColors[i % _sparkColors.length].withValues(alpha: fade));
      }
    }
  }

  @override
  bool shouldRepaint(_CelebrationPainter old) => old.rays != rays || old.burst != burst;
}
