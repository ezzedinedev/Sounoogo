import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/modules/listening/domain/entities/message.dart';
import 'package:sunoogo/app/modules/listening/domain/entities/risk_level.dart';
import 'package:sunoogo/app/modules/listening/domain/entities/sent_message.dart';
import 'package:sunoogo/app/modules/listening/domain/repositories/listening_repository.dart';
import 'package:sunoogo/app/modules/listening/domain/usecases/send_message.dart';

class _MockListeningRepository extends Mock implements ListeningRepository {}

void main() {
  late _MockListeningRepository repository;

  setUp(() => repository = _MockListeningRepository());

  test("un message vide n'est pas envoyé", () async {
    expect(await SendMessage(repository)(7, '   \n'), isNull);
    verifyZeroInteractions(repository);
  });

  test('le texte est nettoyé avant envoi', () async {
    final sent = SentMessage(
      message: Message(id: 1, kind: 'text', body: 'Je me sens seul', createdAt: DateTime(2026)),
      riskLevel: RiskLevel.high,
    );
    when(() => repository.send(7, 'Je me sens seul')).thenAnswer((_) async => sent);

    final result = await SendMessage(repository)(7, '  Je me sens seul  ');

    expect(result?.needsEmergencyHelp, isTrue);
  });

  test('un niveau de risque inconnu est traité comme absent', () {
    expect(RiskLevel.fromValue('critical'), RiskLevel.none);
    expect(RiskLevel.fromValue('high'), RiskLevel.high);
  });
}
