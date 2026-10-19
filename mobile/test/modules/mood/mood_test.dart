import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/modules/mood/data/models/mood_journal_model.dart';
import 'package:sunoogo/app/modules/mood/domain/entities/mood_journal.dart';
import 'package:sunoogo/app/modules/mood/domain/entities/mood_level.dart';
import 'package:sunoogo/app/modules/mood/domain/entities/mood_summary.dart';
import 'package:sunoogo/app/modules/mood/domain/repositories/mood_repository.dart';
import 'package:sunoogo/app/modules/mood/domain/usecases/record_mood.dart';

class _MockMoodRepository extends Mock implements MoodRepository {}

void main() {
  setUpAll(() => registerFallbackValue(MoodLevel.neutral));

  test('le journal de l\'API devient des entités typées', () {
    final journal = MoodJournalModel.fromJson({
      'data': [
        {
          'date': '2026-10-19',
          'mood': 2,
          'mood_label': 'Difficile',
          'feelings': ['tired'],
          'note': null,
        },
      ],
      'summary': {'average': 2.0, 'streak': 1, 'low_streak': 1, 'checked_in_today': false, 'suggestion': 'exercise'},
    });

    expect(journal.entries.single.level, MoodLevel.low);
    expect(journal.entries.single.level.isLow, isTrue);
    expect(journal.summary.suggestion, MoodSuggestion.exercise);
  });

  test('une suggestion inconnue est ignorée', () {
    expect(MoodSuggestion.fromValue('meditate'), MoodSuggestion.none);
  });

  test('RecordMood dédoublonne les émotions, en garde 5 au plus et ignore une note vide', () async {
    final repository = _MockMoodRepository();
    const empty = MoodJournal(entries: [], summary: MoodSummary.empty);
    when(
      () => repository.record(
        any(),
        feelings: any(named: 'feelings'),
        note: any(named: 'note'),
      ),
    ).thenAnswer((_) async => empty);

    await RecordMood(repository)(MoodLevel.low, feelings: ['sad', 'sad', 'tired', 'lonely', 'anxious', 'stressed', 'angry'], note: '  ');

    verify(() => repository.record(MoodLevel.low, feelings: ['sad', 'tired', 'lonely', 'anxious', 'stressed'], note: null)).called(1);
  });
}
