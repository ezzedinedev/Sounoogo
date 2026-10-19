import 'package:flutter/material.dart';

import 'app_colors.dart';

/// Typographie : Rethink Sans pour le texte, Instrument Serif italique pour les accents
/// (même système que le site vitrine).
abstract final class AppTextStyles {
  static const _sans = 'RethinkSans';
  static const _serif = 'InstrumentSerif';

  static const display = TextStyle(
    fontFamily: _sans,
    fontSize: 34,
    height: 1.08,
    fontWeight: FontWeight.w500,
    letterSpacing: -0.6,
    color: AppColors.textPrimary,
  );
  static const displayAccent = TextStyle(
    fontFamily: _serif,
    fontSize: 38,
    height: 1.05,
    fontStyle: FontStyle.italic,
    color: AppColors.violet700,
  );
  static const title = TextStyle(
    fontFamily: _sans,
    fontSize: 22,
    height: 1.2,
    fontWeight: FontWeight.w600,
    letterSpacing: -0.3,
    color: AppColors.textPrimary,
  );
  static const titleAccent = TextStyle(
    fontFamily: _serif,
    fontSize: 25,
    height: 1.1,
    fontStyle: FontStyle.italic,
    color: AppColors.violet700,
  );
  static const subtitle = TextStyle(
    fontFamily: _sans,
    fontSize: 17,
    height: 1.3,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );
  static const body = TextStyle(fontFamily: _sans, fontSize: 15, height: 1.5, color: AppColors.textPrimary);
  static const bodySecondary = TextStyle(fontFamily: _sans, fontSize: 15, height: 1.5, color: AppColors.textSecondary);
  static const label = TextStyle(fontFamily: _sans, fontSize: 14, height: 1.3, fontWeight: FontWeight.w600, color: AppColors.textPrimary);
  static const caption = TextStyle(fontFamily: _sans, fontSize: 12.5, height: 1.4, color: AppColors.textSecondary);
  static const button = TextStyle(fontFamily: _sans, fontSize: 15.5, fontWeight: FontWeight.w600, letterSpacing: 0.1);

  /// Calculatrice de la sortie rapide : police système, sans rappel de la marque.
  static const decoyDisplay = TextStyle(fontSize: 76, color: AppColors.white, fontWeight: FontWeight.w300);
  static const decoyKey = TextStyle(fontSize: 30, color: AppColors.white);

  static const figure = TextStyle(fontFamily: _serif, fontSize: 40, height: 1, fontStyle: FontStyle.italic, color: AppColors.textPrimary);
}
