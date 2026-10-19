import 'package:flutter/material.dart';

/// Comme [IndexedStack] (chaque onglet garde son état et sa position de défilement),
/// mais l'onglet qui apparaît arrive en fondu avec un léger glissement.
class FadeIndexedStack extends StatefulWidget {
  const FadeIndexedStack({super.key, required this.index, required this.children, this.duration = const Duration(milliseconds: 260)});

  final int index;
  final List<Widget> children;
  final Duration duration;

  @override
  State<FadeIndexedStack> createState() => _FadeIndexedStackState();
}

class _FadeIndexedStackState extends State<FadeIndexedStack> with SingleTickerProviderStateMixin {
  late final AnimationController _controller = AnimationController(vsync: this, duration: widget.duration, value: 1);
  late final Animation<double> _curve = CurvedAnimation(parent: _controller, curve: Curves.easeOutCubic);

  @override
  void didUpdateWidget(FadeIndexedStack oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.index != widget.index) {
      MediaQuery.of(context).disableAnimations ? _controller.value = 1 : _controller.forward(from: 0);
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
        child: Transform.translate(offset: Offset(0, 12 * (1 - _curve.value)), child: child),
      ),
      child: IndexedStack(
        index: widget.index,
        children: [
          // Les onglets cachés ne consomment pas d'animations.
          for (final (index, child) in widget.children.indexed) TickerMode(enabled: index == widget.index, child: child),
        ],
      ),
    );
  }
}
