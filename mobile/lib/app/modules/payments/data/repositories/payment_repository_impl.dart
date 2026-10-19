import '../../../../core/error/failure_mapper.dart';
import '../../domain/entities/payment.dart';
import '../../domain/entities/payment_target.dart';
import '../../domain/repositories/payment_repository.dart';
import '../datasources/payment_remote_data_source.dart';

class PaymentRepositoryImpl implements PaymentRepository {
  PaymentRepositoryImpl(this._remote);

  final PaymentRemoteDataSource _remote;

  @override
  Future<Payment> start(PaymentTarget target, String provider) => guardApi(() => _remote.start(target, provider));

  @override
  Future<Payment> find(String reference) => guardApi(() => _remote.find(reference));
}
