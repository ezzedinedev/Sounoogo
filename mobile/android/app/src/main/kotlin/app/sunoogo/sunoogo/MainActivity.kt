package app.sunoogo.sunoogo

import android.view.WindowManager
import io.flutter.embedding.android.FlutterFragmentActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel

// FlutterFragmentActivity : requis par local_auth pour l'invite biométrique.
class MainActivity : FlutterFragmentActivity() {
    override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)

        // Mode discret : FLAG_SECURE masque l'app dans les apps récentes et bloque les captures.
        MethodChannel(flutterEngine.dartExecutor.binaryMessenger, "app.sunoogo/privacy").setMethodCallHandler { call, result ->
            when (call.method) {
                "setSecureScreen" -> {
                    if (call.arguments == true) {
                        window.addFlags(WindowManager.LayoutParams.FLAG_SECURE)
                    } else {
                        window.clearFlags(WindowManager.LayoutParams.FLAG_SECURE)
                    }
                    result.success(null)
                }
                else -> result.notImplemented()
            }
        }
    }
}
