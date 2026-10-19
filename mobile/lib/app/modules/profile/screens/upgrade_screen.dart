import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/widgets/accent_title.dart';
import '../controllers/profile_controller.dart';

class UpgradeScreen extends StatefulWidget {
  const UpgradeScreen({super.key});

  @override
  State<UpgradeScreen> createState() => _UpgradeScreenState();
}

class _UpgradeScreenState extends State<UpgradeScreen> {
  final _login = TextEditingController();
  final _password = TextEditingController();
  final controller = Get.find<ProfileController>();

  @override
  void dispose() {
    _login.dispose();
    _password.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
      body: ListView(
        padding: const EdgeInsets.all(AppSpacing.page),
        children: [
          const AccentTitle('Sécurise', accent: 'ton espace', large: true),
          const SizedBox(height: AppSpacing.md),
          const Text('Tes conversations et ta progression sont conservées. Personne d\'autre ne verra ces identifiants.', style: AppTextStyles.bodySecondary),
          const SizedBox(height: AppSpacing.xl),
          TextField(controller: _login, keyboardType: TextInputType.emailAddress, decoration: const InputDecoration(labelText: 'E-mail ou téléphone')),
          const SizedBox(height: AppSpacing.md),
          TextField(controller: _password, obscureText: true, decoration: const InputDecoration(labelText: 'Mot de passe (8 caractères minimum)')),
          const SizedBox(height: AppSpacing.lg),
          Obx(() => controller.errorMessage.value == null
              ? const SizedBox.shrink()
              : Padding(
                  padding: const EdgeInsets.only(bottom: AppSpacing.md),
                  child: Text(controller.errorMessage.value!, style: AppTextStyles.caption.copyWith(color: AppColors.sos)),
                )),
          Obx(
            () => FilledButton(
              onPressed: controller.isLoading.value ? null : () => controller.upgrade(login: _login.text, password: _password.text),
              child: const Text('Enregistrer'),
            ),
          ),
        ],
      ),
    );
  }
}
