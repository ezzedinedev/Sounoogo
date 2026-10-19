import 'dart:async';

import 'package:dart_pusher_channels/dart_pusher_channels.dart';
import 'package:get/get.dart';

import '../constants/api_constants.dart';
import '../storage/token_storage.dart';

/// Connexion temps réel à Laravel Reverb (protocole Pusher).
/// Brique technique : les data sources s'en servent, jamais les écrans.
class RealtimeService extends GetxService {
  RealtimeService(this._tokens);

  final TokenStorage _tokens;
  PusherChannelsClient? _client;
  StreamSubscription<void>? _connection;
  final _channels = <String, PrivateChannel>{};

  Future<void> _ensureConnected() async {
    if (_client != null) {
      return;
    }

    final client = PusherChannelsClient.websocket(
      options: PusherChannelsOptions.fromHost(
        scheme: ApiConstants.reverbTls ? 'wss' : 'ws',
        host: ApiConstants.reverbHost,
        port: ApiConstants.reverbPort,
        key: ApiConstants.reverbKey,
        shouldSupplyMetadataQueries: true,
        metadata: PusherChannelsOptionsMetadata.byDefault(),
      ),
      connectionErrorHandler: (exception, trace, refresh) => refresh(),
    );

    _connection = client.onConnectionEstablished.listen((_) {
      for (final channel in _channels.values) {
        channel.subscribeIfNotUnsubscribed();
      }
    });
    _client = client;
    unawaited(client.connect());
  }

  /// Écoute un événement d'un canal privé (`private-conversation.12`, `message.sent`).
  Future<Stream<Map<String, dynamic>>> listen(String channelName, String event) async {
    await _ensureConnected();
    final token = await _tokens.read();
    final authUrl = '${ApiConstants.baseUrl.replaceFirst(RegExp(r'/v1$'), '')}${ApiConstants.broadcastingAuth}';

    final channel = _channels.putIfAbsent(
      channelName,
      () => _client!.privateChannel(
        'private-$channelName',
        authorizationDelegate: EndpointAuthorizableChannelTokenAuthorizationDelegate.forPrivateChannel(
          authorizationEndpoint: Uri.parse(authUrl),
          headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
        ),
      ),
    );
    channel.subscribeIfNotUnsubscribed();

    return channel.bind(event).map((e) => e.tryGetDataAsMap() ?? const {});
  }

  void leave(String channelName) => _channels.remove(channelName)?.unsubscribe();

  @override
  void onClose() {
    _connection?.cancel();
    _client?.dispose();
    super.onClose();
  }
}
