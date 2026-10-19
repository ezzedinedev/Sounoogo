import 'package:flutter/material.dart';

import '../theme/app_colors.dart';
import '../theme/app_spacing.dart';
import '../theme/app_text_styles.dart';

/// État vide : une icône, un titre, une explication et éventuellement une action.
class EmptyState extends StatelessWidget {
  const EmptyState({super.key, required this.icon, required this.title, this.message, this.action});

  final IconData icon;
  final String title;
  final String? message;
  final Widget? action;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.xxl),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
              child: Icon(icon, color: AppColors.violet700, size: 30),
            ),
            const SizedBox(height: AppSpacing.lg),
            Text(title, style: AppTextStyles.subtitle, textAlign: TextAlign.center),
            if (message != null) ...[
              const SizedBox(height: AppSpacing.sm),
              Text(message!, style: AppTextStyles.bodySecondary, textAlign: TextAlign.center),
            ],
            if (action != null) ...[const SizedBox(height: AppSpacing.xl), action!],
          ],
        ),
      ),
    );
  }
}

/// État d'erreur avec bouton « Réessayer ».
class ErrorState extends StatelessWidget {
  const ErrorState({super.key, required this.message, required this.onRetry});

  final String message;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return EmptyState(
      icon: Icons.wifi_off_rounded,
      title: 'Oups',
      message: message,
      action: OutlinedButton.icon(
        onPressed: onRetry,
        icon: const Icon(Icons.refresh_rounded),
        label: const Text('Réessayer'),
        style: OutlinedButton.styleFrom(minimumSize: const Size(180, 48)),
      ),
    );
  }
}

/// Bloc de chargement : un reflet lumineux le traverse en continu (immobile si les animations sont réduites).
class Skeleton extends StatefulWidget {
  const Skeleton({super.key, this.height = 16, this.width, this.radius = AppSpacing.radiusSm});

  final double height;
  final double? width;
  final double radius;

  @override
  State<Skeleton> createState() => _SkeletonState();
}

class _SkeletonState extends State<Skeleton> with SingleTickerProviderStateMixin {
  late final AnimationController _controller = AnimationController(vsync: this, duration: const Duration(milliseconds: 1400));

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (MediaQuery.of(context).disableAnimations) {
      _controller.stop();
    } else if (!_controller.isAnimating) {
      _controller.repeat();
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
      animation: _controller,
      builder: (context, _) {
        final t = _controller.value * 3 - 1;
        return Container(
          height: widget.height,
          width: widget.width,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(widget.radius),
            gradient: LinearGradient(
              begin: Alignment(t - 1, -0.3),
              end: Alignment(t + 1, 0.3),
              colors: const [AppColors.surfaceMuted, AppColors.violet50, AppColors.surfaceMuted],
              stops: const [0.25, 0.5, 0.75],
            ),
          ),
        );
      },
    );
  }
}

/// Liste de cartes squelettes pendant le chargement d'une liste.
class SkeletonList extends StatelessWidget {
  const SkeletonList({super.key, this.count = 4, this.itemHeight = 120});

  final int count;
  final double itemHeight;

  @override
  Widget build(BuildContext context) {
    return ListView.separated(
      padding: const EdgeInsets.all(AppSpacing.page),
      physics: const NeverScrollableScrollPhysics(),
      itemCount: count,
      separatorBuilder: (_, _) => const SizedBox(height: AppSpacing.md),
      itemBuilder: (_, _) => Skeleton(height: itemHeight, radius: AppSpacing.radiusLg),
    );
  }
}
