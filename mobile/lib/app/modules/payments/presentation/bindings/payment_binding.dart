import 'package:get/get.dart';

import '../../data/datasources/payment_remote_data_source.dart';
import '../../data/repositories/payment_repository_impl.dart';
import '../../domain/repositories/payment_repository.dart';
import '../../domain/usecases/get_payment.dart';
import '../../domain/usecases/start_payment.dart';
import '../controllers/payment_controller.dart';

/// Global : réservations, formations et abonnements passent tous par ce parcours.
class PaymentBinding extends Bindings {
  @override
  void dependencies() {
    Get
      ..lazyPut<PaymentRepository>(() => PaymentRepositoryImpl(PaymentRemoteDataSource(Get.find())), fenix: true)
      ..lazyPut(() {
        final PaymentRepository repository = Get.find();
        return PaymentController(startPayment: StartPayment(repository), getPayment: GetPayment(repository));
      }, fenix: true);
  }
}
