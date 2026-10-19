import 'package:get/get.dart';

import '../../data/datasources/booking_remote_data_source.dart';
import '../../data/repositories/booking_repository_impl.dart';
import '../../domain/repositories/booking_repository.dart';
import '../../domain/usecases/cancel_booking.dart';
import '../../domain/usecases/get_bookings.dart';
import '../../domain/usecases/join_video_consultation.dart';
import '../controllers/bookings_controller.dart';

/// Données des réservations, partagées avec la fiche d'un professionnel (réserver un créneau).
class BookingsDataBinding extends Bindings {
  @override
  void dependencies() => Get.lazyPut<BookingRepository>(() => BookingRepositoryImpl(BookingRemoteDataSource(Get.find())), fenix: true);
}

class BookingsBinding extends Bindings {
  @override
  void dependencies() {
    BookingsDataBinding().dependencies();
    Get.lazyPut(() {
      final BookingRepository repository = Get.find();
      return BookingsController(
        getBookings: GetBookings(repository),
        cancelBooking: CancelBooking(repository),
        joinVideoConsultation: JoinVideoConsultation(repository),
      );
    });
  }
}
