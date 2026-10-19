class PlanModel {
  const PlanModel({
    required this.code,
    required this.name,
    required this.price,
    required this.durationDays,
    required this.consultationDiscount,
    required this.features,
    this.description,
  });

  factory PlanModel.fromJson(Map<String, dynamic> json) => PlanModel(
        code: json['code'] as String,
        name: json['name'] as String,
        price: json['price'] as int,
        durationDays: json['duration_days'] as int? ?? 30,
        consultationDiscount: json['consultation_discount'] as int? ?? 0,
        features: (json['features'] as List? ?? const []).cast<String>(),
        description: json['description'] as String?,
      );

  final String code;
  final String name;
  final int price;
  final int durationDays;
  final int consultationDiscount;
  final List<String> features;
  final String? description;
}

class SubscriptionModel {
  const SubscriptionModel({required this.status, this.plan, this.endsAt});

  factory SubscriptionModel.fromJson(Map<String, dynamic> json) => SubscriptionModel(
        status: json['status'] as String,
        plan: json['plan'] == null ? null : PlanModel.fromJson(json['plan'] as Map<String, dynamic>),
        endsAt: DateTime.tryParse(json['ends_at'] as String? ?? '')?.toLocal(),
      );

  final String status;
  final PlanModel? plan;
  final DateTime? endsAt;

  bool get isActive => status == 'active';
}
