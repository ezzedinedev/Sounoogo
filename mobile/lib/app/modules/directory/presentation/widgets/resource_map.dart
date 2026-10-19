import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../core/theme/app_colors.dart';
import '../../domain/entities/support_resource.dart';

/// Carte OpenStreetMap des ressources géolocalisées.
class ResourceMap extends StatelessWidget {
  const ResourceMap({super.key, required this.resources, required this.selected, required this.onSelect});

  final List<SupportResource> resources;
  final SupportResource? selected;
  final ValueChanged<SupportResource?> onSelect;

  /// Ouagadougou, quand aucune ressource n'est géolocalisée.
  static const _fallback = LatLng(12.3714, -1.5197);

  @override
  Widget build(BuildContext context) {
    final points = [for (final r in resources.where((r) => r.isOnMap)) LatLng(r.latitude!, r.longitude!)];

    return FlutterMap(
      options: MapOptions(
        initialCameraFit: points.length > 1 ? CameraFit.coordinates(coordinates: points, padding: const EdgeInsets.all(56)) : null,
        initialCenter: points.length == 1 ? points.single : _fallback,
        initialZoom: 13,
        onTap: (_, _) => onSelect(null),
      ),
      children: [
        TileLayer(urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', userAgentPackageName: 'app.sunoogo.sunoogo'),
        MarkerLayer(
          markers: [
            for (final resource in resources.where((r) => r.isOnMap))
              Marker(
                point: LatLng(resource.latitude!, resource.longitude!),
                width: 48,
                height: 48,
                alignment: Alignment.topCenter,
                child: GestureDetector(
                  onTap: () => onSelect(resource),
                  child: Semantics(
                    button: true,
                    label: resource.name,
                    child: AnimatedScale(
                      scale: selected?.id == resource.id ? 1.25 : 1,
                      duration: const Duration(milliseconds: 220),
                      curve: Curves.easeOutBack,
                      alignment: Alignment.bottomCenter,
                      child: Icon(
                        Icons.location_on_rounded,
                        size: 48,
                        color: selected?.id == resource.id ? AppColors.violet800 : AppColors.violet500,
                        shadows: [Shadow(color: AppColors.violet950.withValues(alpha: 0.3), blurRadius: 8)],
                      ),
                    ),
                  ),
                ),
              ),
          ],
        ),
        RichAttributionWidget(
          attributions: [
            TextSourceAttribution('Contributeurs OpenStreetMap', onTap: () => launchUrl(Uri.parse('https://openstreetmap.org/copyright'))),
          ],
        ),
      ],
    );
  }
}
