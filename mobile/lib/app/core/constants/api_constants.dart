/// Adresse et routes de l'API SUNOOGO (Laravel, /api/v1).
abstract final class ApiConstants {
  /// Surchargée au build : --dart-define=API_BASE_URL=https://api.sunoogo.app/api/v1
  /// Par défaut : l'hôte de la machine vu depuis l'émulateur Android.
  static const String baseUrl = String.fromEnvironment('API_BASE_URL', defaultValue: 'http://10.0.2.2:8000/api/v1');

  static const String reverbKey = String.fromEnvironment('REVERB_APP_KEY', defaultValue: 'sunoogo-key');
  static const String reverbHost = String.fromEnvironment('REVERB_HOST', defaultValue: '10.0.2.2');
  static const int reverbPort = int.fromEnvironment('REVERB_PORT', defaultValue: 8080);
  static const bool reverbTls = bool.fromEnvironment('REVERB_TLS');

  static const String meta = '/meta';
  static const String authAnonymous = '/auth/anonymous';
  static const String authRecover = '/auth/recover';
  static const String authLogin = '/auth/login';
  static const String authRegister = '/auth/register';
  static const String authLogout = '/auth/logout';
  static const String me = '/me';
  static const String meUpgrade = '/me/upgrade';
  static const String emergency = '/emergency';
  static const String conversations = '/conversations';
  static const String professionals = '/professionals';
  static const String bookings = '/bookings';
  static const String courses = '/courses';
  static const String enrollments = '/enrollments';
  static const String subscription = '/subscription';
  static const String payments = '/payments';
  static const String broadcastingAuth = '/broadcasting/auth';
  static const String safetyPlan = '/safety-plan';
  static const String moods = '/moods';
  static const String exercises = '/exercises';
  static const String guides = '/guides';
  static const String resources = '/resources';
  static const String preferredListener = '/me/preferred-listener';
  static const String devices = '/me/devices';

  static String guide(String slug) => '$guides/$slug';

  static String conversation(int id) => '$conversations/$id';
  static String conversationMessages(int id) => '$conversations/$id/messages';
  static String conversationClose(int id) => '$conversations/$id/close';
  static String conversationRating(int id) => '$conversations/$id/rating';
  static String conversationReports(int id) => '$conversations/$id/reports';
  static String professional(int id) => '$professionals/$id';
  static String booking(int id) => '$bookings/$id';
  static String bookingPay(int id) => '$bookings/$id/pay';
  static String bookingCancel(int id) => '$bookings/$id/cancel';
  static String course(String slug) => '$courses/$slug';
  static String courseEnroll(String slug) => '$courses/$slug/enroll';
  static String coursePurchase(String slug) => '$courses/$slug/purchase';
  static String lesson(String slug, int id) => '$courses/$slug/lessons/$id';
  static String lessonComplete(String slug, int id) => '$courses/$slug/lessons/$id/complete';
  static String payment(String reference) => '$payments/$reference';
}
