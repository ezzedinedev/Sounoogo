/// Ce que le membre paie. La couche données en déduit la route de l'API.
sealed class PaymentTarget {
  const PaymentTarget();
}

final class BookingPayment extends PaymentTarget {
  const BookingPayment(this.bookingId);

  final int bookingId;
}

final class CoursePurchase extends PaymentTarget {
  const CoursePurchase(this.courseSlug);

  final String courseSlug;
}

final class SubscriptionPayment extends PaymentTarget {
  const SubscriptionPayment(this.planCode);

  final String planCode;
}
