import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/pin_check.dart';
import '../services/app_lock_service.dart';
import 'pin_pad.dart';

/// Écran de verrouillage, volontairement sobre : il ne dit rien de l'app.
class LockScreen extends StatefulWidget {
  const LockScreen({super.key});

  @override
  State<LockScreen> createState() => _LockScreenState();
}

class _LockScreenState extends State<LockScreen> {
  final _service = Get.find<AppLockService>();
  String _pin = '';
  String? _message;
  bool _error = false;
  bool _checking = false;
  Timer? _retry;

  int get _length => _service.pinLength.value ?? 4;

  @override
  void dispose() {
    _retry?.cancel();
    super.dispose();
  }

  Future<void> _onDigit(String digit) async {
    if (_checking || _pin.length >= _length) {
      return;
    }
    setState(() {
      _pin += digit;
      _error = false;
    });
    if (_pin.length == _length) {
      await _check();
    }
  }

  Future<void> _check() async {
    setState(() => _checking = true);
    final check = await _service.unlockWithPin(_pin);
    _checking = false;
    if (!mounted) {
      return;
    }
    switch (check) {
      case PinAccepted():
        break;
      case PinRejected(:final attemptsBeforeLockout):
        setState(() {
          _pin = '';
          _error = true;
          _message = attemptsBeforeLockout <= 2
              ? 'Code incorrect. Encore $attemptsBeforeLockout essai${attemptsBeforeLockout > 1 ? 's' : ''}.'
              : 'Code incorrect.';
        });
      case PinLockedOut(:final until):
        final wait = until.difference(DateTime.now());
        setState(() {
          _pin = '';
          _error = true;
          _message = 'Trop d\'essais. Réessaie dans ${wait.inSeconds + 1} secondes.';
        });
        _retry?.cancel();
        _retry = Timer(wait, () => mounted ? setState(() => _message = null) : null);
    }
  }

  @override
  Widget build(BuildContext context) {
    final biometric = _service.settings.value.biometricEnabled;

    return Material(
      color: AppColors.background,
      child: SafeArea(
        child: Column(
          children: [
            const Spacer(),
            const Icon(Icons.lock_outline_rounded, size: 32, color: AppColors.violet700),
            const SizedBox(height: AppSpacing.lg),
            const Text('Saisis ton code', style: AppTextStyles.title),
            const SizedBox(height: AppSpacing.xl),
            PinDots(length: _length, filled: _pin.length, error: _error),
            const SizedBox(height: AppSpacing.md),
            SizedBox(
              height: 40,
              child: _checking
                  ? const Center(child: SizedBox.square(dimension: 22, child: CircularProgressIndicator(strokeWidth: 2.4)))
                  : Text(
                      _message ?? '',
                      style: AppTextStyles.caption.copyWith(color: AppColors.sos),
                      textAlign: TextAlign.center,
                    ),
            ),
            const Spacer(),
            PinPad(
              onDigit: _onDigit,
              onDelete: () => setState(() => _pin = _pin.isEmpty ? '' : _pin.substring(0, _pin.length - 1)),
              extra: biometric
                  ? IconButton(
                      tooltip: 'Utiliser l\'empreinte ou le visage',
                      onPressed: _service.unlockWithBiometrics,
                      icon: const Icon(Icons.fingerprint_rounded, size: 32, color: AppColors.violet700),
                    )
                  : null,
            ),
            const SizedBox(height: AppSpacing.xxl),
          ],
        ),
      ),
    );
  }
}
