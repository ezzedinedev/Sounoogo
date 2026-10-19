import 'dart:async';
import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:flutter_timezone/flutter_timezone.dart';
import 'package:timezone/data/latest_all.dart' as tz;
import 'package:timezone/timezone.dart' as tz;

/// Notifications affichées par le téléphone lui-même (rappels, messages reçus app ouverte).
class LocalNotificationsDataSource {
  LocalNotificationsDataSource({FlutterLocalNotificationsPlugin? plugin}) : _plugin = plugin ?? FlutterLocalNotificationsPlugin();

  static const dailyReminderId = 1;

  static const _messages = AndroidNotificationDetails(
    'sunoogo_messages',
    'Messages',
    channelDescription: 'Réponses des écoutants et rappels',
    importance: Importance.high,
    priority: Priority.high,
    // Rien n'apparaît sur l'écran verrouillé au-delà du titre.
    visibility: NotificationVisibility.private,
  );

  final FlutterLocalNotificationsPlugin _plugin;
  final _taps = StreamController<Map<String, dynamic>>.broadcast();
  bool _ready = false;

  Stream<Map<String, dynamic>> get taps => _taps.stream;

  Future<void> initialize() async {
    if (_ready || kIsWeb) {
      return;
    }
    tz.initializeTimeZones();
    try {
      tz.setLocalLocation(tz.getLocation((await FlutterTimezone.getLocalTimezone()).identifier));
    } catch (_) {
      tz.setLocalLocation(tz.UTC);
    }
    await _plugin.initialize(
      settings: const InitializationSettings(
        android: AndroidInitializationSettings('@mipmap/ic_launcher'),
        iOS: DarwinInitializationSettings(requestAlertPermission: false, requestBadgePermission: false, requestSoundPermission: false),
      ),
      onDidReceiveNotificationResponse: (response) => _emit(response.payload),
    );
    final launch = await _plugin.getNotificationAppLaunchDetails();
    if (launch?.didNotificationLaunchApp ?? false) {
      _emit(launch!.notificationResponse?.payload);
    }
    _ready = true;
  }

  void _emit(String? payload) {
    if (payload != null) {
      _taps.add(jsonDecode(payload) as Map<String, dynamic>);
    }
  }

  Future<bool> requestPermission() async {
    if (kIsWeb) {
      return false;
    }
    final android = _plugin.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
    final ios = _plugin.resolvePlatformSpecificImplementation<IOSFlutterLocalNotificationsPlugin>();
    return await android?.requestNotificationsPermission() ?? await ios?.requestPermissions(alert: true, sound: true) ?? false;
  }

  Future<void> scheduleDaily({
    required int hour,
    required int minute,
    required String title,
    required String body,
    required Map<String, String> data,
  }) async {
    final now = tz.TZDateTime.now(tz.local);
    var next = tz.TZDateTime(tz.local, now.year, now.month, now.day, hour, minute);
    if (!next.isAfter(now)) {
      next = next.add(const Duration(days: 1));
    }
    await _plugin.zonedSchedule(
      id: dailyReminderId,
      title: title,
      body: body,
      scheduledDate: next,
      notificationDetails: const NotificationDetails(android: _messages, iOS: DarwinNotificationDetails()),
      // Inexact : pas besoin de la permission « alarmes exactes », quelques minutes d'écart suffisent.
      androidScheduleMode: AndroidScheduleMode.inexactAllowWhileIdle,
      matchDateTimeComponents: DateTimeComponents.time,
      payload: jsonEncode(data),
    );
  }

  Future<void> cancel(int id) async {
    if (!kIsWeb) {
      await _plugin.cancel(id: id);
    }
  }

  Future<void> show({required String title, required String body, required Map<String, dynamic> data}) async {
    if (kIsWeb) {
      return;
    }
    await _plugin.show(
      id: DateTime.now().millisecondsSinceEpoch ~/ 1000 % 100000,
      title: title,
      body: body,
      notificationDetails: const NotificationDetails(android: _messages, iOS: DarwinNotificationDetails()),
      payload: jsonEncode(data),
    );
  }
}
