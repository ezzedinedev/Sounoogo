import '../../domain/entities/certificate.dart';
import '../../domain/entities/course.dart';
import '../../domain/entities/enrollment.dart';
import '../../domain/entities/learning_badge.dart';
import '../../domain/entities/learning_progress.dart';
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

abstract final class LearningBadgeModel {
  static LearningBadge fromJson(Map<String, dynamic> json) => LearningBadge(
    code: json['code'] as String,
    label: json['label'] as String,
    description: json['description'] as String,
    earned: json['earned'] as bool,
    awardedAt: DateTime.tryParse(json['awarded_at'] as String? ?? '')?.toLocal(),
  );
}

abstract final class CertificateModel {
  static Certificate fromJson(Map<String, dynamic> json) {
    final course = json['course'] as Map<String, dynamic>;
    return Certificate(
      code: json['code'] as String,
      holderName: json['holder_name'] as String,
      issuedAt: DateTime.parse(json['issued_at'] as String).toLocal(),
      courseSlug: course['slug'] as String,
      courseTitle: course['title'] as String,
      pdfUrl: json['pdf_url'] as String,
      verificationUrl: json['verification_url'] as String,
    );
  }
}

abstract final class LearningProgressModel {
  static LearningProgress fromJson(Map<String, dynamic> json) => LearningProgress(
    lessons: json['lessons'] as int,
    courses: json['courses'] as int,
    currentStreak: json['current_streak'] as int,
    bestStreak: json['best_streak'] as int,
    activeToday: json['active_today'] as bool,
    badges: (json['badges'] as List).map((badge) => LearningBadgeModel.fromJson(badge as Map<String, dynamic>)).toList(),
    certificates: (json['certificates'] as List)
        .map((certificate) => CertificateModel.fromJson(certificate as Map<String, dynamic>))
        .toList(),
  );
}
