import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../domain/entities/safety_plan.dart';
import '../controllers/safety_plan_controller.dart';
import '../widgets/safety_plan_sections.dart';
import '../../../../core/widgets/app_toast.dart';

/// Rédaction du plan, section par section.
class SafetyPlanEditScreen extends StatefulWidget {
  const SafetyPlanEditScreen({super.key});

  @override
  State<SafetyPlanEditScreen> createState() => _SafetyPlanEditScreenState();
}

class _SafetyPlanEditScreenState extends State<SafetyPlanEditScreen> {
  static const _maxTexts = 10;
  static const _maxContacts = 5;

  final controller = Get.find<SafetyPlanController>();
  late final Map<SafetyPlanSection, List<TextEditingController>> _texts;
  late final Map<SafetyPlanSection, List<(TextEditingController, TextEditingController)>> _contacts;

  @override
  void initState() {
    super.initState();
    final plan = controller.plan.value ?? const SafetyPlan();
    _texts = {
      for (final section in SafetyPlanSection.values.where((s) => !s.isContacts))
        section: [for (final text in section.textsOf(plan)) TextEditingController(text: text)],
    };
    _contacts = {
      for (final section in SafetyPlanSection.values.where((s) => s.isContacts))
        section: [
          for (final contact in section.contactsOf(plan))
            (TextEditingController(text: contact.name), TextEditingController(text: contact.phone)),
        ],
    };
    for (final fields in _texts.values) {
      if (fields.isEmpty) {
        fields.add(TextEditingController());
      }
    }
  }

  @override
  void dispose() {
    for (final field in _texts.values.expand((fields) => fields)) {
      field.dispose();
    }
    for (final (name, phone) in _contacts.values.expand((fields) => fields)) {
      name.dispose();
      phone.dispose();
    }
    super.dispose();
  }

  List<String> _valuesOf(SafetyPlanSection section) => [for (final field in _texts[section]!) field.text];

  List<SafetyContact> _contactsOf(SafetyPlanSection section) => [
    for (final (name, phone) in _contacts[section]!) SafetyContact(name: name.text, phone: phone.text),
  ];

  Future<void> _save() async {
    final saved = await controller.save(
      SafetyPlan(
        warningSigns: _valuesOf(SafetyPlanSection.warningSigns),
        copingStrategies: _valuesOf(SafetyPlanSection.copingStrategies),
        distractions: _valuesOf(SafetyPlanSection.distractions),
        supportContacts: _contactsOf(SafetyPlanSection.supportContacts),
        professionalContacts: _contactsOf(SafetyPlanSection.professionalContacts),
        safeEnvironment: _valuesOf(SafetyPlanSection.safeEnvironment),
        reasonsToLive: _valuesOf(SafetyPlanSection.reasonsToLive),
      ),
    );
    if (saved) {
      Get.back<void>();
      AppToast.show('Plan enregistré', 'Il reste disponible sur ce téléphone, même sans connexion.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Préparer mon plan')),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, 120),
        children: [
          for (final section in SafetyPlanSection.values) ...[
            _SectionHeader(section),
            const SizedBox(height: AppSpacing.md),
            if (section.isContacts) ..._contactEditors(section) else ..._textEditors(section),
            const SizedBox(height: AppSpacing.xl),
          ],
        ],
      ),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(AppSpacing.page, AppSpacing.sm, AppSpacing.page, AppSpacing.md),
          child: Obx(
            () => FilledButton(
              onPressed: controller.isSaving.value ? null : _save,
              child: controller.isSaving.value
                  ? const SizedBox.square(dimension: 22, child: CircularProgressIndicator(strokeWidth: 2.4, color: AppColors.white))
                  : const Text('Enregistrer mon plan'),
            ),
          ),
        ),
      ),
    );
  }

  List<Widget> _textEditors(SafetyPlanSection section) {
    final fields = _texts[section]!;
    return [
      for (final (index, field) in fields.indexed)
        Padding(
          padding: const EdgeInsets.only(bottom: AppSpacing.sm),
          child: TextField(
            controller: field,
            maxLength: 200,
            textCapitalization: TextCapitalization.sentences,
            decoration: InputDecoration(
              hintText: index == 0 ? section.hint : null,
              counterText: '',
              suffixIcon: fields.length > 1
                  ? IconButton(
                      tooltip: 'Retirer',
                      icon: const Icon(Icons.close_rounded),
                      onPressed: () => setState(() => fields.removeAt(index).dispose()),
                    )
                  : null,
            ),
          ),
        ),
      if (fields.length < _maxTexts) _AddButton(onPressed: () => setState(() => fields.add(TextEditingController()))),
    ];
  }

  List<Widget> _contactEditors(SafetyPlanSection section) {
    final fields = _contacts[section]!;
    return [
      for (final (index, (name, phone)) in fields.indexed)
        Padding(
          padding: const EdgeInsets.only(bottom: AppSpacing.sm),
          child: Row(
            children: [
              Expanded(
                flex: 5,
                child: TextField(
                  controller: name,
                  maxLength: 80,
                  textCapitalization: TextCapitalization.words,
                  decoration: const InputDecoration(labelText: 'Nom', counterText: ''),
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              Expanded(
                flex: 4,
                child: TextField(
                  controller: phone,
                  maxLength: 30,
                  keyboardType: TextInputType.phone,
                  decoration: const InputDecoration(labelText: 'Téléphone', counterText: ''),
                ),
              ),
              IconButton(
                tooltip: 'Retirer',
                icon: const Icon(Icons.close_rounded),
                onPressed: () => setState(() {
                  final (removedName, removedPhone) = fields.removeAt(index);
                  removedName.dispose();
                  removedPhone.dispose();
                }),
              ),
            ],
          ),
        ),
      if (fields.length < _maxContacts)
        _AddButton(
          label: 'Ajouter un contact',
          onPressed: () => setState(() => fields.add((TextEditingController(), TextEditingController()))),
        ),
    ];
  }
}

class _SectionHeader extends StatelessWidget {
  const _SectionHeader(this.section);

  final SafetyPlanSection section;

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(AppSpacing.sm),
          decoration: BoxDecoration(color: AppColors.violet50, borderRadius: BorderRadius.circular(AppSpacing.radiusSm)),
          child: Icon(section.icon, color: AppColors.violet700, size: 20),
        ),
        const SizedBox(width: AppSpacing.md),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('${section.step}. ${section.title}', style: AppTextStyles.subtitle),
              const SizedBox(height: 2),
              Text(section.help, style: AppTextStyles.caption),
            ],
          ),
        ),
      ],
    );
  }
}

class _AddButton extends StatelessWidget {
  const _AddButton({required this.onPressed, this.label = 'Ajouter une ligne'});

  final VoidCallback onPressed;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.centerLeft,
      child: TextButton.icon(onPressed: onPressed, icon: const Icon(Icons.add_rounded), label: Text(label)),
    );
  }
}
