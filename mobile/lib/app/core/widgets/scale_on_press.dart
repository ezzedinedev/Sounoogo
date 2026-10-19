import 'package:flutter/material.dart';

/// Enfoncement léger pendant l'appui, sans prendre le geste : l'[InkWell] de l'enfant
/// garde son effet d'encre et son action. À utiliser autour des cartes cliquables.
class ScaleOnPress extends StatefulWidget {
  const ScaleOnPress({super.key, required this.child, this.scale = 0.98});

  final Widget child;
  final double scale;

  @override
  State<ScaleOnPress> createState() => _ScaleOnPressState();
}

class _ScaleOnPressState extends State<ScaleOnPress> {
  bool _pressed = false;

  void _set(bool pressed) {
    if (_pressed != pressed) {
      setState(() => _pressed = pressed);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Listener(
      onPointerDown: (_) => _set(true),
      onPointerUp: (_) => _set(false),
      onPointerCancel: (_) => _set(false),
      child: AnimatedScale(
        scale: _pressed ? widget.scale : 1,
        duration: const Duration(milliseconds: 140),
        curve: Curves.easeOut,
        child: widget.child,
      ),
    );
  }
}
