import 'package:get/get.dart';

import '../../../courses/presentation/bindings/courses_binding.dart';
import '../../../listening/presentation/bindings/listening_binding.dart';
import '../../../payments/presentation/bindings/payment_binding.dart';
import '../../../professionals/presentation/bindings/professionals_binding.dart';
import '../../../profile/presentation/bindings/profile_binding.dart';
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
