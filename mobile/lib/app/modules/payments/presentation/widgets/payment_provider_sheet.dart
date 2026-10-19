import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../shared/presentation/session_service.dart';

/// Choix du moyen de paiement. Renvoie la valeur du prestataire choisi.
abstract final class PaymentProviderSheet {
  static Future<String?> show({required String label, required int amount}) {
    final providers = Get.find<SessionService>().paymentProviders;

    return Get.bottomSheet<String>(
      SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, AppSpacing.xl),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: AppTextStyles.caption),
              const SizedBox(height: AppSpacing.xs),
              Text(Formatters.money(amount), style: AppTextStyles.figure),
              const SizedBox(height: AppSpacing.xl),
              const Text('Payer avec', style: AppTextStyles.label),
              const SizedBox(height: AppSpacing.md),
              if (providers.isEmpty)
                const Text('Aucun moyen de paiement n\'est disponible pour le moment.', style: AppTextStyles.bodySecondary),
              for (final provider in providers) ...[
                Material(
                  color: AppColors.surface,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
                    side: const BorderSide(color: AppColors.border),
                  ),
                  child: ListTile(
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
                    leading: _ProviderLogo(provider.value),
                    title: Text(provider.label, style: AppTextStyles.label),
                    trailing: const Icon(Icons.chevron_right_rounded),
                    onTap: () => Get.back(result: provider.value),
                  ),
                ),
                const SizedBox(height: AppSpacing.sm),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

/// Logo officiel du moyen de paiement (voir assets/images/payments/README.md).
class _ProviderLogo extends StatelessWidget {
  const _ProviderLogo(this.provider);

  final String provider;

  static const _assets = {'wave': 'assets/images/payments/wave.png', 'orange_money': 'assets/images/payments/orange_money.png'};

  @override
  Widget build(BuildContext context) {
    final asset = _assets[provider];

    return ClipRRect(
      borderRadius: BorderRadius.circular(AppSpacing.radiusSm),
      child: SizedBox.square(
        dimension: 40,
        child: asset == null
            ? const ColoredBox(
                color: AppColors.violet50,
                child: Icon(Icons.science_outlined, color: AppColors.violet700),
              )
            : Image.asset(asset, fit: BoxFit.cover),
      ),
    );
  }
}
