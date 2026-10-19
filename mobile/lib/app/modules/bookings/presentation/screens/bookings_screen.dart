import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../core/widgets/state_views.dart';
import '../controllers/bookings_controller.dart';
import '../../domain/entities/booking.dart';

class BookingsScreen extends GetView<BookingsController> {
  const BookingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Mes consultations')),
      body: RefreshIndicator(
        onRefresh: controller.load,
        child: Obx(() {
          if (controller.isLoading.value && controller.items.isEmpty) {
            return const SkeletonList(count: 3, itemHeight: 110);
          }
          if (controller.errorMessage.value != null) {
            return ErrorState(message: controller.errorMessage.value!, onRetry: controller.load);
          }
          if (controller.items.isEmpty) {
            return const EmptyState(
              icon: Icons.event_available_outlined,
              title: 'Aucune consultation',
              message: 'Réserve un créneau avec un professionnel depuis l\'onglet Consultations.',
            );
          }
          return ListView.separated(
            padding: const EdgeInsets.all(AppSpacing.page),
            itemCount: controller.items.length,
            separatorBuilder: (_, _) => const SizedBox(height: AppSpacing.md),
            itemBuilder: (_, index) => _BookingCard(controller.items[index]),
          );
        }),
      ),
    );
  }
}

class _BookingCard extends GetView<BookingsController> {
  const _BookingCard(this.booking);

  final Booking booking;

  @override
  Widget build(BuildContext context) {
    final (color, background) = switch (booking.status) {
      'confirmed' => (AppColors.success, AppColors.successSoft),
      'pending_payment' => (AppColors.warning, AppColors.warningSoft),
      _ => (AppColors.textSecondary, AppColors.surfaceMuted),
    };

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(child: Text(booking.professionalName ?? 'Consultation', style: AppTextStyles.subtitle)),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm, vertical: 3),
                  decoration: BoxDecoration(color: background, borderRadius: BorderRadius.circular(AppSpacing.radiusPill)),
                  child: Text(
                    booking.statusLabel,
                    style: AppTextStyles.caption.copyWith(color: color, fontWeight: FontWeight.w600),
                  ),
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.xs),
            Text(
              '${booking.startsAt == null ? '' : Formatters.dayTime(booking.startsAt!)} · ${booking.modeLabel} · ${Formatters.money(booking.price)}',
              style: AppTextStyles.bodySecondary,
            ),
            if (booking.isCancellable) ...[
              const SizedBox(height: AppSpacing.md),
              Row(
                children: [
                  if (booking.awaitsPayment)
                    Expanded(
                      child: FilledButton(
                        onPressed: () => controller.pay(booking),
                        style: FilledButton.styleFrom(minimumSize: const Size.fromHeight(44)),
                        child: const Text('Payer'),
                      ),
                    ),
                  if (booking.awaitsPayment) const SizedBox(width: AppSpacing.sm),
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => controller.cancel(booking),
                      style: OutlinedButton.styleFrom(minimumSize: const Size.fromHeight(44)),
                      child: const Text('Annuler'),
                    ),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }
}
