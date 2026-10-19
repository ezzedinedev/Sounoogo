import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/pin_check.dart';
import '../services/app_lock_service.dart';

/// Calculatrice qui fonctionne vraiment, affichée par la sortie rapide.
/// Taper son code puis « = » ramène dans l'app. Aucun élément ne rappelle SUNOOGO.
class DecoyCalculator extends StatefulWidget {
  const DecoyCalculator({super.key});

  @override
  State<DecoyCalculator> createState() => _DecoyCalculatorState();
}

class _DecoyCalculatorState extends State<DecoyCalculator> {
  final _service = Get.find<AppLockService>();
  final _format = NumberFormat('#,##0.########', 'fr_FR');

  String _input = '0';
  double? _accumulator;
  String? _operator;
  bool _fresh = true;

  void _digit(String digit) => setState(() {
    if (_fresh || _input == '0') {
      _input = digit == ',' ? '0,' : digit;
      _fresh = false;
    } else if (_input.length < 12 && !(digit == ',' && _input.contains(','))) {
      _input += digit;
    }
  });

  double get _value => double.tryParse(_input.replaceAll(',', '.')) ?? 0;

  void _apply(String operator) => setState(() {
    _accumulator = _operator == null ? _value : _compute();
    _input = _show(_accumulator!);
    _operator = operator;
    _fresh = true;
  });

  double _compute() {
    final a = _accumulator ?? 0;
    final b = _value;
    return switch (_operator) {
      '+' => a + b,
      '−' => a - b,
      '×' => a * b,
      '÷' => b == 0 ? double.nan : a / b,
      _ => b,
    };
  }

  String _show(double value) => value.isNaN || value.isInfinite ? 'Erreur' : _format.format(value);

  Future<void> _equals() async {
    // Code saisi puis « = » : retour discret dans l'app, même si une opération était en cours.
    final digitsOnly = RegExp(r'^\d+$').hasMatch(_input);
    if (!_fresh && digitsOnly && _input.length == _service.pinLength.value) {
      final check = await _service.unlockWithPin(_input);
      if (check is PinAccepted) {
        return;
      }
    }
    setState(() {
      if (_operator != null) {
        _input = _show(_compute());
      }
      _accumulator = null;
      _operator = null;
      _fresh = true;
    });
  }

  void _clear() => setState(() {
    _input = '0';
    _accumulator = null;
    _operator = null;
    _fresh = true;
  });

  @override
  Widget build(BuildContext context) {
    Widget key(String label, {Color color = AppColors.decoyDigit, Color text = AppColors.white, VoidCallback? onTap, int flex = 1}) =>
        Expanded(
          flex: flex,
          child: Padding(
            padding: const EdgeInsets.all(6),
            child: Material(
              color: color,
              borderRadius: BorderRadius.circular(40),
              child: InkWell(
                borderRadius: BorderRadius.circular(40),
                onTap: onTap ?? () => _digit(label),
                child: SizedBox(
                  height: 76,
                  child: Center(
                    child: Text(label, style: AppTextStyles.decoyKey.copyWith(color: text)),
                  ),
                ),
              ),
            ),
          ),
        );

    return Material(
      color: AppColors.decoyBackground,
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Column(
            children: [
              Expanded(
                child: Align(
                  alignment: Alignment.bottomRight,
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    child: FittedBox(child: Text(_input, style: AppTextStyles.decoyDisplay)),
                  ),
                ),
              ),
              Row(
                children: [
                  key('C', color: AppColors.decoyFunction, text: AppColors.decoyBackground, onTap: _clear),
                  key(
                    '±',
                    color: AppColors.decoyFunction,
                    text: AppColors.decoyBackground,
                    onTap: () => setState(() => _input = _show(-_value)),
                  ),
                  key(
                    '%',
                    color: AppColors.decoyFunction,
                    text: AppColors.decoyBackground,
                    onTap: () => setState(() => _input = _show(_value / 100)),
                  ),
                  key('÷', color: AppColors.decoyOperator, onTap: () => _apply('÷')),
                ],
              ),
              Row(
                children: [
                  key('7'),
                  key('8'),
                  key('9'),
                  key('×', color: AppColors.decoyOperator, onTap: () => _apply('×')),
                ],
              ),
              Row(
                children: [
                  key('4'),
                  key('5'),
                  key('6'),
                  key('−', color: AppColors.decoyOperator, onTap: () => _apply('−')),
                ],
              ),
              Row(
                children: [
                  key('1'),
                  key('2'),
                  key('3'),
                  key('+', color: AppColors.decoyOperator, onTap: () => _apply('+')),
                ],
              ),
              Row(
                children: [
                  key('0', flex: 2),
                  key(','),
                  key('=', color: AppColors.decoyOperator, onTap: _equals),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
