abstract final class AppRoutes {
  static const splash = '/';
  static const welcome = '/welcome';
  static const recoveryCode = '/recovery-code';
  static const recover = '/recover';
  static const login = '/login';
  static const home = '/home';
  static const conversation = '/conversation';
  static const professionalDetail = '/professionals/:id';
  static const bookings = '/bookings';
  static const courseDetail = '/courses/:slug';
  static const lesson = '/courses/:slug/lessons/:id';
  static const subscription = '/subscription';
  static const paymentStatus = '/payments/:reference';
  static const upgrade = '/profile/upgrade';
  static const safetyPlan = '/safety-plan';
  static const safetyPlanEdit = '/safety-plan/edit';
  static const mood = '/mood';
  static const breathing = '/exercises/breathing';
  static const settings = '/settings';
  static const settingsPin = '/settings/pin';

  static String professional(int id) => '/professionals/$id';
  static String course(String slug) => '/courses/$slug';
  static String lessonOf(String slug, int id) => '/courses/$slug/lessons/$id';
  static String payment(String reference) => '/payments/$reference';
}
