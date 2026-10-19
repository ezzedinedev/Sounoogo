import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/widgets/accent_title.dart';
import '../../../../core/widgets/fade_slide_in.dart';
import '../../../../core/widgets/state_views.dart';
import '../../domain/entities/feeling.dart';
import '../../domain/entities/mood_entry.dart';
import '../../domain/entities/mood_journal.dart';
import '../../domain/entities/mood_level.dart';
import '../controllers/mood_controller.dart';
import '../widgets/mood_check_in_sheet.dart';
import '../widgets/mood_face.dart';

/// Journal d'humeur : courbe des 30 derniers jours et historique.
class MoodScreen extends GetView<MoodController> {
  const MoodScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Mon humeur')),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => MoodCheckInSheet.show(controller.journal.value?.today?.level ?? MoodLevel.neutral),
        icon: const Icon(Icons.add_rounded),
        label: const Text('Check-in'),
      ),
      body: Obx(() {
        final journal = controller.journal.value;
        if (journal == null) {
          return controller.errorMessage.value != null
              ? ErrorState(message: controller.errorMessage.value!, onRetry: controller.load)
              : const SkeletonList();
        }
        return RefreshIndicator(
          onRefresh: controller.load,
          child: ListView(
            padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.md, AppSpacing.page, 100),
            children: [
              const AccentTitle('Prendre soin de', accent: 'soi'),
              const SizedBox(height: AppSpacing.xs),
              const Text("Ton journal est privé : personne d'autre ne le lit.", style: AppTextStyles.bodySecondary),
              const SizedBox(height: AppSpacing.xl),
              FadeSlideIn(index: 0, child: _Summary(journal)),
              const SizedBox(height: AppSpacing.lg),
              FadeSlideIn(index: 1, child: _Chart(journal)),
              const SizedBox(height: AppSpacing.xl),
              if (journal.entries.isEmpty)
                const EmptyState(
                  icon: Icons.sentiment_satisfied_alt_outlined,
                  title: 'Ton journal est vide',
                  message: 'Un check-in par jour suffit pour voir ton humeur évoluer.',
                )
              else ...[
                const Text('Historique', style: AppTextStyles.subtitle),
                const SizedBox(height: AppSpacing.md),
                for (final (index, entry) in journal.entries.reversed.indexed) FadeSlideIn(index: index + 2, child: _EntryTile(entry)),
              ],
            ],
          ),
        );
      }),
    );
  }
}

class _Summary extends StatelessWidget {
  const _Summary(this.journal);

  final MoodJournal journal;

  @override
  Widget build(BuildContext context) {
    final summary = journal.summary;
    final average = summary.average;
    final items = [
      (average == null ? '–' : NumberFormat('0.0', 'fr_FR').format(average), 'humeur moyenne'),
      ('${summary.streak}', summary.streak > 1 ? 'jours de suite' : 'jour de suite'),
      ('${journal.entries.length}', 'check-ins · 30 j'),
    ];

    return Row(
      children: [
        for (final (index, (figure, label)) in items.indexed) ...[
          if (index > 0) const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Container(
              padding: const EdgeInsets.symmetric(vertical: AppSpacing.lg, horizontal: AppSpacing.sm),
              decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusMd)),
              child: Column(
                children: [
                  Text(figure, style: AppTextStyles.figure.copyWith(fontSize: 32)),
                  const SizedBox(height: AppSpacing.xs),
                  Text(label, style: AppTextStyles.caption, textAlign: TextAlign.center),
                ],
              ),
            ),
          ),
        ],
      ],
    );
  }
}

class _Chart extends StatelessWidget {
  const _Chart(this.journal);

  final MoodJournal journal;

  static const _days = 30;

  @override
  Widget build(BuildContext context) {
    final start = DateUtils.dateOnly(DateTime.now()).subtract(const Duration(days: _days - 1));
    final spots = [
      for (final entry in journal.entries)
        FlSpot(DateUtils.dateOnly(entry.date).difference(start).inDays.toDouble(), entry.level.value.toDouble()),
    ];

    return Card(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(AppSpacing.sm, AppSpacing.lg, AppSpacing.lg, AppSpacing.md),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Padding(
              padding: EdgeInsets.only(left: AppSpacing.sm),
              child: Text('30 derniers jours', style: AppTextStyles.label),
            ),
            const SizedBox(height: AppSpacing.lg),
            SizedBox(
              height: 180,
              child: spots.isEmpty
                  ? const Center(child: Text('La courbe apparaîtra après ton premier check-in.', style: AppTextStyles.caption))
                  : LineChart(
                      LineChartData(
                        minX: 0,
                        maxX: _days - 1,
                        minY: 0.5,
                        maxY: 5.5,
                        gridData: FlGridData(
                          drawVerticalLine: false,
                          horizontalInterval: 1,
                          getDrawingHorizontalLine: (_) => const FlLine(color: AppColors.border, strokeWidth: 1),
                        ),
                        borderData: FlBorderData(show: false),
                        lineTouchData: const LineTouchData(enabled: false),
                        titlesData: FlTitlesData(
                          topTitles: const AxisTitles(),
                          rightTitles: const AxisTitles(),
                          leftTitles: AxisTitles(
                            sideTitles: SideTitles(
                              showTitles: true,
                              interval: 1,
                              reservedSize: 34,
                              getTitlesWidget: (value, _) => value % 1 == 0 && value >= 1 && value <= 5
                                  ? MoodFace(level: MoodLevel.fromValue(value.toInt()), size: 22)
                                  : const SizedBox.shrink(),
                            ),
                          ),
                          bottomTitles: AxisTitles(
                            sideTitles: SideTitles(
                              showTitles: true,
                              interval: 1,
                              reservedSize: 24,
                              // Une date par semaine, la dernière étant aujourd'hui.
                              getTitlesWidget: (value, _) => (_days - 1 - value.toInt()) % 7 != 0
                                  ? const SizedBox.shrink()
                                  : Padding(
                                      padding: const EdgeInsets.only(top: AppSpacing.xs),
                                      child: Text(
                                        DateFormat('d MMM', 'fr_FR').format(start.add(Duration(days: value.toInt()))),
                                        style: AppTextStyles.caption.copyWith(fontSize: 11),
                                      ),
                                    ),
                            ),
                          ),
                        ),
                        lineBarsData: [
                          LineChartBarData(
                            spots: spots,
                            isCurved: true,
                            preventCurveOverShooting: true,
                            color: AppColors.violet700,
                            barWidth: 3,
                            dotData: FlDotData(
                              getDotPainter: (_, _, _, _) => FlDotCirclePainter(
                                radius: 3.5,
                                color: AppColors.white,
                                strokeWidth: 2.5,
                                strokeColor: AppColors.violet700,
                              ),
                            ),
                            belowBarData: BarAreaData(
                              show: true,
                              gradient: LinearGradient(
                                begin: Alignment.topCenter,
                                end: Alignment.bottomCenter,
                                colors: [AppColors.violet300.withValues(alpha: 0.35), AppColors.violet300.withValues(alpha: 0)],
                              ),
                            ),
                          ),
                        ],
                      ),
                      duration: const Duration(milliseconds: 600),
                      curve: Curves.easeOutCubic,
                    ),
            ),
          ],
        ),
      ),
    );
  }
}

class _EntryTile extends StatelessWidget {
  const _EntryTile(this.entry);

  final MoodEntry entry;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.sm),
      child: Card(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              MoodFace(level: entry.level, size: 40),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(DateFormat('EEEE d MMMM', 'fr_FR').format(entry.date), style: AppTextStyles.label),
                    Text(entry.level.label, style: AppTextStyles.caption),
                    if (entry.feelings.isNotEmpty) ...[
                      const SizedBox(height: AppSpacing.sm),
                      Wrap(
                        spacing: AppSpacing.xs,
                        runSpacing: AppSpacing.xs,
                        children: [
                          for (final feeling in entry.feelings)
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm, vertical: 2),
                              decoration: BoxDecoration(
                                color: AppColors.violet50,
                                borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
                              ),
                              child: Text(Feeling.labelOf(feeling), style: AppTextStyles.caption.copyWith(color: AppColors.violet800)),
                            ),
                        ],
                      ),
                    ],
                    if (entry.note != null) ...[const SizedBox(height: AppSpacing.sm), Text(entry.note!, style: AppTextStyles.body)],
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
