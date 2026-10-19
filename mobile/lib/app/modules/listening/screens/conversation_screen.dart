import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../core/theme/app_colors.dart';
import '../../../core/theme/app_spacing.dart';
import '../../../core/theme/app_text_styles.dart';
import '../../../core/utils/formatters.dart';
import '../../../core/widgets/emergency_sheet.dart';
import '../controllers/listening_controller.dart';
import '../models/message_model.dart';

/// Le chat d'écoute.
class ConversationScreen extends StatefulWidget {
  const ConversationScreen({super.key});

  @override
  State<ConversationScreen> createState() => _ConversationScreenState();
}

class _ConversationScreenState extends State<ConversationScreen> {
  final controller = Get.find<ListeningController>();
  final _input = TextEditingController();
  final _scroll = ScrollController();
  late final Worker _worker;

  @override
  void initState() {
    super.initState();
    _worker = ever(controller.messages, (_) => _scrollToEnd());
    _scrollToEnd();
  }

  @override
  void dispose() {
    _worker.dispose();
    _input.dispose();
    _scroll.dispose();
    super.dispose();
  }

  void _scrollToEnd() => WidgetsBinding.instance.addPostFrameCallback((_) {
        if (_scroll.hasClients) {
          _scroll.animateTo(_scroll.position.maxScrollExtent, duration: const Duration(milliseconds: 250), curve: Curves.easeOut);
        }
      });

  Future<void> _send() async {
    final text = _input.text;
    _input.clear();
    await controller.send(text);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        titleSpacing: 0,
        title: Obx(() {
          final current = controller.conversation.value;
          final waiting = current?.isWaiting ?? true;
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(waiting ? 'En attente d\'un écoutant' : (current?.listenerName ?? 'Écoute'), style: AppTextStyles.subtitle),
              Text(
                current?.isClosed ?? false ? 'Conversation terminée' : 'Anonyme · chiffrée',
                style: AppTextStyles.caption,
              ),
            ],
          );
        }),
        actions: [
          const SosButton(),
          PopupMenuButton<String>(
            tooltip: 'Options',
            onSelected: (value) => switch (value) {
              'close' => _confirmClose(),
              _ => controller.report(value),
            },
            itemBuilder: (_) => const [
              PopupMenuItem(value: 'close', child: Text('Terminer la conversation')),
              PopupMenuItem(value: 'inappropriate', child: Text('Signaler des propos inappropriés')),
              PopupMenuItem(value: 'identity', child: Text('Signaler une demande d\'informations personnelles')),
            ],
          ),
        ],
      ),
      body: Column(
        children: [
          Expanded(
            child: Obx(
              () => ListView.builder(
                controller: _scroll,
                padding: const EdgeInsets.all(AppSpacing.page),
                itemCount: controller.messages.length,
                itemBuilder: (_, index) => _Bubble(message: controller.messages[index], mine: controller.messages[index].senderId == controller.myId),
              ),
            ),
          ),
          Obx(() {
            final current = controller.conversation.value;
            if (current?.isClosed ?? false) {
              return _RatingBar(onRate: controller.rate);
            }
            return _Composer(input: _input, sending: controller.isSending.value, onSend: _send, error: controller.errorMessage.value);
          }),
        ],
      ),
    );
  }

  Future<void> _confirmClose() async {
    final confirmed = await Get.dialog<bool>(
      AlertDialog(
        title: const Text('Terminer la conversation ?'),
        content: const Text('Tu pourras en ouvrir une nouvelle à tout moment.'),
        actions: [
          TextButton(onPressed: () => Get.back(result: false), child: const Text('Annuler')),
          TextButton(onPressed: () => Get.back(result: true), child: const Text('Terminer')),
        ],
      ),
    );
    if (confirmed ?? false) {
      await controller.close();
    }
  }
}

class _Bubble extends StatelessWidget {
  const _Bubble({required this.message, required this.mine});

  final MessageModel message;
  final bool mine;

  @override
  Widget build(BuildContext context) {
    if (message.isSystem) {
      return Padding(
        padding: const EdgeInsets.symmetric(vertical: AppSpacing.md),
        child: Text(message.body, textAlign: TextAlign.center, style: AppTextStyles.caption),
      );
    }

    return Align(
      alignment: mine ? Alignment.centerRight : Alignment.centerLeft,
      child: ConstrainedBox(
        constraints: BoxConstraints(maxWidth: MediaQuery.sizeOf(context).width * 0.78),
        child: Container(
          margin: const EdgeInsets.only(bottom: AppSpacing.sm),
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg, vertical: AppSpacing.md),
          decoration: BoxDecoration(
            color: mine ? AppColors.violet700 : AppColors.surface,
            border: mine ? null : Border.all(color: AppColors.border),
            borderRadius: BorderRadius.only(
              topLeft: const Radius.circular(AppSpacing.radiusMd + 2),
              topRight: const Radius.circular(AppSpacing.radiusMd + 2),
              bottomLeft: Radius.circular(mine ? AppSpacing.radiusMd + 2 : 4),
              bottomRight: Radius.circular(mine ? 4 : AppSpacing.radiusMd + 2),
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(message.body, style: AppTextStyles.body.copyWith(color: mine ? AppColors.white : AppColors.textPrimary)),
              const SizedBox(height: AppSpacing.xs),
              Text(
                Formatters.time(message.createdAt),
                style: AppTextStyles.caption.copyWith(fontSize: 11, color: mine ? AppColors.white.withValues(alpha: 0.7) : AppColors.textMuted),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _Composer extends StatelessWidget {
  const _Composer({required this.input, required this.sending, required this.onSend, this.error});

  final TextEditingController input;
  final bool sending;
  final VoidCallback onSend;
  final String? error;

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      top: false,
      child: Container(
        padding: const EdgeInsets.fromLTRB(AppSpacing.lg, AppSpacing.sm, AppSpacing.sm, AppSpacing.sm),
        decoration: const BoxDecoration(color: AppColors.surface, border: Border(top: BorderSide(color: AppColors.border))),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (error != null)
              Padding(
                padding: const EdgeInsets.only(bottom: AppSpacing.xs),
                child: Text(error!, style: AppTextStyles.caption.copyWith(color: AppColors.sos)),
              ),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: input,
                    minLines: 1,
                    maxLines: 5,
                    textCapitalization: TextCapitalization.sentences,
                    decoration: const InputDecoration(hintText: 'Écris ce que tu ressens…', filled: false, border: InputBorder.none, enabledBorder: InputBorder.none, focusedBorder: InputBorder.none),
                  ),
                ),
                IconButton.filled(
                  onPressed: sending ? null : onSend,
                  tooltip: 'Envoyer',
                  style: IconButton.styleFrom(backgroundColor: AppColors.violet700, foregroundColor: AppColors.white),
                  icon: sending
                      ? const SizedBox.square(dimension: 18, child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.white))
                      : const Icon(Icons.arrow_upward_rounded),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _RatingBar extends StatelessWidget {
  const _RatingBar({required this.onRate});

  final ValueChanged<int> onRate;

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      top: false,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(AppSpacing.xl),
        decoration: const BoxDecoration(color: AppColors.violet50, border: Border(top: BorderSide(color: AppColors.border))),
        child: Column(
          children: [
            const Text('Cette écoute t\'a-t-elle aidé·e ?', style: AppTextStyles.subtitle),
            const SizedBox(height: AppSpacing.md),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                for (var rating = 1; rating <= 5; rating++)
                  IconButton(
                    tooltip: '$rating sur 5',
                    onPressed: () => onRate(rating),
                    icon: const Icon(Icons.favorite_border_rounded, color: AppColors.violet700, size: 30),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
