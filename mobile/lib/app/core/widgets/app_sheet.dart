import 'package:flutter/material.dart';
import 'package:get/get.dart';

/// Feuille modale de l'app : passe par `showModalBottomSheet` pour appliquer tout le thème
/// (fond, coins arrondis, poignée), ce que `Get.bottomSheet` ne fait pas.
abstract final class AppSheet {
  static Future<T?> show<T>(Widget child) =>
      showModalBottomSheet<T>(context: Get.context!, isScrollControlled: true, useSafeArea: true, builder: (_) => child);
}
