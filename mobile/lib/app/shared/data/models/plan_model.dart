import '../../domain/entities/plan.dart';
import '../../domain/entities/subscription.dart';

abstract final class PlanModel {
  static Plan fromJson(Map<String, dynamic> json) => Plan(
    code: json['code'] as String,
    name: json['name'] as String,
    price: json['price'] as int,
    durationDays: json['duration_days'] as int? ?? 30,
    consultationDiscount: json['consultation_discount'] as int? ?? 0,
    features: (json['features'] as List? ?? const []).cast<String>(),
    description: json['description'] as String?,
  );
}

abstract final class SubscriptionModel {
  static Subscription fromJson(Map<String, dynamic> json) => Subscription(
    status: json['status'] as String,
    plan: json['plan'] == null ? null : PlanModel.fromJson(json['plan'] as Map<String, dynamic>),
    endsAt: DateTime.tryParse(json['ends_at'] as String? ?? '')?.toLocal(),
  );
}
