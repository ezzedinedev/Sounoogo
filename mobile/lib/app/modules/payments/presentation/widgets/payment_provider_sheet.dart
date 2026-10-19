import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../../core/widgets/app_sheet.dart';
import '../../../../shared/domain/entities/payment_provider.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../domain/entities/payment_method.dart';

/// Choix du moyen de paiement. Pour le Mobile Money, demande aussi le numéro à débiter.
abstract final class PaymentProviderSheet {
  static Future<PaymentMethod?> show({required String label, required int amount}) =>
      AppSheet.show<PaymentMethod>(_PaymentSheet(label: label, amount: amount, providers: Get.find<SessionService>().paymentProviders));
}

class _PaymentSheet extends StatefulWidget {
  const _PaymentSheet({required this.label, required this.amount, required this.providers});

  final String label;
  final int amount;
  final List<PaymentProvider> providers;

  @override
  State<_PaymentSheet> createState() => _PaymentSheetState();
}

class _PaymentSheetState extends State<_PaymentSheet> {
  static final _phonePattern = RegExp(r'^\+?[0-9]{8,15}$');

  final _phone = TextEditingController();
  PaymentProvider? _selected;
  String? _phoneError;

  @override
  void dispose() {
    _phone.dispose();
    super.dispose();
  }

  void _choose(PaymentProvider provider) {
    if (!provider.requiresPhone) {
      Get.back(result: PaymentMethod(provider.value));
      return;
    }
    setState(() => _selected = provider);
  }

  void _confirm() {
    final phone = _phone.text.replaceAll(RegExp(r'[\s.-]'), '');
    if (!_phonePattern.hasMatch(phone)) {
      setState(() => _phoneError = 'Entre un numéro valide, par exemple 70 12 34 56.');
      return;
    }
    Get.back(result: PaymentMethod(_selected!.value, phone: phone));
  }

  @override
  Widget build(BuildContext context) {
    final selected = _selected;

    return SafeArea(
      child: Padding(
        padding: EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, AppSpacing.xl + MediaQuery.viewInsetsOf(context).bottom),
        child: AnimatedSize(
          duration: const Duration(milliseconds: 220),
          curve: Curves.easeOutCubic,
          alignment: Alignment.topCenter,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(widget.label, style: AppTextStyles.caption),
              const SizedBox(height: AppSpacing.xs),
              Text(Formatters.money(widget.amount), style: AppTextStyles.figure),
              const SizedBox(height: AppSpacing.xl),
              if (selected == null) ..._providerList() else ..._phoneStep(selected),
            ],
          ),
        ),
      ),
    );
  }

  List<Widget> _providerList() => [
    const Text('Payer avec', style: AppTextStyles.label),
    const SizedBox(height: AppSpacing.md),
    if (widget.providers.isEmpty)
      const Text("Aucun moyen de paiement n'est disponible pour le moment.", style: AppTextStyles.bodySecondary),
    for (final provider in widget.providers) ...[
      Material(
        color: AppColors.surface,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppSpacing.radiusMd),
          side: const BorderSide(color: AppColors.border),
        ),
        child: ListTile(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
          leading: _ProviderIcon(provider),
          title: Text(provider.label, style: AppTextStyles.label),
          subtitle: provider.requiresPhone ? const Text('Validation sur ton téléphone', style: AppTextStyles.caption) : null,
          trailing: const Icon(Icons.chevron_right_rounded),
          onTap: () => _choose(provider),
        ),
      ),
      const SizedBox(height: AppSpacing.sm),
    ],
  ];

  List<Widget> _phoneStep(PaymentProvider provider) => [
    Row(
      children: [
        IconButton(
          tooltip: 'Changer de moyen de paiement',
          onPressed: () => setState(() => _selected = null),
          icon: const Icon(Icons.arrow_back_rounded),
        ),
        Text(provider.label, style: AppTextStyles.label),
      ],
    ),
    const SizedBox(height: AppSpacing.md),
    TextField(
      controller: _phone,
      autofocus: true,
      keyboardType: TextInputType.phone,
      inputFormatters: [FilteringTextInputFormatter.allow(RegExp(r'[0-9+\s]'))],
      textInputAction: TextInputAction.done,
      onSubmitted: (_) => _confirm(),
      onChanged: (_) => setState(() => _phoneError = null),
      decoration: InputDecoration(labelText: 'Numéro Mobile Money', hintText: '70 12 34 56', errorText: _phoneError),
    ),
    const SizedBox(height: AppSpacing.sm),
    const Row(
      children: [
        Icon(Icons.lock_outline_rounded, size: 16, color: AppColors.textSecondary),
        SizedBox(width: AppSpacing.xs),
        Expanded(child: Text("Ce numéro sert uniquement au paiement. SUNOOGO ne le conserve pas.", style: AppTextStyles.caption)),
      ],
    ),
    const SizedBox(height: AppSpacing.lg),
    FilledButton(onPressed: _confirm, child: Text('Payer ${Formatters.money(widget.amount)}')),
  ];
}

class _ProviderIcon extends StatelessWidget {
  const _ProviderIcon(this.provider);

  final PaymentProvider provider;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 40,
      height: 40,
      decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusSm)),
      child: Icon(provider.requiresPhone ? Icons.phone_iphone_rounded : Icons.science_outlined, color: AppColors.violet700),
    );
  }
}
