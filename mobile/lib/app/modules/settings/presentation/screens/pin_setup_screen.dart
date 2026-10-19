import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/app_toast.dart';
import '../controllers/settings_controller.dart';
import '../widgets/pin_pad.dart';

/// Création du code en deux temps : saisie puis confirmation.
class PinSetupScreen extends StatefulWidget {
  const PinSetupScreen({super.key});

  @override
  State<PinSetupScreen> createState() => _PinSetupScreenState();
}

class _PinSetupScreenState extends State<PinSetupScreen> {
  static const _minLength = 4;
  static const _maxLength = 6;

  final _controller = Get.find<SettingsController>();
  String _pin = '';
  String? _first;
  String? _error;
  bool _saving = false;

  bool get _confirming => _first != null;

  void _onDigit(String digit) {
    if (_pin.length < (_confirming ? _first!.length : _maxLength)) {
      setState(() {
        _pin += digit;
        _error = null;
      });
      if (_confirming && _pin.length == _first!.length) {
        _save();
      }
    }
  }

  void _next() => setState(() {
    _first = _pin;
    _pin = '';
  });

  Future<void> _save() async {
    setState(() => _saving = true);
    final error = await _controller.createPin(_first!, _pin);
    if (!mounted) {
      return;
    }
    if (error == null) {
      // Retour d'abord : avec une notification ouverte, Get.back() fermerait la notification.
      Get.back<void>();
      AppToast.show('Verrou activé', 'Ton code sera demandé à chaque retour dans l\'app.');
      return;
    }
    setState(() {
      _saving = false;
      _error = error;
      _first = null;
      _pin = '';
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Mon code')),
      body: SafeArea(
        child: Column(
          children: [
            const Spacer(),
            Text(
              _confirming ? 'Confirme ton code' : 'Choisis un code de 4 à 6 chiffres',
              style: AppTextStyles.title,
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: AppSpacing.sm),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: AppSpacing.xxl),
              child: Text(
                'Évite ta date de naissance ou un code que tes proches connaissent.',
                style: AppTextStyles.bodySecondary,
                textAlign: TextAlign.center,
              ),
            ),
            const SizedBox(height: AppSpacing.xl),
            PinDots(
              length: _confirming ? _first!.length : (_pin.length < _minLength ? _minLength : _pin.length),
              filled: _pin.length,
              error: _error != null,
            ),
            const SizedBox(height: AppSpacing.md),
            SizedBox(
              height: 40,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppSpacing.xl),
                child: _saving
                    ? const Center(child: SizedBox.square(dimension: 22, child: CircularProgressIndicator(strokeWidth: 2.4)))
                    : Text(
                        _error ?? '',
                        style: AppTextStyles.caption.copyWith(color: AppColors.sos),
                        textAlign: TextAlign.center,
                      ),
              ),
            ),
            const Spacer(),
            PinPad(onDigit: _onDigit, onDelete: () => setState(() => _pin = _pin.isEmpty ? '' : _pin.substring(0, _pin.length - 1))),
            const SizedBox(height: AppSpacing.lg),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.page),
              child: _confirming
                  ? const SizedBox(height: 52)
                  : FilledButton(onPressed: _pin.length >= _minLength && !_saving ? _next : null, child: const Text('Continuer')),
            ),
            const SizedBox(height: AppSpacing.lg),
          ],
        ),
      ),
    );
  }
}
