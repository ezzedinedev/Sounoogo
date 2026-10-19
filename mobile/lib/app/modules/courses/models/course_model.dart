class LessonModel {
  const LessonModel({required this.id, required this.title, required this.position, required this.durationMinutes, required this.isPreview, this.content, this.videoUrl});

  factory LessonModel.fromJson(Map<String, dynamic> json) => LessonModel(
        id: json['id'] as int,
        title: json['title'] as String,
        position: json['position'] as int,
        durationMinutes: json['duration_minutes'] as int,
        isPreview: json['is_preview'] as bool? ?? false,
        content: json['content'] as String?,
        videoUrl: json['video_url'] as String?,
      );

  final int id;
  final String title;
  final int position;
  final int durationMinutes;
  final bool isPreview;
  final String? content;
  final String? videoUrl;
}

class CourseModel {
  const CourseModel({
    required this.slug,
    required this.title,
    required this.summary,
    required this.levelLabel,
    required this.price,
    required this.includedInSubscription,
    this.description,
    this.coverUrl,
    this.lessonsCount,
    this.durationMinutes,
    this.lessons = const [],
  });

  factory CourseModel.fromJson(Map<String, dynamic> json) => CourseModel(
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

  final String slug;
  final String title;
  final String summary;
  final String levelLabel;
  final int price;
  final bool includedInSubscription;
  final String? description;
  final String? coverUrl;
  final int? lessonsCount;
  final int? durationMinutes;
  final List<LessonModel> lessons;

  bool get isFree => price == 0;
}

class EnrollmentModel {
  const EnrollmentModel({required this.courseSlug, required this.progress, required this.completedLessonIds});

  factory EnrollmentModel.fromJson(Map<String, dynamic> json) => EnrollmentModel(
        courseSlug: (json['course'] as Map<String, dynamic>)['slug'] as String,
        progress: json['progress'] as int,
        completedLessonIds: (json['completed_lesson_ids'] as List).cast<int>(),
      );

  final String courseSlug;
  final int progress;
  final List<int> completedLessonIds;
}
