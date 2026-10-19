import '../../professionals/models/professional_model.dart';

class BookingModel {
  const BookingModel({
    required this.id,
    required this.status,
    required this.statusLabel,
    required this.modeLabel,
    required this.price,
    this.startsAt,
    this.professionalName,
  });

  factory BookingModel.fromJson(Map<String, dynamic> json) {
    final slot = json['slot'] == null ? null : SlotModel.fromJson(json['slot'] as Map<String, dynamic>);
    return BookingModel(
      id: json['id'] as int,
      status: json['status'] as String,
      statusLabel: json['status_label'] as String,
      modeLabel: json['mode_label'] as String,
      price: json['price'] as int,
      startsAt: slot?.startsAt,
      professionalName: (json['professional'] as Map<String, dynamic>?)?['name'] as String?,
    );
  }

  final int id;
  final String status;
  final String statusLabel;
  final String modeLabel;
  final int price;
  final DateTime? startsAt;
  final String? professionalName;

  bool get awaitsPayment => status == 'pending_payment';
  bool get isCancellable => status == 'pending_payment' || status == 'confirmed';
}
