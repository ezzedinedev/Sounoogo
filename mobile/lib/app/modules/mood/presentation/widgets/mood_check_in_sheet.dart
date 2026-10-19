import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/widgets/app_sheet.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/feeling.dart';
import '../../domain/entities/mood_level.dart';
import '../controllers/mood_controller.dart';
import 'mood_face.dart';

/// Détail du check-in : émotions et note facultative.
abstract final class MoodCheckInSheet {
  static Future<bool> show(MoodLevel level) async => await AppSheet.show<bool>(_CheckInSheet(initial: level)) ?? false;
}

class _CheckInSheet extends StatefulWidget {
  const _CheckInSheet({required this.initial});

  final MoodLevel initial;

  @override
  State<_CheckInSheet> createState() => _CheckInSheetState();
}

class _CheckInSheetState extends State<_CheckInSheet> {
  final _note = TextEditingController();
  final _feelings = <String>{};
  late MoodLevel _level = widget.initial;

  @override
  void dispose() {
    _note.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    final saved = await Get.find<MoodController>().checkIn(_level, feelings: _feelings.toList(), note: _note.text);
    if (saved) {
      Get.back(result: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<MoodController>();

    return SafeArea(
      child: SingleChildScrollView(
        padding: EdgeInsets.fromLTRB(AppSpacing.page, 0, AppSpacing.page, AppSpacing.xl + MediaQuery.viewInsetsOf(context).bottom),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text("Aujourd'hui, je me sens…", style: AppTextStyles.title),
            const SizedBox(height: AppSpacing.lg),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                for (final level in MoodLevel.values)
                  GestureDetector(
                    onTap: () => setState(() => _level = level),
                    child: MoodFace(level: level, size: 50, selected: level == _level),
                  ),
              ],
            ),
            const SizedBox(height: AppSpacing.sm),
            Center(
              child: Text(_level.label, style: AppTextStyles.label.copyWith(color: AppColors.violet700)),
            ),
            const SizedBox(height: AppSpacing.xl),
            const Text('Ce que je ressens', style: AppTextStyles.label),
            const SizedBox(height: AppSpacing.sm),
            Wrap(
              spacing: AppSpacing.sm,
              runSpacing: AppSpacing.sm,
              children: [
                for (final feeling in Feeling.all)
                  FilterChip(
                    label: Text(feeling.label),
                    selected: _feelings.contains(feeling.value),
                    onSelected: (on) => setState(() {
                      if (!on) {
                        _feelings.remove(feeling.value);
                      } else if (_feelings.length < Feeling.maxPerCheckIn) {
                        _feelings.add(feeling.value);
                      }
                    }),
                  ),
              ],
            ),
            const SizedBox(height: AppSpacing.lg),
            TextField(
              controller: _note,
              maxLength: 1000,
              minLines: 2,
              maxLines: 4,
              decoration: const InputDecoration(labelText: 'Un mot sur ta journée (facultatif)', helperText: 'Visible par toi seul·e.'),
            ),
            const SizedBox(height: AppSpacing.md),
            Obx(
              () => FilledButton(
                onPressed: controller.isSaving.value ? null : _save,
                child: controller.isSaving.value
                    ? const SizedBox.square(dimension: 22, child: CircularProgressIndicator(strokeWidth: 2.4, color: AppColors.white))
                    : const Text('Enregistrer'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
