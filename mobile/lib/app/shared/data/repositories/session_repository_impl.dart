import '../../../core/error/failure_mapper.dart';
import '../../../core/network/api_exception.dart';
import '../../../core/storage/secure_cache.dart';
import '../../../core/storage/token_storage.dart';
import '../../domain/entities/user.dart';
import '../../domain/repositories/session_repository.dart';
import '../datasources/session_remote_data_source.dart';

class SessionRepositoryImpl implements SessionRepository {
  SessionRepositoryImpl(this._remote, this._tokens, this._cache);

  final SessionRemoteDataSource _remote;
  final TokenStorage _tokens;
  final SecureCache _cache;

  @override
  Future<bool> hasToken() async => await _tokens.read() != null;

  @override
  Future<User> currentUser() => guardApi(_remote.me);

  @override
  Future<void> save(String token) => _tokens.write(token);

  @override
  Future<void> clear({bool remote = true}) async {
    if (remote) {
      try {
        await _remote.logout();
      } on ApiException {
        // La révocation est au mieux : on efface le jeton local quoi qu'il arrive.
      }
    }
    await _tokens.clear();
    // Plus rien de personnel ne reste sur l'appareil après la déconnexion.
    await _cache.clear();
  }
}
