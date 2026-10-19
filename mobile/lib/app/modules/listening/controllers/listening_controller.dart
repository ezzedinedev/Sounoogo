import 'dart:async';

import 'package:get/get.dart';

import '../../../../routes/app_routes.dart';
import '../../../core/network/api_exception.dart';
import '../../../core/services/realtime_service.dart';
import '../../../core/services/session_service.dart';
import '../../../core/widgets/emergency_sheet.dart';
import '../models/conversation_model.dart';
import '../models/message_model.dart';
import '../repositories/listening_repository.dart';

class ListeningController extends GetxController {
  ListeningController(this._repository);

  final ListeningRepository _repository;
  final SessionService _session = Get.find();
  final RealtimeService _realtime = Get.find();

  final conversation = Rxn<ConversationModel>();
  final messages = <MessageModel>[].obs;
  final isLoading = false.obs;
  final isSending = false.obs;
  final errorMessage = RxnString();
  final selectedTopic = RxnString();

  StreamSubscription<Map<String, dynamic>>? _messagesSub;
  StreamSubscription<Map<String, dynamic>>? _statusSub;
  Timer? _poll;

  int? get myId => _session.user.value?.id;

  @override
  void onInit() {
    super.onInit();
    loadCurrent();
  }

  Future<void> loadCurrent() => _guard(() async {
        final current = await _repository.current();
        conversation.value = current;
        if (current != null) {
          await _attach(current);
        }
      });

  Future<void> start() => _guard(() async {
        final opened = await _repository.open(topic: selectedTopic.value);
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
    final body = text.trim();
    final current = conversation.value;
    if (body.isEmpty || current == null || isSending.value) {
      return;
    }
    isSending.value = true;
    try {
      final sent = await _repository.send(current.id, body);
      _append(sent.message);
      if (sent.riskLevel == 'high') {
        // Détresse détectée : numéros d'urgence affichés tout de suite (le superviseur est déjà alerté côté serveur).
        await EmergencySheet.show(alertSupervisor: false);
      }
    } on ApiException catch (error) {
      errorMessage.value = _friendlyError(error);
    } finally {
      isSending.value = false;
    }
  }

  Future<void> close() => _guard(() async {
        final current = conversation.value;
        if (current == null) {
          return;
        }
        conversation.value = await _repository.close(current.id);
        await _refreshMessages();
      });

  Future<void> rate(int rating) => _guard(() async {
        final current = conversation.value;
        if (current == null) {
          return;
        }
        await _repository.rate(current.id, rating);
        _detach();
        conversation.value = null;
        messages.clear();
        Get.back<void>();
        Get.snackbar('Merci', 'Prends soin de toi. Tu peux revenir quand tu veux.', snackPosition: SnackPosition.BOTTOM);
      });

  Future<void> report(String reason) => _guard(() async {
        final current = conversation.value;
        if (current != null) {
          await _repository.report(current.id, reason);
          Get.snackbar('Signalement envoyé', "L'équipe de modération va examiner la conversation.", snackPosition: SnackPosition.BOTTOM);
        }
      });

  Future<void> _attach(ConversationModel current) async {
    _detach();
    await _refreshMessages();

    try {
      final channel = 'conversation.${current.id}';
      _messagesSub = (await _realtime.listen(channel, 'message.sent')).listen((data) {
        _append(MessageModel.fromJson(data['message'] as Map<String, dynamic>));
      });
      _statusSub = (await _realtime.listen(channel, 'conversation.updated')).listen((data) {
        conversation.value = ConversationModel.fromJson(data['conversation'] as Map<String, dynamic>);
      });
    } catch (_) {
      // Temps réel indisponible : on bascule sur un rafraîchissement régulier.
    }
    _poll = Timer.periodic(const Duration(seconds: 15), (_) => _refresh());
  }

  Future<void> _refresh() async {
    final current = conversation.value;
    if (current == null) {
      return;
    }
    try {
      conversation.value = await _repository.find(current.id);
      await _refreshMessages();
    } catch (_) {}
  }

  Future<void> _refreshMessages() async {
    final current = conversation.value;
    if (current != null) {
      messages.assignAll(await _repository.messages(current.id));
    }
  }

  void _append(MessageModel message) {
    if (!messages.any((m) => m.id == message.id)) {
      messages.add(message);
    }
  }

  void _detach() {
    _messagesSub?.cancel();
    _statusSub?.cancel();
    _poll?.cancel();
    final current = conversation.value;
    if (current != null) {
      _realtime.leave('conversation.${current.id}');
    }
  }

  Future<void> _guard(Future<void> Function() action) async {
    isLoading.value = true;
    errorMessage.value = null;
    try {
      await action();
    } on ApiException catch (error) {
      errorMessage.value = _friendlyError(error);
    } finally {
      isLoading.value = false;
    }
  }

  String _friendlyError(ApiException error) =>
      error.isNetwork ? 'Pas de connexion. Ton message n\'a pas été envoyé, réessaie dans un instant.' : error.message;

  @override
  void onClose() {
    _detach();
    super.onClose();
  }
}
