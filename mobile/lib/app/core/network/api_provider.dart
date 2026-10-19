import 'package:dio/dio.dart';

import '../constants/api_constants.dart';
import 'api_exception.dart';

/// Client HTTP unique de l'app. Les data sources le reçoivent par constructeur.
class ApiProvider {
  ApiProvider({required Future<String?> Function() tokenReader, this.onUnauthorized})
    : _dio = Dio(
        BaseOptions(
          baseUrl: ApiConstants.baseUrl,
          connectTimeout: const Duration(seconds: 15),
          receiveTimeout: const Duration(seconds: 20),
          headers: {'Accept': 'application/json'},
        ),
      ) {
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await tokenReader();
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          handler.next(options);
        },
      ),
    );
  }

  final Dio _dio;

  /// Appelé quand le jeton n'est plus valide (session expirée ou compte suspendu).
  final Future<void> Function()? onUnauthorized;

  Future<Map<String, dynamic>> get(String path, {Map<String, dynamic>? query}) =>
      _send(() => _dio.get<Map<String, dynamic>>(path, queryParameters: query));

  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? data}) =>
      _send(() => _dio.post<Map<String, dynamic>>(path, data: data));

  Future<Map<String, dynamic>> patch(String path, {Map<String, dynamic>? data}) =>
      _send(() => _dio.patch<Map<String, dynamic>>(path, data: data));

  Future<Map<String, dynamic>> delete(String path) => _send(() => _dio.delete<Map<String, dynamic>>(path));

  Future<Map<String, dynamic>> _send(Future<Response<Map<String, dynamic>>> Function() request) async {
    try {
      final response = await request();
      return response.data ?? const {};
    } on DioException catch (error) {
      final exception = _toApiException(error);
      if (exception.isUnauthorized) {
        await onUnauthorized?.call();
      }
      throw exception;
    }
  }

  ApiException _toApiException(DioException error) {
    final response = error.response;
    if (response == null) {
      return const ApiException('Connexion impossible. Vérifie ta connexion internet et réessaie.');
    }

    final body = response.data is Map<String, dynamic> ? response.data as Map<String, dynamic> : const <String, dynamic>{};
    final errors = (body['errors'] as Map<String, dynamic>? ?? const {}).map(
      (field, messages) => MapEntry(field, (messages as List).map((m) => m.toString()).toList()),
    );

    final message = switch (response.statusCode) {
      422 => errors.values.firstOrNull?.firstOrNull ?? 'Certaines informations sont invalides.',
      429 => 'Trop de tentatives. Patiente un instant avant de réessayer.',
      final code? when code >= 500 => 'Le service est momentanément indisponible. Réessaie dans un instant.',
      _ => body['message']?.toString() ?? 'Une erreur est survenue.',
    };

    return ApiException(message, statusCode: response.statusCode, fieldErrors: errors);
  }
}
