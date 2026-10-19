import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_text_styles.dart';
import '../../../../core/utils/formatters.dart';
import '../../../emergency/presentation/widgets/emergency_sheet.dart';
import '../controllers/listening_controller.dart';
import '../../domain/entities/message.dart';

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

  /// Première apparition de chaque message. Ceux déjà là à l'ouverture ne sont pas animés ;
  /// un nouveau message garde son animation même si la liste se reconstruit pendant qu'elle joue.
  late final Map<int, DateTime> _firstSeen = {for (final message in controller.messages) message.id: DateTime(2000)};

  @override
  void initState() {
    super.initState();
    _worker = ever(controller.messages, (_) => _scrollToEnd());
    _scrollToEnd();
  }

  bool _isNew(Message message) =>
      DateTime.now().difference(_firstSeen.putIfAbsent(message.id, DateTime.now)) < const Duration(milliseconds: 600);

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
          final title = waiting ? 'En attente d\'un écoutant' : (current?.listenerName ?? 'Écoute');
          return AnimatedSwitcher(
            duration: const Duration(milliseconds: 300),
            layoutBuilder: (current, previous) => Stack(alignment: Alignment.centerLeft, children: [...previous, ?current]),
            child: Column(
              key: ValueKey(title),
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: AppTextStyles.subtitle),
                Text(current?.isClosed ?? false ? 'Conversation terminée' : 'Anonyme · chiffrée', style: AppTextStyles.caption),
              ],
            ),
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
            child: Obx(() {
              final waiting = controller.conversation.value?.isWaiting ?? false;
              final messages = controller.messages;
              return ListView.builder(
                controller: _scroll,
                padding: const EdgeInsets.all(AppSpacing.page),
                itemCount: messages.length + (waiting ? 1 : 0),
                itemBuilder: (_, index) {
                  if (index == messages.length) {
                    return const _WaitingForListener();
                  }
                  final message = messages[index];
                  final mine = message.senderId == controller.myId;
                  return _Arrival(
                    key: ValueKey(message.id),
                    fromRight: mine,
                    animate: _isNew(message),
                    child: _Bubble(message: message, mine: mine),
                  );
                },
              );
            }),
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

  final Message message;
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
                style: AppTextStyles.caption.copyWith(
                  fontSize: 11,
                  color: mine ? AppColors.white.withValues(alpha: 0.8) : AppColors.textSecondary,
                ),
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
        decoration: const BoxDecoration(
          color: AppColors.surface,
          border: Border(top: BorderSide(color: AppColors.border)),
        ),
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
                    decoration: const InputDecoration(
                      hintText: 'Écris ce que tu ressens…',
                      filled: false,
                      border: InputBorder.none,
                      enabledBorder: InputBorder.none,
                      focusedBorder: InputBorder.none,
                    ),
                  ),
                ),
                // Le bouton s'allume dès qu'il y a quelque chose à envoyer.
                ValueListenableBuilder<TextEditingValue>(
                  valueListenable: input,
                  builder: (_, value, _) {
                    final ready = value.text.trim().isNotEmpty && !sending;
                    return AnimatedScale(
                      scale: ready || sending ? 1 : 0.86,
                      duration: const Duration(milliseconds: 180),
                      curve: Curves.easeOutBack,
                      child: IconButton.filled(
                        onPressed: ready ? onSend : null,
                        tooltip: 'Envoyer',
                        style: IconButton.styleFrom(
                          backgroundColor: AppColors.violet700,
                          foregroundColor: AppColors.white,
                          disabledBackgroundColor: AppColors.violet100,
                          disabledForegroundColor: AppColors.violet300,
                        ),
                        icon: sending
                            ? const SizedBox.square(dimension: 18, child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.white))
                            : const Icon(Icons.arrow_upward_rounded),
                      ),
                    );
                  },
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _RatingBar extends StatefulWidget {
  const _RatingBar({required this.onRate});

  final ValueChanged<int> onRate;

  @override
  State<_RatingBar> createState() => _RatingBarState();
}

class _RatingBarState extends State<_RatingBar> {
  int _rating = 0;

  Future<void> _rate(int rating) async {
    if (_rating > 0) {
      return;
    }
    setState(() => _rating = rating);
    // Le temps de voir les cœurs se remplir avant d'envoyer.
    await Future<void>.delayed(const Duration(milliseconds: 650));
    widget.onRate(rating);
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      top: false,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(AppSpacing.xl),
        decoration: const BoxDecoration(
          color: AppColors.violet50,
          border: Border(top: BorderSide(color: AppColors.border)),
        ),
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
                    onPressed: () => _rate(rating),
                    icon: AnimatedScale(
                      scale: rating <= _rating ? 1.15 : 1,
                      duration: Duration(milliseconds: 200 + rating * 60),
                      curve: Curves.easeOutBack,
                      child: Icon(
                        rating <= _rating ? Icons.favorite_rounded : Icons.favorite_border_rounded,
                        color: AppColors.violet700,
                        size: 30,
                      ),
                    ),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

/// Arrivée d'un message : il glisse depuis son côté en grandissant légèrement.
class _Arrival extends StatelessWidget {
  const _Arrival({super.key, required this.fromRight, required this.animate, required this.child});

  final bool fromRight;
  final bool animate;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final still = !animate || MediaQuery.of(context).disableAnimations;
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: still ? 1 : 0, end: 1),
      duration: const Duration(milliseconds: 380),
      curve: Curves.easeOutCubic,
      builder: (_, t, child) => Opacity(
        opacity: t,
        child: Transform.translate(
          offset: Offset((fromRight ? 24 : -24) * (1 - t), 8 * (1 - t)),
          child: Transform.scale(scale: 0.94 + 0.06 * t, alignment: fromRight ? Alignment.bottomRight : Alignment.bottomLeft, child: child),
        ),
      ),
      child: child,
    );
  }
}

/// Tant qu'aucun écoutant n'a rejoint : une présence qui « respire » et des points de saisie.
class _WaitingForListener extends StatefulWidget {
  const _WaitingForListener();

  @override
  State<_WaitingForListener> createState() => _WaitingForListenerState();
}

class _WaitingForListenerState extends State<_WaitingForListener> with SingleTickerProviderStateMixin {
  late final AnimationController _pulse = AnimationController(vsync: this, duration: const Duration(milliseconds: 1800));

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (MediaQuery.of(context).disableAnimations) {
      _pulse.value = 0.5;
    } else if (!_pulse.isAnimating) {
      _pulse.repeat();
    }
  }

  @override
  void dispose() {
    _pulse.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: AppSpacing.xl),
      child: Column(
        children: [
          SizedBox.square(
            dimension: 96,
            child: AnimatedBuilder(
              animation: _pulse,
              builder: (_, _) => Stack(
                alignment: Alignment.center,
                children: [
                  for (final delay in const [0.0, 0.5])
                    Builder(
                      builder: (_) {
                        final t = (_pulse.value + delay) % 1;
                        return Container(
                          width: 48 + 48 * t,
                          height: 48 + 48 * t,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: AppColors.violet300.withValues(alpha: 0.35 * (1 - t)),
                          ),
                        );
                      },
                    ),
                  Container(
                    width: 48,
                    height: 48,
                    decoration: const BoxDecoration(gradient: AppColors.brandGradient, shape: BoxShape.circle),
                    child: const Icon(Icons.headset_mic_outlined, color: AppColors.white, size: 22),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: AppSpacing.md),
          const Text('Recherche d\'un écoutant disponible', style: AppTextStyles.label),
          const SizedBox(height: AppSpacing.md),
          AnimatedBuilder(
            animation: _pulse,
            builder: (_, _) => Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                for (var i = 0; i < 3; i++)
                  Container(
                    margin: const EdgeInsets.symmetric(horizontal: 3),
                    width: 7,
                    height: 7,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppColors.violet500.withValues(alpha: 0.3 + 0.7 * _dot(i)),
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  /// Chaque point s'allume à son tour.
  double _dot(int index) {
    final t = (_pulse.value * 3 - index) % 3;
    return t < 1 ? 1 - (t - 0.5).abs() * 2 : 0;
  }
}
