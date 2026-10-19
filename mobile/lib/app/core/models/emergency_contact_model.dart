class EmergencyContactModel {
  const EmergencyContactModel({required this.label, required this.phone, this.description});

  factory EmergencyContactModel.fromJson(Map<String, dynamic> json) => EmergencyContactModel(
        label: json['label'] as String,
        phone: json['phone'] as String,
        description: json['description'] as String?,
      );

  final String label;
  final String phone;
  final String? description;
}
