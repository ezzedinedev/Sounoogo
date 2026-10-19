import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

/// Logo SUNOOGO (badge violet, bulle et soleil levant).
class BrandMark extends StatelessWidget {
  const BrandMark({super.key, this.size = 40, this.inverse = false});

  final double size;
  final bool inverse;

  @override
  Widget build(BuildContext context) => SvgPicture.asset(
    inverse ? 'assets/images/logo-mark-inverse.svg' : 'assets/images/logo-badge.svg',
    width: size,
    height: size,
    semanticsLabel: 'SUNOOGO',
  );
}
