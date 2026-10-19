import 'package:get/get.dart';

import '../../data/datasources/listening_realtime_data_source.dart';
import '../../data/datasources/listening_remote_data_source.dart';
import '../../data/repositories/listening_repository_impl.dart';
import '../../domain/repositories/listening_repository.dart';
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
import '../controllers/listening_controller.dart';

class ListeningBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<ListeningRepository>(
      () => ListeningRepositoryImpl(ListeningRemoteDataSource(Get.find()), ListeningRealtimeDataSource(Get.find())),
      fenix: true,
    );
    Get.lazyPut(() {
      final ListeningRepository repository = Get.find();
      return ListeningController(
        getActiveConversation: GetActiveConversation(repository),
        openConversation: OpenConversation(repository),
        getConversation: GetConversation(repository),
        getMessages: GetMessages(repository),
        sendMessage: SendMessage(repository),
        closeConversation: CloseConversation(repository),
        rateConversation: RateConversation(repository),
        reportConversation: ReportConversation(repository),
        watchConversation: WatchConversation(repository),
        sendVoiceMessage: SendVoiceMessage(repository),
        getVoiceFile: GetVoiceFile(repository),
        preferListener: PreferListener(repository),
      );
    }, fenix: true);
  }
}
