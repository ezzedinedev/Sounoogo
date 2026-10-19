import 'dart:async';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';

import '../../../../core/constants/push_config.dart';

/// Firebase Cloud Messaging : jeton de l'appareil et messages reçus.
class PushDataSource {
  bool _ready = false;

  bool get available => PushConfig.enabled && !kIsWeb;

  Future<bool> _initialize() async {
    if (!available) {
      return false;
    }
    if (!_ready) {
      await Firebase.initializeApp(
        options: const FirebaseOptions(
          apiKey: PushConfig.apiKey,
          appId: PushConfig.appId,
          messagingSenderId: PushConfig.senderId,
          projectId: PushConfig.projectId,
        ),
      );
      _ready = true;
    }
    return true;
  }

  /// Jeton de l'appareil, après accord du membre ; null si refusé ou non configuré.
  Future<String?> token() async {
    if (!await _initialize()) {
      return null;
    }
    final settings = await FirebaseMessaging.instance.requestPermission();
    if (settings.authorizationStatus == AuthorizationStatus.denied) {
      return null;
    }
    // Jeton d'enregistrement attendu par l'API HTTP v1 (champ `message.token`) côté serveur.
    // ignore: deprecated_member_use
    return FirebaseMessaging.instance.getToken();
  }

  // ignore: deprecated_member_use
  Future<String?> currentToken() async => await _initialize() ? FirebaseMessaging.instance.getToken() : null;

  Future<void> deleteToken() async {
    if (await _initialize()) {
      // ignore: deprecated_member_use
      await FirebaseMessaging.instance.deleteToken();
    }
  }

  /// Messages reçus pendant que l'app est ouverte (le système ne les affiche pas lui-même).
  Stream<RemoteMessage> foreground() async* {
    if (await _initialize()) {
      yield* FirebaseMessaging.onMessage;
    }
  }

  /// Notifications touchées : app en arrière-plan, ou lancée par la notification.
  Stream<Map<String, dynamic>> opened() async* {
    if (!await _initialize()) {
      return;
    }
    final initial = await FirebaseMessaging.instance.getInitialMessage();
    if (initial != null) {
      yield initial.data;
    }
    yield* FirebaseMessaging.onMessageOpenedApp.map((message) => message.data);
  }
}
