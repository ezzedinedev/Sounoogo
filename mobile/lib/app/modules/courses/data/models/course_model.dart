import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/lesson.dart';

abstract final class LessonModel {
  static Lesson fromJson(Map<String, dynamic> json) => Lesson(
    id: json['id'] as int,
    title: json['title'] as String,
    position: json['position'] as int,
    durationMinutes: json['duration_minutes'] as int,
    isPreview: json['is_preview'] as bool? ?? false,
    content: json['content'] as String?,
    videoUrl: json['video_url'] as String?,
  );
}

abstract final class CourseModel {
  static Course fromJson(Map<String, dynamic> json) => Course(
    slug: json['slug'] as String,
    title: json['title'] as String,
    summary: json['summary'] as String,
    levelLabel: json['level_label'] as String? ?? '',
    price: json['price'] as int,
    includedInSubscription: json['included_in_subscription'] as bool? ?? false,
    description: json['description'] as String?,
    coverUrl: json['cover_url'] as String?,
    lessonsCount: json['lessons_count'] as int?,
    durationMinutes: (json['duration_minutes'] as num?)?.toInt(),
    lessons: (json['lessons'] as List? ?? const []).map((l) => LessonModel.fromJson(l as Map<String, dynamic>)).toList(),
  );
}

abstract final class EnrollmentModel {
  static Enrollment fromJson(Map<String, dynamic> json) => Enrollment(
    courseSlug: (json['course'] as Map<String, dynamic>)['slug'] as String,
    progress: json['progress'] as int,
    completedLessonIds: (json['completed_lesson_ids'] as List).cast<int>(),
  );
}
