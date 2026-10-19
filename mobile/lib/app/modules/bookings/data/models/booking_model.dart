import '../../domain/entities/booking.dart';

abstract final class BookingModel {
  static Booking fromJson(Map<String, dynamic> json) {
    final slot = json['slot'] as Map<String, dynamic>?;
    return Booking(
      id: json['id'] as int,
      status: json['status'] as String,
      statusLabel: json['status_label'] as String,
      modeLabel: json['mode_label'] as String,
      price: json['price'] as int,
      startsAt: slot == null ? null : DateTime.parse(slot['starts_at'] as String).toLocal(),
      professionalName: (json['professional'] as Map<String, dynamic>?)?['name'] as String?,
    );
  }
}
