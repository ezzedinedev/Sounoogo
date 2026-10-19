import 'package:flutter/material.dart';

import '../theme/app_colors.dart';

/// Halo lumineux qui dérive lentement derrière un contenu sombre : donne de la profondeur
/// sans distraire. Immobile si l'utilisateur a réduit les animations.
class DriftingGlow extends StatefulWidget {
  const DriftingGlow({super.key, required this.child, this.color = AppColors.violet500});

  final Widget child;
  final Color color;

  @override
  State<DriftingGlow> createState() => _DriftingGlowState();
}

class _DriftingGlowState extends State<DriftingGlow> with SingleTickerProviderStateMixin {
  late final AnimationController _controller = AnimationController(vsync: this, duration: const Duration(seconds: 14));
  late final Animation<double> _curve = CurvedAnimation(parent: _controller, curve: Curves.easeInOutSine);

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (MediaQuery.of(context).disableAnimations) {
      _controller.value = 0.5;
    } else if (!_controller.isAnimating) {
      _controller.repeat(reverse: true);
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _curve,
      builder: (context, child) {
        final t = _curve.value;
        return DecoratedBox(
          decoration: BoxDecoration(
            gradient: RadialGradient(
              center: Alignment(-0.6 + 1.2 * t, -0.7 + 0.4 * t),
              radius: 1.1,
              colors: [widget.color.withValues(alpha: 0.55), widget.color.withValues(alpha: 0)],
            ),
          ),
          child: child,
        );
      },
      child: widget.child,
    );
  }
}
