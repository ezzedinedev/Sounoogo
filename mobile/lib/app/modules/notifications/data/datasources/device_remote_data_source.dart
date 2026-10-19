import 'package:flutter/foundation.dart';

import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/api_provider.dart';

class DeviceRemoteDataSource {
  DeviceRemoteDataSource(this._api);

  final ApiProvider _api;

  String get _platform => defaultTargetPlatform == TargetPlatform.iOS ? 'ios' : 'android';

  Future<void> register(String token) => _api.post(ApiConstants.devices, data: {'token': token, 'platform': _platform});

  Future<void> unregister(String token) => _api.delete(ApiConstants.devices, data: {'token': token});
}
