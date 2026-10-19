import '../../domain/entities/user.dart';
import 'plan_model.dart';

/// Conversion de la ressource `UserResource` de l'API vers l'entité [User].
abstract final class UserModel {
  static User fromJson(Map<String, dynamic> json) => User(
    id: json['id'] as int,
    pseudonym: json['pseudonym'] as String,
    role: json['role'] as String? ?? 'member',
    isAnonymous: json['is_anonymous'] as bool? ?? false,
    country: json['country'] as String? ?? 'BF',
    name: json['name'] as String?,
    email: json['email'] as String?,
    phone: json['phone'] as String?,
    subscription: json['subscription'] == null ? null : SubscriptionModel.fromJson(json['subscription'] as Map<String, dynamic>),
    preferredListener: switch (json['preferred_listener']) {
      {'display_name': final String name, 'is_available': final bool available} => PreferredListener(
        displayName: name,
        isAvailable: available,
      ),
      _ => null,
    },
  );
}
