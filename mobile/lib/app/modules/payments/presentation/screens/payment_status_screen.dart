import 'package:flutter/material.dart';
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
                Icon(icon, size: 72, color: color),
                const SizedBox(height: AppSpacing.lg),
                Text(title, style: AppTextStyles.title, textAlign: TextAlign.center),
                const SizedBox(height: AppSpacing.sm),
                Text(message, style: AppTextStyles.bodySecondary, textAlign: TextAlign.center),
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
