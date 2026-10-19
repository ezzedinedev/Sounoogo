import 'package:flutter/material.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../domain/entities/certificate.dart';

/// Attestation d'une formation terminée : à demander, ou à télécharger une fois délivrée.
class CertificateCard extends StatelessWidget {
  const CertificateCard({super.key, required this.certificate, required this.busy, required this.onRequest, required this.onShare});

  final Certificate? certificate;
  final bool busy;
  final VoidCallback onRequest;
  final VoidCallback onShare;

  @override
  Widget build(BuildContext context) {
    final issued = certificate;

    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [AppColors.violet800, AppColors.violet950],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(AppSpacing.radiusLg),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.workspace_premium_rounded, color: AppColors.lilac200),
              const SizedBox(width: AppSpacing.sm),
              Text('Parcours terminé', style: AppTextStyles.label.copyWith(color: AppColors.white)),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          Text(
            issued == null
                ? 'Bravo ! Tu peux obtenir une attestation à ton nom. Ton compte, lui, reste anonyme.'
                : 'Attestation délivrée à ${issued.holderName} le ${Formatters.day(issued.issuedAt)}.',
            style: AppTextStyles.body.copyWith(color: AppColors.lilac200),
          ),
          if (issued != null) ...[
            const SizedBox(height: AppSpacing.xs),
            Text('Code de vérification : ${issued.code}', style: AppTextStyles.caption.copyWith(color: AppColors.violet200)),
          ],
          const SizedBox(height: AppSpacing.lg),
          FilledButton.icon(
            onPressed: busy ? null : (issued == null ? onRequest : onShare),
            style: FilledButton.styleFrom(backgroundColor: AppColors.white, foregroundColor: AppColors.violet800),
            icon: busy
                ? const SizedBox.square(dimension: 18, child: CircularProgressIndicator(strokeWidth: 2))
                : Icon(issued == null ? Icons.edit_note_rounded : Icons.ios_share_rounded),
            label: Text(issued == null ? 'Obtenir mon attestation' : 'Télécharger le PDF'),
          ),
        ],
      ),
    );
  }
}

/// Saisie du nom à inscrire sur l'attestation. Renvoie le nom, ou null si la personne renonce.
class CertificateNameSheet extends StatefulWidget {
  const CertificateNameSheet({super.key});

  @override
  State<CertificateNameSheet> createState() => _CertificateNameSheetState();
}

class _CertificateNameSheetState extends State<CertificateNameSheet> {
  final _name = TextEditingController();

  @override
  void dispose() {
    _name.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.fromLTRB(
        AppSpacing.page,
        AppSpacing.md,
        AppSpacing.page,
        AppSpacing.xl + MediaQuery.viewInsetsOf(context).bottom,
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const Text('Nom sur l\'attestation', style: AppTextStyles.subtitle),
          const SizedBox(height: AppSpacing.sm),
          const Text(
            'Il apparaîtra sur le PDF et sur la page de vérification, accessible uniquement avec le code. '
            'Il ne pourra plus être modifié.',
            style: AppTextStyles.bodySecondary,
          ),
          const SizedBox(height: AppSpacing.lg),
          TextField(
            controller: _name,
            autofocus: true,
            textCapitalization: TextCapitalization.words,
            autofillHints: const [AutofillHints.name],
            maxLength: 80,
            decoration: const InputDecoration(labelText: 'Prénom et nom'),
            onSubmitted: (_) => _submit(),
          ),
          const SizedBox(height: AppSpacing.md),
          ValueListenableBuilder(
            valueListenable: _name,
            builder: (_, value, _) =>
                FilledButton(onPressed: value.text.trim().length < 2 ? null : _submit, child: const Text('Obtenir l\'attestation')),
          ),
        ],
      ),
    );
  }

  void _submit() => Navigator.of(context).pop(_name.text);
}
