import 'package:flutter/material.dart';

/// Apparition douce (fondu + glissement vers le haut), avec un décalage pour
/// créer des cascades. Désactivée si l'utilisateur a demandé moins d'animations.
class FadeSlideIn extends StatefulWidget {
  const FadeSlideIn({super.key, required this.child, this.index = 0, this.offset = 24, this.duration = const Duration(milliseconds: 520)});

  final Widget child;

  /// Cascade sur les éléments d'une liste. Les espacements ([SizedBox], [Spacer]) et les enfants
  /// flexibles d'une [Flex] restent tels quels.
  static List<Widget> stagger(List<Widget> children, {int start = 0}) {
    var rank = start;
    return [
      for (final child in children)
        child is SizedBox || child is Spacer || child is Flexible ? child : FadeSlideIn(index: rank++, child: child),
    ];
  }

  /// Position dans la cascade : chaque rang ajoute 70 ms de délai.
  final int index;
  final double offset;
  final Duration duration;

  @override
  State<FadeSlideIn> createState() => _FadeSlideInState();
}

class _FadeSlideInState extends State<FadeSlideIn> with SingleTickerProviderStateMixin {
  late final AnimationController _controller = AnimationController(vsync: this, duration: widget.duration);
  late final Animation<double> _curve = CurvedAnimation(parent: _controller, curve: Curves.easeOutCubic);
  bool _started = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_started) {
      return;
    }
    _started = true;
    if (MediaQuery.of(context).disableAnimations) {
      _controller.value = 1;
    } else {
      Future<void>.delayed(Duration(milliseconds: 70 * widget.index.clamp(0, 8)), () {
        if (mounted) {
          _controller.forward();
        }
      });
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
      builder: (context, child) => Opacity(
        opacity: _curve.value,
        child: Transform.translate(offset: Offset(0, widget.offset * (1 - _curve.value)), child: child),
      ),
      child: widget.child,
    );
  }
}
