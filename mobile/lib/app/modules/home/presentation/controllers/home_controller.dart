import 'package:get/get.dart';

import '../../../../shared/presentation/session_service.dart';

class HomeController extends GetxController {
  final SessionService _session = Get.find();

  final tab = 0.obs;

  static const listening = 1;
  static const professionals = 2;
  static const courses = 3;

  String get pseudonym => _session.user.value?.pseudonym ?? '';

  String get greeting {
    final hour = DateTime.now().hour;
    return hour < 12 ? 'Bonjour' : (hour < 18 ? 'Bon après-midi' : 'Bonsoir');
  }

  void goTo(int index) => tab.value = index;
}
