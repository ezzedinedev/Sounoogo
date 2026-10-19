import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:shared_preferences_platform_interface/in_memory_shared_preferences_async.dart';
import 'package:shared_preferences_platform_interface/shared_preferences_async_platform_interface.dart';
import 'package:sunoogo/app/modules/listening/domain/entities/message.dart';
import 'package:sunoogo/app/modules/listening/domain/entities/risk_level.dart';
import 'package:sunoogo/app/modules/listening/domain/entities/sent_message.dart';
import 'package:sunoogo/app/modules/listening/domain/repositories/listening_repository.dart';
import 'package:sunoogo/app/modules/listening/domain/usecases/send_voice_message.dart';
import 'package:sunoogo/app/modules/notifications/data/datasources/device_remote_data_source.dart';
import 'package:sunoogo/app/modules/notifications/data/datasources/local_notifications_data_source.dart';
import 'package:sunoogo/app/modules/notifications/data/datasources/push_data_source.dart';
import 'package:sunoogo/app/modules/notifications/data/repositories/notification_repository_impl.dart';
import 'package:sunoogo/app/modules/notifications/domain/entities/push_event.dart';
import 'package:sunoogo/app/modules/notifications/domain/entities/reminder_settings.dart';
import 'package:sunoogo/app/shared/data/models/user_model.dart';

class _MockListeningRepository extends Mock implements ListeningRepository {}

class _MockLocal extends Mock implements LocalNotificationsDataSource {}

class _MockPush extends Mock implements PushDataSource {}

class _MockDevices extends Mock implements DeviceRemoteDataSource {}

void main() {
  group('SendVoiceMessage', () {
    late _MockListeningRepository repository;
    final sent = SentMessage(
      message: Message(
        id: 1,
        kind: 'audio',
        body: 'Message vocal (0:42)',
        createdAt: DateTime(2026),
        audioUrl: 'https://api/a',
        audioDuration: 42,
      ),
      riskLevel: RiskLevel.none,
    );

    setUp(() {
      repository = _MockListeningRepository();
      when(() => repository.sendVoice(any(), any(), any())).thenAnswer((_) async => sent);
    });

    test('ignore un appui trop bref', () async {
      expect(await SendVoiceMessage(repository)(7, '/tmp/a.m4a', const Duration(milliseconds: 600)), isNull);
      verifyNever(() => repository.sendVoice(any(), any(), any()));
    });

    test('plafonne la durée à 2 minutes', () async {
      final result = await SendVoiceMessage(repository)(7, '/tmp/a.m4a', const Duration(minutes: 3));

      expect(result?.message.isVoice, isTrue);
      verify(() => repository.sendVoice(7, '/tmp/a.m4a', 120)).called(1);
    });
  });

  test('lit l\'écoutant préféré du profil', () {
    final user = UserModel.fromJson({
      'id': 1,
      'pseudonym': 'Baobab-204',
      'preferred_listener': {'display_name': 'Mariam', 'is_available': true},
    });

    expect(user.preferredListener?.displayName, 'Mariam');
    expect(UserModel.fromJson({'id': 2, 'pseudonym': 'Aube-1'}).preferredListener, isNull);
  });

  test('associe chaque notification à son écran', () {
    expect(PushTarget.fromData({'type': 'conversation', 'conversation_id': '3'}), isA<ConversationTarget>());
    expect(PushTarget.fromData({'type': 'checkin'}), isA<CheckInTarget>());
    expect(PushTarget.fromData({'type': 'inconnu'}), isNull);
  });

  group('rappel quotidien', () {
    late _MockLocal local;
    late NotificationRepositoryImpl repository;

    setUp(() {
      SharedPreferencesAsyncPlatform.instance = InMemorySharedPreferencesAsync.empty();
      local = _MockLocal();
      repository = NotificationRepositoryImpl(local, _MockPush(), _MockDevices());
      when(() => local.cancel(any())).thenAnswer((_) async {});
      when(
        () => local.scheduleDaily(
          hour: any(named: 'hour'),
          minute: any(named: 'minute'),
          title: any(named: 'title'),
          body: any(named: 'body'),
          data: any(named: 'data'),
        ),
      ).thenAnswer((_) async {});
    });

    test('n\'est pas activé si le membre refuse les notifications', () async {
      when(local.requestPermission).thenAnswer((_) async => false);

      expect(await repository.saveReminder(const ReminderSettings(enabled: true, hour: 21)), isFalse);
      expect((await repository.reminder()).enabled, isFalse);
    });

    test('est programmé à l\'heure choisie, avec un texte neutre', () async {
      when(local.requestPermission).thenAnswer((_) async => true);

      expect(await repository.saveReminder(const ReminderSettings(enabled: true, hour: 21, minute: 30)), isTrue);

      final saved = await repository.reminder();
      expect((saved.enabled, saved.hour, saved.minute), (true, 21, 30));
      final body =
          verify(
                () => local.scheduleDaily(
                  hour: 21,
                  minute: 30,
                  title: any(named: 'title'),
                  body: captureAny(named: 'body'),
                  data: {'type': 'checkin'},
                ),
              ).captured.single
              as String;
      expect(body.toLowerCase(), isNot(contains('humeur')));
    });
  });
}
