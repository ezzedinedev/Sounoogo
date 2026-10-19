import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/core/error/failure.dart';
import 'package:sunoogo/app/core/network/api_provider.dart';
import 'package:sunoogo/app/modules/bookings/data/models/booking_model.dart';
import 'package:sunoogo/app/modules/bookings/domain/entities/booking.dart';
import 'package:sunoogo/app/modules/bookings/domain/repositories/booking_repository.dart';
import 'package:sunoogo/app/modules/bookings/domain/usecases/join_video_consultation.dart';
import 'package:sunoogo/app/modules/courses/data/datasources/course_remote_data_source.dart';
import 'package:sunoogo/app/modules/courses/data/models/course_model.dart';
import 'package:sunoogo/app/modules/courses/domain/repositories/course_repository.dart';
import 'package:sunoogo/app/modules/courses/domain/usecases/issue_certificate.dart';

class _MockApiProvider extends Mock implements ApiProvider {}

class _MockCourseRepository extends Mock implements CourseRepository {}

class _MockBookingRepository extends Mock implements BookingRepository {}

const _badge = {'code': 'first_lesson', 'label': 'Premier pas', 'description': '…', 'earned': true, 'awarded_at': '2026-10-19T10:00:00+00:00'};
const _certificate = {
  'code': 'ABCD-EFGH-JKLM',
  'holder_name': 'Awa Ouédraogo',
  'issued_at': '2026-10-19T10:00:00+00:00',
  'course': {'slug': 'confiance', 'title': 'Reprendre confiance'},
  'pdf_url': 'https://api/certificates/ABCD-EFGH-JKLM/pdf',
  'verification_url': 'https://sunoogo/attestations/ABCD-EFGH-JKLM',
};

void main() {
  group('Progression', () {
    test('lit la série, les jalons et retrouve l\'attestation d\'une formation', () {
      final progress = LearningProgressModel.fromJson({
        'lessons': 4,
        'courses': 1,
        'current_streak': 2,
        'best_streak': 3,
        'active_today': true,
        'badges': [_badge, {..._badge, 'code': 'streak_7', 'earned': false, 'awarded_at': null}],
        'certificates': [_certificate],
      });

      expect(progress.earnedCount, 1);
      expect(progress.badges.last.awardedAt, isNull);
      expect(progress.certificateFor('confiance')?.code, 'ABCD-EFGH-JKLM');
      expect(progress.certificateFor('autre'), isNull);
    });

    test('remonte les jalons obtenus en terminant une leçon', () async {
      final api = _MockApiProvider();
      when(() => api.post('/courses/confiance/lessons/3/complete')).thenAnswer(
        (_) async => {
          'data': {'progress': 100, 'completed_lesson_ids': [1, 3], 'course': {'slug': 'confiance'}},
          'new_badges': [_badge],
        },
      );

      final completion = await CourseRemoteDataSource(api).complete('confiance', 3);

      expect(completion.enrollment.isCompleted, isTrue);
      expect(completion.newBadges.single.label, 'Premier pas');
    });
  });

  group('IssueCertificate', () {
    late _MockCourseRepository repository;

    setUp(() {
      repository = _MockCourseRepository();
      when(() => repository.issueCertificate(any(), any())).thenAnswer((_) async => CertificateModel.fromJson(_certificate));
    });

    test('nettoie les espaces du nom avant l\'envoi', () async {
      await IssueCertificate(repository)('confiance', '  Awa   Ouédraogo ');

      verify(() => repository.issueCertificate('confiance', 'Awa Ouédraogo')).called(1);
    });

    test('refuse un nom vide ou fantaisiste sans appeler le serveur', () {
      for (final name in [' ', 'A', '<script>', '0612345678']) {
        expect(() => IssueCertificate(repository)('confiance', name), throwsA(isA<ValidationFailure>()));
      }
      verifyNever(() => repository.issueCertificate(any(), any()));
    });
  });

  group('Téléconsultation', () {
    final booking = BookingModel.fromJson({
      'id': 9,
      'status': 'confirmed',
      'status_label': 'Confirmée',
      'mode_label': 'Visio',
      'price': 15000,
      'slot': {'starts_at': '2026-10-20T10:00:00+00:00'},
      'video': {'opens_at': '2026-10-20T09:50:00+00:00', 'closes_at': '2026-10-20T11:20:00+00:00', 'is_open': false},
    });
    late _MockBookingRepository repository;

    setUp(() {
      repository = _MockBookingRepository();
      when(() => repository.videoLink(9)).thenAnswer((_) async => Uri.parse('https://visio.example/sunoogo-abc'));
    });

    test('ne demande pas de lien avant l\'ouverture de la salle', () async {
      final join = JoinVideoConsultation(repository, clock: () => DateTime.utc(2026, 10, 20, 9, 45));

      expect(await join(booking), isNull);
      verifyNever(() => repository.videoLink(any()));
    });

    test('donne le lien pendant la fenêtre d\'ouverture', () async {
      final join = JoinVideoConsultation(repository, clock: () => DateTime.utc(2026, 10, 20, 10, 5));

      expect(await join(booking), Uri.parse('https://visio.example/sunoogo-abc'));
    });

    test('ignore les consultations sans visio', () async {
      const inPerson = Booking(id: 2, status: 'confirmed', statusLabel: 'Confirmée', modeLabel: 'Présentiel', price: 0);

      expect(await JoinVideoConsultation(repository)(inPerson), isNull);
    });
  });
}
