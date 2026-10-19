import 'plan.dart';

class Subscription {
  const Subscription({required this.status, this.plan, this.endsAt});

  final String status;
  final Plan? plan;
  final DateTime? endsAt;

  bool get isActive => status == 'active';
}
