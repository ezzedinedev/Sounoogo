/// Notification ouverte par le membre : où l'emmener.
sealed class PushTarget {
  const PushTarget();

  static PushTarget? fromData(Map<String, dynamic> data) => switch (data['type']) {
    'conversation' => const ConversationTarget(),
    'booking' => const BookingTarget(),
    'checkin' => const CheckInTarget(),
    _ => null,
  };
}

final class ConversationTarget extends PushTarget {
  const ConversationTarget();
}

final class BookingTarget extends PushTarget {
  const BookingTarget();
}

final class CheckInTarget extends PushTarget {
  const CheckInTarget();
}
