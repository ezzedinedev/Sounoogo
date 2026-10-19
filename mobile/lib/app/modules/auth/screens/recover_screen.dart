import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/widgets/accent_title.dart';
import '../controllers/auth_controller.dart';

/// Formulaire générique à deux champs, partagé par « récupérer » et « se connecter ».
class _TwoFieldForm extends StatefulWidget {
  const _TwoFieldForm({
    required this.title,
    required this.accent,
    required this.subtitle,
    required this.firstLabel,
    required this.secondLabel,
    required this.submitLabel,
    required this.onSubmit,
    this.obscureSecond = false,
    this.firstKeyboard = TextInputType.text,
  });

  final String title;
  final String accent;
  final String subtitle;
  final String firstLabel;
  final String secondLabel;
  final String submitLabel;
  final bool obscureSecond;
  final TextInputType firstKeyboard;
  final Future<void> Function(String first, String second) onSubmit;

  @override
  State<_TwoFieldForm> createState() => _TwoFieldFormState();
}

class _TwoFieldFormState extends State<_TwoFieldForm> {
  final _first = TextEditingController();
  final _second = TextEditingController();

  @override
  void dispose() {
    _first.dispose();
    _second.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<AuthController>();

    return Scaffold(
      appBar: AppBar(),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.all(AppSpacing.page),
          children: [
            AccentTitle(widget.title, accent: widget.accent, large: true),
            const SizedBox(height: AppSpacing.md),
            Text(widget.subtitle, style: AppTextStyles.bodySecondary),
            const SizedBox(height: AppSpacing.xl),
            TextField(controller: _first, keyboardType: widget.firstKeyboard, decoration: InputDecoration(labelText: widget.firstLabel), textInputAction: TextInputAction.next),
            const SizedBox(height: AppSpacing.md),
            TextField(controller: _second, obscureText: widget.obscureSecond, decoration: InputDecoration(labelText: widget.secondLabel)),
            const SizedBox(height: AppSpacing.lg),
            Obx(() => controller.errorMessage.value == null
                ? const SizedBox.shrink()
                : Padding(
                    padding: const EdgeInsets.only(bottom: AppSpacing.md),
                    child: Text(controller.errorMessage.value!, style: AppTextStyles.caption.copyWith(color: AppColors.sos)),
                  )),
            Obx(
              () => FilledButton(
                onPressed: controller.isLoading.value ? null : () => widget.onSubmit(_first.text, _second.text),
                child: controller.isLoading.value
                    ? const SizedBox.square(dimension: 22, child: CircularProgressIndicator(strokeWidth: 2.4, color: AppColors.white))
                    : Text(widget.submitLabel),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class RecoverScreen extends GetView<AuthController> {
  const RecoverScreen({super.key});

  @override
  Widget build(BuildContext context) => _TwoFieldForm(
        title: 'Retrouver',
        accent: 'ton espace',
        subtitle: 'Entre le pseudonyme et le code de récupération reçus lors de ta première visite.',
        firstLabel: 'Pseudonyme (ex : Baobab-204)',
        secondLabel: 'Code de récupération',
        submitLabel: 'Retrouver mon espace',
        onSubmit: controller.recover,
      );
}

class LoginScreen extends GetView<AuthController> {
  const LoginScreen({super.key});

  @override
  Widget build(BuildContext context) => _TwoFieldForm(
        title: 'Content de',
        accent: 'te revoir',
        subtitle: "Connecte-toi avec l'e-mail ou le numéro de téléphone de ton compte.",
        firstLabel: 'E-mail ou téléphone',
        secondLabel: 'Mot de passe',
        submitLabel: 'Se connecter',
        obscureSecond: true,
        firstKeyboard: TextInputType.emailAddress,
        onSubmit: controller.login,
      );
}
