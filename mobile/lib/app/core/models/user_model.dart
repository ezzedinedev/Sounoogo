import 'plan_model.dart';

class UserModel {
  const UserModel({
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

  factory UserModel.fromJson(Map<String, dynamic> json) => UserModel(
        id: json['id'] as int,
        pseudonym: json['pseudonym'] as String,
        role: json['role'] as String? ?? 'member',
        isAnonymous: json['is_anonymous'] as bool? ?? false,
        country: json['country'] as String? ?? 'BF',
        name: json['name'] as String?,
        email: json['email'] as String?,
        phone: json['phone'] as String?,
        subscription: json['subscription'] == null ? null : SubscriptionModel.fromJson(json['subscription'] as Map<String, dynamic>),
      );

  final int id;
  final String pseudonym;
  final String role;
  final bool isAnonymous;
  final String country;
  final String? name;
  final String? email;
  final String? phone;
  final SubscriptionModel? subscription;
}
