import 'package:flutter/material.dart';

/// Palette SUNOOGO : violet premium et blanc. Seule source de couleurs de l'app.
abstract final class AppColors {
  // Violet de marque
  static const violet50 = Color(0xFFF8F5FF);
  static const violet100 = Color(0xFFEFE8FF);
  static const violet200 = Color(0xFFDCCDFB);
  static const violet300 = Color(0xFFB48CF2);
  static const violet500 = Color(0xFF7442D7);
  static const violet700 = Color(0xFF602FB8);
  static const violet800 = Color(0xFF4C2494);
  static const violet950 = Color(0xFF210E40);
  static const lilac200 = Color(0xFFEBD6FB);

  // Neutres
  static const white = Color(0xFFFFFFFF);
  static const background = Color(0xFFFBFAFE);
  static const surface = Color(0xFFFFFFFF);
  static const surfaceMuted = Color(0xFFF4F2F8);
  static const border = Color(0xFFE7E3EF);
  static const textPrimary = Color(0xFF1C1430);
  static const textSecondary = Color(0xFF6B6480);
  static const textMuted = Color(0xFF9A94AB);

  // Sémantique
  static const success = Color(0xFF16A34A);
  static const successSoft = Color(0xFFE8F7EE);
  static const warning = Color(0xFFB45309);
  static const warningSoft = Color(0xFFFFF7E6);

  /// Rouge d'urgence : réservé au SOS, jamais décoratif.
  static const sos = Color(0xFFC81E1E);
  static const sosSoft = Color(0xFFFDEDED);

  static const brandGradient = LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: [violet500, violet800]);
}
