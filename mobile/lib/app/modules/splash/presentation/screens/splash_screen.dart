import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/animated_logo.dart';
import '../controllers/splash_controller.dart';

/// Splash animé : le logo se construit (bulle, horizon, soleil levant, rayons),
/// puis le nom et la signature apparaissent. Respecte « réduire les animations ».
class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  final controller = Get.find<SplashController>();
  late final AnimationController _animation = AnimationController(vsync: this, duration: const Duration(milliseconds: 2200));

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (MediaQuery.of(context).disableAnimations) {
      _animation.value = 1;
      controller.onAnimationComplete();
    } else if (!_animation.isAnimating && _animation.value == 0) {
      _animation.forward().whenComplete(controller.onAnimationComplete);
    }
  }

  @override
  void dispose() {
    _animation.dispose();
    super.dispose();
  }

  double _between(double start, double end) => Curves.easeOutCubic.transform(((_animation.value - start) / (end - start)).clamp(0.0, 1.0));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AnimatedBuilder(
        animation: _animation,
        builder: (context, _) {
          final wordmark = _between(0.62, 0.9);
          final tagline = _between(0.78, 1);
          final glow = _between(0.3, 1);

          return DecoratedBox(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [AppColors.violet700, AppColors.violet950],
              ),
            ),
            child: Stack(
              alignment: Alignment.center,
              children: [
                // Halo doux derrière le logo, comme une lumière d'aube.
                Container(
                  width: 260,
                  height: 260,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: RadialGradient(
                      colors: [
                        AppColors.violet300.withValues(alpha: 0.35 * glow),
                        Colors.transparent,
                      ],
                    ),
                  ),
                ),
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    AnimatedLogo(progress: _animation.value, size: 112),
                    const SizedBox(height: AppSpacing.xl),
                    Opacity(
                      opacity: wordmark,
                      child: Transform.translate(
                        offset: Offset(0, 14 * (1 - wordmark)),
                        child: Text(
                          'SUNOOGO',
                          style: AppTextStyles.title.copyWith(color: AppColors.white, fontSize: 26, letterSpacing: 2 + 8 * (1 - wordmark)),
                        ),
                      ),
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    Opacity(
                      opacity: tagline,
                      child: Text(
                        "Parle librement. Quelqu'un t'écoute.",
                        style: AppTextStyles.titleAccent.copyWith(color: AppColors.lilac200, fontSize: 20),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
