import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../controllers/payment_controller.dart';

class PaymentStatusScreen extends GetView<PaymentController> {
  const PaymentStatusScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Paiement')),
      body: SafeArea(
        child: Obx(() {
          final payment = controller.payment.value;
          if (payment == null) {
            return const SizedBox.shrink();
          }

          final (icon, color, title, message) = switch (payment.status) {
            'succeeded' => (Icons.check_circle_rounded, AppColors.success, 'Paiement confirmé', 'Merci ! C\'est validé.'),
            'failed' || 'cancelled' => (
              Icons.error_outline_rounded,
              AppColors.sos,
              'Paiement non abouti',
              'Aucun montant n\'a été débité. Tu peux réessayer.',
            ),
            _ => (
              Icons.hourglass_top_rounded,
              AppColors.violet700,
              'En attente de confirmation',
              'Valide le paiement sur ton téléphone, puis reviens ici.',
            ),
          };

          return Padding(
            padding: const EdgeInsets.all(AppSpacing.page),
            child: Column(
              children: [
                const Spacer(),
                AnimatedSwitcher(
                  duration: const Duration(milliseconds: 450),
                  switchInCurve: Curves.easeOutBack,
                  transitionBuilder: (child, animation) => ScaleTransition(
                    scale: animation,
                    child: FadeTransition(opacity: animation, child: child),
                  ),
                  child: _StatusBadge(key: ValueKey(payment.status), status: payment.status, icon: icon, color: color),
                ),
                const SizedBox(height: AppSpacing.lg),
                AnimatedSwitcher(
                  duration: const Duration(milliseconds: 300),
                  child: Column(
                    key: ValueKey(title),
                    children: [
                      Text(title, style: AppTextStyles.title, textAlign: TextAlign.center),
                      const SizedBox(height: AppSpacing.sm),
                      Text(message, style: AppTextStyles.bodySecondary, textAlign: TextAlign.center),
                    ],
                  ),
                ),
                const SizedBox(height: AppSpacing.xl),
                Text('${payment.label ?? ''} · ${Formatters.money(payment.amount)}', style: AppTextStyles.caption),
                Text('Réf. ${payment.reference}', style: AppTextStyles.caption),
                const Spacer(),
                if (payment.isPending)
                  OutlinedButton(onPressed: controller.reopenCheckout, child: const Text('Rouvrir la page de paiement')),
                const SizedBox(height: AppSpacing.md),
                FilledButton(onPressed: Get.back, child: Text(payment.isPending ? 'Je reviendrai plus tard' : 'Terminer')),
              ],
            ),
          );
        }),
      ),
    );
  }
}

/// Pastille d'état : anneau qui tourne en attente, coche qui se dessine à la confirmation.
class _StatusBadge extends StatefulWidget {
  const _StatusBadge({super.key, required this.status, required this.icon, required this.color});

  final String status;
  final IconData icon;
  final Color color;

  @override
  State<_StatusBadge> createState() => _StatusBadgeState();
}

class _StatusBadgeState extends State<_StatusBadge> with SingleTickerProviderStateMixin {
  late final AnimationController _check = AnimationController(vsync: this, duration: const Duration(milliseconds: 650));

  bool get _succeeded => widget.status == 'succeeded';

  @override
  void initState() {
    super.initState();
    if (_succeeded) {
      HapticFeedback.mediumImpact();
      _check.forward();
    }
  }

  @override
  void dispose() {
    _check.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final pending = widget.status == 'pending';
    return SizedBox.square(
      dimension: 96,
      child: Stack(
        alignment: Alignment.center,
        children: [
          Container(
            width: 88,
            height: 88,
            decoration: BoxDecoration(shape: BoxShape.circle, color: widget.color.withValues(alpha: 0.1)),
          ),
          if (pending)
            SizedBox.square(
              dimension: 88,
              child: CircularProgressIndicator(strokeWidth: 3, color: widget.color, backgroundColor: widget.color.withValues(alpha: 0.12)),
            ),
          if (_succeeded)
            AnimatedBuilder(
              animation: _check,
              builder: (_, _) => CustomPaint(
                size: const Size.square(44),
                painter: _CheckPainter(progress: _check.value, color: widget.color),
              ),
            )
          else
            Icon(widget.icon, size: 40, color: widget.color),
        ],
      ),
    );
  }
}

class _CheckPainter extends CustomPainter {
  _CheckPainter({required this.progress, required this.color});

  final double progress;
  final Color color;

  @override
  void paint(Canvas canvas, Size size) {
    final path = Path()
      ..moveTo(size.width * 0.18, size.height * 0.52)
      ..lineTo(size.width * 0.42, size.height * 0.74)
      ..lineTo(size.width * 0.84, size.height * 0.28);
    final metric = path.computeMetrics().first;
    canvas.drawPath(
      metric.extractPath(0, metric.length * Curves.easeOutCubic.transform(progress)),
      Paint()
        ..color = color
        ..style = PaintingStyle.stroke
        ..strokeWidth = size.width * 0.11
        ..strokeCap = StrokeCap.round
        ..strokeJoin = StrokeJoin.round,
    );
  }

  @override
  bool shouldRepaint(_CheckPainter old) => old.progress != progress || old.color != color;
}
