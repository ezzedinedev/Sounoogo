/// Attestation de fin de formation, vérifiable par son code.
class Certificate {
  const Certificate({
    required this.code,
    required this.holderName,
    required this.issuedAt,
    required this.courseSlug,
    required this.courseTitle,
    required this.pdfUrl,
    required this.verificationUrl,
  });

  final String code;
  final String holderName;
  final DateTime issuedAt;
  final String courseSlug;
  final String courseTitle;
  final String pdfUrl;
  final String verificationUrl;
}
