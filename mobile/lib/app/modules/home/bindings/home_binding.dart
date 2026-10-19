import 'package:get/get.dart';

import '../../courses/bindings/courses_binding.dart';
import '../../listening/bindings/listening_binding.dart';
import '../../payments/bindings/payment_binding.dart';
import '../../professionals/bindings/professionals_binding.dart';
import '../../profile/bindings/profile_binding.dart';
import '../controllers/home_controller.dart';

/// L'accueil héberge les onglets : chaque module enregistre son propre contrôleur.
class HomeBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut(HomeController.new);
    ListeningBinding().dependencies();
    ProfessionalsBinding().dependencies();
    CoursesBinding().dependencies();
    ProfileBinding().dependencies();
    PaymentBinding().dependencies();
  }
}
