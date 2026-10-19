import 'dart:async';

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
import '../../domain/usecases/open_conversation.dart';
import '../../domain/usecases/rate_conversation.dart';
import '../../domain/usecases/report_conversation.dart';
import '../../domain/usecases/send_message.dart';
import '../../domain/usecases/watch_conversation.dart';

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
    Get.snackbar('Merci', 'Prends soin de toi. Tu peux revenir quand tu veux.', snackPosition: SnackPosition.BOTTOM);
  });

  Future<void> report(String reason) => _guard(() async {
    final current = conversation.value;
    if (current != null) {
      await _reportConversation(current.id, reason);
      Get.snackbar('Signalement envoyé', "L'équipe de modération va examiner la conversation.", snackPosition: SnackPosition.BOTTOM);
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
