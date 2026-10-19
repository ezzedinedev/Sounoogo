import 'package:get/get.dart';

import '../app/modules/auth/presentation/bindings/auth_binding.dart';
import '../app/modules/auth/presentation/screens/recover_screen.dart';
import '../app/modules/auth/presentation/screens/recovery_code_screen.dart';
import '../app/modules/auth/presentation/screens/welcome_screen.dart';
import '../app/modules/bookings/presentation/bindings/bookings_binding.dart';
import '../app/modules/bookings/presentation/screens/bookings_screen.dart';
import '../app/modules/courses/presentation/bindings/courses_binding.dart';
import '../app/modules/courses/presentation/screens/course_detail_screen.dart';
import '../app/modules/courses/presentation/screens/lesson_screen.dart';
import '../app/modules/home/presentation/bindings/home_binding.dart';
import '../app/modules/home/presentation/screens/home_screen.dart';
import '../app/modules/listening/presentation/screens/conversation_screen.dart';
import '../app/modules/payments/presentation/screens/payment_status_screen.dart';
import '../app/modules/professionals/presentation/bindings/professionals_binding.dart';
import '../app/modules/professionals/presentation/screens/professional_detail_screen.dart';
import '../app/modules/profile/presentation/screens/upgrade_screen.dart';
import '../app/modules/splash/presentation/bindings/splash_binding.dart';
import '../app/modules/splash/presentation/screens/splash_screen.dart';
import '../app/modules/subscription/presentation/bindings/subscription_binding.dart';
import '../app/modules/subscription/presentation/screens/subscription_screen.dart';
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
