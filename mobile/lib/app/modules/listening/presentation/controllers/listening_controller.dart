import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../../routes/app_routes.dart';
import '../../../../core/error/failure.dart';
import '../../../../shared/presentation/session_service.dart';
import '../../../emergency/presentation/widgets/emergency_sheet.dart';
import '../../domain/entities/conversation.dart';
import '../../domain/entities/conversation_event.dart';
import '../../domain/entities/message.dart';
import '../../domain/usecases/close_conversation.dart';
import '../../domain/usecases/get_active_conversation.dart';
import '../../domain/usecases/get_conversation.dart';
import '../../domain/usecases/get_messages.dart';
import '../../domain/usecases/get_voice_file.dart';
import '../../domain/usecases/prefer_listener.dart';
import '../../domain/usecases/send_voice_message.dart';
import '../../domain/usecases/open_conversation.dart';
import '../../domain/usecases/rate_conversation.dart';
import '../../domain/usecases/report_conversation.dart';
import '../../domain/usecases/send_message.dart';
import '../../domain/usecases/watch_conversation.dart';
import '../../../../core/widgets/app_toast.dart';

class ListeningController extends GetxController {
  ListeningController({
    required this._getActiveConversation,
    required this._openConversation,
    required this._getConversation,
    required this._getMessages,
    required this._sendMessage,
    required this._closeConversation,
    required this._rateConversation,
    required this._reportConversation,
    required this._watchConversation,
    required this._sendVoiceMessage,
    required this._getVoiceFile,
    required this._preferListener,
  });

  final GetActiveConversation _getActiveConversation;
  final OpenConversation _openConversation;
  final GetConversation _getConversation;
  final GetMessages _getMessages;
  final SendMessage _sendMessage;
  final CloseConversation _closeConversation;
  final RateConversation _rateConversation;
  final ReportConversation _reportConversation;
  final WatchConversation _watchConversation;
  final SendVoiceMessage _sendVoiceMessage;
  final GetVoiceFile _getVoiceFile;
  final PreferListener _preferListener;
  final SessionService _session = Get.find();

  final conversation = Rxn<Conversation>();
  final messages = <Message>[].obs;
  final isLoading = false.obs;
  final isSending = false.obs;
  final errorMessage = RxnString();
  final selectedTopic = RxnString();

  StreamSubscription<ConversationEvent>? _events;
  Timer? _poll;

  int? get myId => _session.user.value?.id;

  @override
  void onInit() {
    super.onInit();
    loadCurrent();
  }

  Future<void> loadCurrent() => _guard(() async {
    final current = await _getActiveConversation();
    conversation.value = current;
    if (current != null) {
      await _attach(current);
    }
  });

  Future<void> start() => _guard(() async {
    final opened = await _openConversation(topic: selectedTopic.value);
    conversation.value = opened;
    await _attach(opened);
    Get.toNamed(AppRoutes.conversation);
  });

  Future<void> resume() async {
    if (conversation.value != null) {
      await Get.toNamed(AppRoutes.conversation);
    }
  }

  Future<void> send(String text) async {
    final current = conversation.value;
    if (current == null || isSending.value) {
      return;
    }
    isSending.value = true;
    errorMessage.value = null;
    try {
      final sent = await _sendMessage(current.id, text);
      if (sent == null) {
        return;
      }
      _append(sent.message);
      if (sent.needsEmergencyHelp) {
        // Détresse détectée : numéros affichés tout de suite (le superviseur est déjà alerté côté serveur).
        await EmergencySheet.show(alertSupervisor: false);
      }
    } on Failure catch (failure) {
      errorMessage.value = _friendlyError(failure);
    } finally {
      isSending.value = false;
    }
  }

  Future<void> close() => _guard(() async {
    final current = conversation.value;
    if (current == null) {
      return;
    }
    conversation.value = await _closeConversation(current.id);
    await _refreshMessages();
  });

  Future<void> rate(int rating) => _guard(() async {
    final current = conversation.value;
    if (current == null) {
      return;
    }
    await _rateConversation(current.id, rating);
    _detach();
    conversation.value = null;
    messages.clear();
    Get.back<void>();
    if (rating >= 4 && current.listenerName != null) {
      await _offerToKeepListener(current);
    } else {
      AppToast.show('Merci', 'Prends soin de toi. Tu peux revenir quand tu veux.');
    }
  });

  /// Après une écoute appréciée : proposer de retrouver la même personne la prochaine fois.
  Future<void> _offerToKeepListener(Conversation previous) async {
    final keep = await Get.dialog<bool>(
      AlertDialog(
        title: Text('Reparler à ${previous.listenerName} ?'),
        content: const Text('La prochaine fois, ta conversation lui sera confiée en priorité s\'il ou elle est disponible.'),
        actions: [
          TextButton(onPressed: () => Get.back(result: false), child: const Text('Non merci')),
          FilledButton(onPressed: () => Get.back(result: true), child: const Text('Oui')),
        ],
      ),
    );
    if (keep ?? false) {
      _session.updateUser(await _preferListener(previous.id));
      AppToast.show('C\'est noté', '${previous.listenerName} sera prévenu·e en priorité la prochaine fois.');
    }
  }

  /// Message vocal : renvoie `true` s'il est parti.
  Future<bool> sendVoice(String filePath, Duration length) async {
    final current = conversation.value;
    if (current == null || isSending.value) {
      return false;
    }
    isSending.value = true;
    errorMessage.value = null;
    try {
      final sent = await _sendVoiceMessage(current.id, filePath, length);
      if (sent != null) {
        _append(sent.message);
      }
      return sent != null;
    } on Failure catch (failure) {
      errorMessage.value = _friendlyError(failure);
      return false;
    } finally {
      isSending.value = false;
    }
  }

  Future<String> voiceFile(Message message) => _getVoiceFile(message);

  Future<void> report(String reason) => _guard(() async {
    final current = conversation.value;
    if (current != null) {
      await _reportConversation(current.id, reason);
      AppToast.show('Signalement envoyé', "L'équipe de modération va examiner la conversation.");
    }
  });

  Future<void> _attach(Conversation current) async {
    _detach();
    await _refreshMessages();

    _events = _watchConversation(current.id).listen(
      (event) {
        switch (event) {
          case MessageReceived(:final message):
            _append(message);
          case ConversationUpdated(conversation: final updated):
            conversation.value = updated;
        }
      },
      // Temps réel indisponible : le rafraîchissement régulier prend le relais.
      onError: (Object _) {},
    );
    _poll = Timer.periodic(const Duration(seconds: 15), (_) => _refresh());
  }

  Future<void> _refresh() async {
    final current = conversation.value;
    if (current == null) {
      return;
    }
    try {
      conversation.value = await _getConversation(current.id);
      await _refreshMessages();
    } on Failure {
      // Nouvel essai au prochain passage.
    }
  }

  Future<void> _refreshMessages() async {
    final current = conversation.value;
    if (current != null) {
      messages.assignAll(await _getMessages(current.id));
    }
  }

  void _append(Message message) {
    if (!messages.any((m) => m.id == message.id)) {
      messages.add(message);
    }
  }

  void _detach() {
    _events?.cancel();
    _events = null;
    _poll?.cancel();
  }

  Future<void> _guard(Future<void> Function() action) async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      await action();
    } on Failure catch (failure) {
      errorMessage.value = _friendlyError(failure);
    } finally {
      isLoading.value = false;
    }
  }

  String _friendlyError(Failure failure) => switch (failure) {
    NetworkFailure() => "Pas de connexion. Ton message n'a pas été envoyé, réessaie dans un instant.",
    _ => failure.message,
  };

  @override
  void onClose() {
    _detach();
    super.onClose();
  }
}
