class Payment {
  const Payment({required this.reference, required this.amount, required this.status, this.label, this.checkoutUrl});

  final String reference;

  /// Montant en francs CFA.
  final int amount;
  final String status;
  final String? label;

  /// Page du prestataire (Wave, Orange Money) où le membre valide le paiement.
  final String? checkoutUrl;

  bool get isPending => status == 'pending';
  bool get isSucceeded => status == 'succeeded';
}
