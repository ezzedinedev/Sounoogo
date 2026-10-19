import 'emergency_contact.dart';
import 'payment_provider.dart';
import 'plan.dart';

/// Données de référence publiées par l'API au démarrage.
class ReferenceData {
  const ReferenceData({required this.emergencyContacts, required this.paymentProviders, required this.plans}) : isFallback = false;

  /// Utilisées hors ligne : l'urgence doit rester accessible sans réseau.
  const ReferenceData.fallback()
    : emergencyContacts = const [
        EmergencyContact(label: 'Police secours', phone: '17'),
        EmergencyContact(label: 'Sapeurs-pompiers', phone: '18'),
      ],
      paymentProviders = const [],
      plans = const [],
      isFallback = true;

  final List<EmergencyContact> emergencyContacts;
  final List<PaymentProvider> paymentProviders;
  final List<Plan> plans;
  final bool isFallback;
}
