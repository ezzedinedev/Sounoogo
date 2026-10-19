import 'package:flutter/material.dart';
import 'package:flutter_markdown_plus/flutter_markdown_plus.dart';
import 'package:get/get.dart';

import '../../../../core/error/failure.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/state_views.dart';
import '../../../emergency/presentation/widgets/emergency_sheet.dart';
import '../../domain/entities/guide.dart';
import '../controllers/guides_controller.dart';

class GuideScreen extends StatefulWidget {
  const GuideScreen({super.key});

  @override
  State<GuideScreen> createState() => _GuideScreenState();
}

class _GuideScreenState extends State<GuideScreen> {
  final _controller = Get.find<GuidesController>();
  late Future<Guide> _guide = _controller.read(Get.parameters['slug']!);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        actions: const [
          SosButton(),
          SizedBox(width: AppSpacing.page),
        ],
      ),
      body: FutureBuilder<Guide>(
        future: _guide,
        builder: (context, snapshot) {
          if (snapshot.hasError) {
            final error = snapshot.error;
            return ErrorState(
              message: error is Failure ? error.message : 'Impossible d\'ouvrir ce guide.',
              onRetry: () => setState(() => _guide = _controller.read(Get.parameters['slug']!)),
            );
          }
          final guide = snapshot.data;
          if (guide == null) {
            return const SkeletonList(count: 3);
          }
          return ListView(
            padding: const EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, AppSpacing.xxxl),
            children: [
              Text(guide.title, style: AppTextStyles.display.copyWith(fontSize: 30)),
              const SizedBox(height: AppSpacing.sm),
              Text('${guide.readingMinutes} min de lecture', style: AppTextStyles.caption.copyWith(color: AppColors.violet700)),
              const SizedBox(height: AppSpacing.xl),
              MarkdownBody(
                data: guide.body ?? '',
                styleSheet: MarkdownStyleSheet(
                  p: AppTextStyles.body.copyWith(fontSize: 16, height: 1.6),
                  h2: AppTextStyles.subtitle.copyWith(fontSize: 19),
                  h2Padding: const EdgeInsets.only(top: AppSpacing.lg),
                  listBullet: AppTextStyles.body.copyWith(color: AppColors.violet700),
                  strong: AppTextStyles.body.copyWith(fontWeight: FontWeight.w700, fontSize: 16),
                  blockSpacing: AppSpacing.md,
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}
