import 'package:get/get.dart';

import '../app/modules/auth/bindings/auth_binding.dart';
import '../app/modules/auth/screens/recover_screen.dart';
import '../app/modules/auth/screens/recovery_code_screen.dart';
import '../app/modules/auth/screens/welcome_screen.dart';
import '../app/modules/bookings/bindings/bookings_binding.dart';
import '../app/modules/bookings/screens/bookings_screen.dart';
import '../app/modules/courses/bindings/courses_binding.dart';
import '../app/modules/courses/screens/course_detail_screen.dart';
import '../app/modules/courses/screens/lesson_screen.dart';
import '../app/modules/home/bindings/home_binding.dart';
import '../app/modules/home/screens/home_screen.dart';
import '../app/modules/listening/screens/conversation_screen.dart';
import '../app/modules/payments/screens/payment_status_screen.dart';
import '../app/modules/professionals/bindings/professionals_binding.dart';
import '../app/modules/professionals/screens/professional_detail_screen.dart';
import '../app/modules/profile/screens/upgrade_screen.dart';
import '../app/modules/splash/bindings/splash_binding.dart';
import '../app/modules/splash/screens/splash_screen.dart';
import '../app/modules/subscription/bindings/subscription_binding.dart';
import '../app/modules/subscription/screens/subscription_screen.dart';
import 'app_routes.dart';

abstract final class AppPages {
  static final pages = [
    GetPage(name: AppRoutes.splash, page: SplashScreen.new, binding: SplashBinding()),
    GetPage(name: AppRoutes.welcome, page: WelcomeScreen.new, binding: AuthBinding()),
    GetPage(name: AppRoutes.recoveryCode, page: RecoveryCodeScreen.new, binding: AuthBinding()),
    GetPage(name: AppRoutes.recover, page: RecoverScreen.new, binding: AuthBinding()),
    GetPage(name: AppRoutes.login, page: LoginScreen.new, binding: AuthBinding()),
    GetPage(name: AppRoutes.home, page: HomeScreen.new, binding: HomeBinding()),
    GetPage(name: AppRoutes.conversation, page: ConversationScreen.new),
    GetPage(name: AppRoutes.professionalDetail, page: ProfessionalDetailScreen.new, binding: ProfessionalDetailBinding()),
    GetPage(name: AppRoutes.bookings, page: BookingsScreen.new, binding: BookingsBinding()),
    GetPage(name: AppRoutes.courseDetail, page: CourseDetailScreen.new, binding: CourseDetailBinding()),
    GetPage(name: AppRoutes.lesson, page: LessonScreen.new, binding: LessonBinding()),
    GetPage(name: AppRoutes.subscription, page: SubscriptionScreen.new, binding: SubscriptionBinding()),
    GetPage(name: AppRoutes.paymentStatus, page: PaymentStatusScreen.new),
    GetPage(name: AppRoutes.upgrade, page: UpgradeScreen.new),
  ];
}
