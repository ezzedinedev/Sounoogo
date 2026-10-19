import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:sunoogo/app/core/error/failure.dart';
import 'package:sunoogo/app/core/network/api_exception.dart';
import 'package:sunoogo/app/core/storage/json_cache.dart';
import 'package:sunoogo/app/modules/directory/data/models/support_resource_model.dart';
import 'package:sunoogo/app/modules/exercises/data/datasources/exercise_audio_data_source.dart';
import 'package:sunoogo/app/modules/exercises/data/datasources/exercise_remote_data_source.dart';
import 'package:sunoogo/app/modules/exercises/data/repositories/exercise_repository_impl.dart';
import 'package:sunoogo/app/modules/guides/data/datasources/guide_remote_data_source.dart';
import 'package:sunoogo/app/modules/guides/data/repositories/guide_repository_impl.dart';

class _MemoryJsonCache extends Fake implements JsonCache {
  final data = <String, Object>{};

  @override
  Future<Object?> read(String key) async => data[key];

  @override
  Future<void> write(String key, Object value) async => data[key] = value;
}

class _MockExerciseRemote extends Mock implements ExerciseRemoteDataSource {}

class _MockGuideRemote extends Mock implements GuideRemoteDataSource {}

void main() {
  const breathing = {
    'slug': 'respiration-apaisante',
    'title': 'Respiration apaisante',
    'summary': '4-2-6',
    'category': 'breathing',
    'category_label': 'Respiration',
    'duration_minutes': 2,
    'steps': <Object>[],
    'breathing': {'inhale': 4, 'hold': 2, 'exhale': 6, 'rest': 0, 'cycles': 10},
    'audio_url': null,
  };

  group('ExerciseRepositoryImpl', () {
    late _MockExerciseRemote remote;
    late _MemoryJsonCache cache;
    late ExerciseRepositoryImpl repository;

    setUp(() {
      remote = _MockExerciseRemote();
      cache = _MemoryJsonCache();
      repository = ExerciseRepositoryImpl(remote, cache, ExerciseAudioDataSource());
    });

    test('enregistre les exercices pour un usage hors ligne', () async {
      when(remote.fetch).thenAnswer((_) async => [breathing]);

      final library = await repository.library();

      expect(library.fromCache, isFalse);
      expect(library.exercises.single.breathing!.total, const Duration(seconds: 120));
      expect(cache.data['exercises'], isNotNull);
    });

    test('hors ligne, reprend la dernière copie', () async {
      cache.data['exercises'] = [breathing];
      when(remote.fetch).thenThrow(const ApiException('hors ligne'));

      final library = await repository.library();

      expect(library.fromCache, isTrue);
      expect(library.find('respiration-apaisante')?.isBreathing, isTrue);
    });

    test('hors ligne sans copie, l\'échec réseau remonte', () async {
      when(remote.fetch).thenThrow(const ApiException('hors ligne'));

      await expectLater(repository.library(), throwsA(isA<NetworkFailure>()));
    });
  });

  test('un guide déjà ouvert reste lisible hors ligne', () async {
    final remote = _MockGuideRemote();
    final repository = GuideRepositoryImpl(remote, _MemoryJsonCache());
    when(() => remote.find('ecouter')).thenAnswer((_) async => {'slug': 'ecouter', 'title': 'Écouter', 'summary': '…', 'reading_minutes': 2, 'body': '## Écouter'});
    await repository.read('ecouter');

    when(() => remote.find('ecouter')).thenThrow(const ApiException('hors ligne'));

    expect((await repository.read('ecouter')).body, '## Écouter');
  });

  test('l\'annuaire distingue les ressources placées sur la carte', () {
    final directory = ResourceDirectoryModel.fromJson({
      'data': [
        {'id': 1, 'name': 'Centre A', 'category': 'health_center', 'category_label': 'Centre de santé', 'city': 'Ouagadougou', 'latitude': 12.37, 'longitude': -1.52},
        {'id': 2, 'name': 'Association B', 'category': 'association', 'category_label': 'Association', 'city': 'Ouagadougou'},
      ],
      'cities': ['Ouagadougou'],
      'categories': [
        {'value': 'association', 'label': 'Association'},
      ],
    });

    expect(directory.resources.where((r) => r.isOnMap).map((r) => r.name), ['Centre A']);
    expect(directory.categories.single.label, 'Association');
  });
}
