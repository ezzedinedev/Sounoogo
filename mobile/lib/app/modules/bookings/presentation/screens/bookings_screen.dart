import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../core/widgets/state_views.dart';
import '../controllers/bookings_controller.dart';
import '../../domain/entities/booking.dart';
import '../../../../core/widgets/fade_slide_in.dart';

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
            itemBuilder: (_, index) => FadeSlideIn(index: index, child: _BookingCard(controller.items[index])),
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
            if (booking.status == 'confirmed' && booking.video != null) ...[
              const SizedBox(height: AppSpacing.md),
              _VideoAccess(booking: booking),
            ],
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

/// Accès à la salle de visio : bouton actif pendant la fenêtre d'ouverture, horaire sinon.
class _VideoAccess extends GetView<BookingsController> {
  const _VideoAccess({required this.booking});

  final Booking booking;

  @override
  Widget build(BuildContext context) {
    return Obx(() {
      final room = booking.video!;
      final now = controller.now.value;

      if (room.isOverAt(now)) return const SizedBox.shrink();
      if (!room.isOpenAt(now)) {
        return Row(
          children: [
            const Icon(Icons.videocam_outlined, size: 18, color: AppColors.violet700),
            const SizedBox(width: AppSpacing.sm),
            Expanded(child: Text('La salle de visio ouvrira ${Formatters.dayTime(room.opensAt)}.', style: AppTextStyles.caption)),
          ],
        );
      }

      final joining = controller.isJoining.value == booking.id;
      return FilledButton.icon(
        onPressed: joining ? null : () => controller.joinVideo(booking),
        style: FilledButton.styleFrom(minimumSize: const Size.fromHeight(48)),
        icon: joining
            ? const SizedBox.square(dimension: 18, child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.white))
            : const _LiveDot(),
        label: const Text('Rejoindre la visio'),
      );
    });
  }
}

/// Point « en direct » qui pulse doucement (fixe si les animations sont réduites).
class _LiveDot extends StatefulWidget {
  const _LiveDot();

  @override
  State<_LiveDot> createState() => _LiveDotState();
}

class _LiveDotState extends State<_LiveDot> with SingleTickerProviderStateMixin {
  late final _pulse = AnimationController(vsync: this, duration: const Duration(milliseconds: 1200));

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (MediaQuery.disableAnimationsOf(context)) {
      _pulse.stop();
    } else if (!_pulse.isAnimating) {
      _pulse.repeat(reverse: true);
    }
  }

  @override
  void dispose() {
    _pulse.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return SizedBox.square(
      dimension: 18,
      child: Center(
        child: FadeTransition(
          opacity: Tween<double>(begin: 1, end: 0.35).animate(_pulse),
          child: Container(
            width: 10,
            height: 10,
            decoration: const BoxDecoration(color: AppColors.lilac200, shape: BoxShape.circle),
          ),
        ),
      ),
    );
  }
}
