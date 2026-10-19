import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../core/models/plan_model.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/utils/formatters.dart';
import '../../../core/widgets/accent_title.dart';
import '../controllers/subscription_controller.dart';

class SubscriptionScreen extends GetView<SubscriptionController> {
  const SubscriptionScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Abonnement')),
      body: Obx(() {
        final current = controller.current;
        return ListView(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.xxl),
          children: [
            const AccentTitle("L'écoute reste", accent: 'toujours gratuite', large: true),
            const SizedBox(height: AppSpacing.md),
            const Text('L\'abonnement débloque toutes les formations et réduit le prix des consultations. Sans engagement.', style: AppTextStyles.bodySecondary),
            if (current != null && current.isActive) ...[
              const SizedBox(height: AppSpacing.xl),
              Container(
                padding: const EdgeInsets.all(AppSpacing.lg),
                decoration: BoxDecoration(color: AppColors.successSoft, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
                child: Text(
                  'Ton abonnement ${current.plan?.name ?? ''} est actif${current.endsAt == null ? '' : ' jusqu\'au ${Formatters.day(current.endsAt!)}'}.',
                  style: AppTextStyles.label.copyWith(color: AppColors.success),
                ),
              ),
            ],
            const SizedBox(height: AppSpacing.xl),
            for (final (index, plan) in controller.plans.indexed) ...[
              _PlanCard(plan: plan, featured: index == controller.plans.length - 1, onChoose: () => controller.subscribe(plan)),
              const SizedBox(height: AppSpacing.lg),
            ],
            Text('Paiement par Wave ou Orange Money. Pas de renouvellement automatique.', style: AppTextStyles.caption, textAlign: TextAlign.center),
          ],
        );
      }),
    );
  }
}

class _PlanCard extends StatelessWidget {
  const _PlanCard({required this.plan, required this.featured, required this.onChoose});

  final PlanModel plan;
  final bool featured;
  final VoidCallback onChoose;

  @override
  Widget build(BuildContext context) {
    final foreground = featured ? AppColors.white : AppColors.textPrimary;
    final muted = featured ? AppColors.lilac200 : AppColors.textSecondary;

    return Container(
      padding: const EdgeInsets.all(AppSpacing.xl),
      decoration: BoxDecoration(
        color: featured ? AppColors.violet950 : AppColors.surface,
        gradient: featured ? const LinearGradient(colors: [AppColors.violet800, AppColors.violet950], begin: Alignment.topLeft, end: Alignment.bottomRight) : null,
        border: featured ? null : Border.all(color: AppColors.border),
        borderRadius: BorderRadius.circular(AppSpacing.radiusLg),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Text(plan.name, style: AppTextStyles.subtitle.copyWith(color: foreground)),
              const Spacer(),
              if (featured)
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm, vertical: 3),
                  decoration: BoxDecoration(color: AppColors.white, borderRadius: BorderRadius.circular(AppSpacing.radiusPill)),
                  child: Text('Le plus complet', style: AppTextStyles.caption.copyWith(color: AppColors.violet800, fontWeight: FontWeight.w700)),
                ),
            ],
          ),
          if (plan.description != null) Text(plan.description!, style: AppTextStyles.caption.copyWith(color: muted)),
          const SizedBox(height: AppSpacing.lg),
          Text.rich(TextSpan(children: [
            TextSpan(text: Formatters.money(plan.price), style: AppTextStyles.figure.copyWith(color: foreground)),
            TextSpan(text: '  / mois', style: AppTextStyles.caption.copyWith(color: muted)),
          ])),
          const SizedBox(height: AppSpacing.lg),
          for (final feature in plan.features)
            Padding(
              padding: const EdgeInsets.only(bottom: AppSpacing.sm),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.check_rounded, size: 18, color: featured ? AppColors.lilac200 : AppColors.violet700),
                  const SizedBox(width: AppSpacing.sm),
                  Expanded(child: Text(feature, style: AppTextStyles.body.copyWith(color: foreground))),
                ],
              ),
            ),
          const SizedBox(height: AppSpacing.md),
          FilledButton(
            onPressed: onChoose,
            style: featured ? FilledButton.styleFrom(backgroundColor: AppColors.white, foregroundColor: AppColors.violet800) : null,
            child: Text('Choisir ${plan.name}'),
          ),
        ],
      ),
    );
  }
}
