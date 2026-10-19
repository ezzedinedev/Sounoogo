import 'package:get/get.dart';

import '../../../bookings/domain/usecases/book_consultation.dart';
import '../../../bookings/presentation/bindings/bookings_binding.dart';
import '../../data/datasources/professional_remote_data_source.dart';
import '../../data/repositories/professional_repository_impl.dart';
import '../../domain/repositories/professional_repository.dart';
import '../../domain/usecases/get_professional.dart';
import '../../domain/usecases/get_professionals.dart';
import '../controllers/professional_detail_controller.dart';
import '../controllers/professionals_controller.dart';

void _registerRepository() =>
    Get.lazyPut<ProfessionalRepository>(() => ProfessionalRepositoryImpl(ProfessionalRemoteDataSource(Get.find())), fenix: true);

class ProfessionalsBinding extends Bindings {
  @override
  void dependencies() {
    _registerRepository();
    Get.lazyPut(() => ProfessionalsController(GetProfessionals(Get.find())), fenix: true);
  }
}

class ProfessionalDetailBinding extends Bindings {
  @override
  void dependencies() {
    _registerRepository();
    BookingsDataBinding().dependencies();
    Get.lazyPut(
      () => ProfessionalDetailController(getProfessional: GetProfessional(Get.find()), bookConsultation: BookConsultation(Get.find())),
    );
  }
}
