import 'subscription.dart';

/// Membre connecté. Un membre anonyme n'a ni nom, ni e-mail, ni téléphone.
class User {
  const User({
    required this.id,
    required this.pseudonym,
    required this.role,
    required this.isAnonymous,
    required this.country,
    this.name,
    this.email,
    this.phone,
    this.subscription,
  });

  final int id;
  final String pseudonym;
  final String role;
  final bool isAnonymous;
  final String country;
  final String? name;
  final String? email;
  final String? phone;
  final Subscription? subscription;
}
