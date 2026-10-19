import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../theme/app_colors.dart';
import '../theme/app_spacing.dart';
import '../theme/app_text_styles.dart';

class PremiumNavItem {
  const PremiumNavItem({required this.icon, required this.activeIcon, required this.label});

  final IconData icon;
  final IconData activeIcon;
  final String label;
}

/// Barre de navigation flottante : pilule violet nuit, onglet actif mis en avant
/// par une capsule claire qui s'élargit pour révéler son libellé.
class PremiumNavBar extends StatelessWidget {
  const PremiumNavBar({super.key, required this.items, required this.currentIndex, required this.onTap});

  final List<PremiumNavItem> items;
  final int currentIndex;
  final ValueChanged<int> onTap;

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      minimum: const EdgeInsets.fromLTRB(AppSpacing.lg, 0, AppSpacing.lg, AppSpacing.md),
      child: Container(
        height: 68,
        padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm),
        decoration: BoxDecoration(
          gradient: const LinearGradient(colors: [AppColors.violet800, AppColors.violet950], begin: Alignment.topLeft, end: Alignment.bottomRight),
          borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
          boxShadow: [
            BoxShadow(color: AppColors.violet950.withValues(alpha: 0.28), blurRadius: 24, offset: const Offset(0, 10)),
          ],
        ),
        child: Row(
          children: [
            for (final (index, item) in items.indexed)
              Expanded(
                flex: index == currentIndex ? 2 : 1,
                child: _NavButton(
                  item: item,
                  selected: index == currentIndex,
                  onTap: () {
                    if (index != currentIndex) {
                      HapticFeedback.selectionClick();
                      onTap(index);
                    }
                  },
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _NavButton extends StatelessWidget {
  const _NavButton({required this.item, required this.selected, required this.onTap});

  final PremiumNavItem item;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Semantics(
      button: true,
      selected: selected,
      label: item.label,
      child: GestureDetector(
        onTap: onTap,
        behavior: HitTestBehavior.opaque,
        child: Center(
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 320),
            curve: Curves.easeOutCubic,
            height: 48,
            padding: EdgeInsets.symmetric(horizontal: selected ? AppSpacing.lg : 0),
            decoration: BoxDecoration(
              color: selected ? AppColors.white : Colors.transparent,
              borderRadius: BorderRadius.circular(AppSpacing.radiusPill),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                AnimatedSwitcher(
                  duration: const Duration(milliseconds: 200),
                  child: Icon(
                    selected ? item.activeIcon : item.icon,
                    key: ValueKey(selected),
                    size: 23,
                    color: selected ? AppColors.violet800 : AppColors.lilac200.withValues(alpha: 0.75),
                  ),
                ),
                Flexible(
                  child: AnimatedSize(
                    duration: const Duration(milliseconds: 320),
                    curve: Curves.easeOutCubic,
                    child: selected
                        ? Padding(
                            padding: const EdgeInsets.only(left: AppSpacing.sm),
                            child: Text(
                              item.label,
                              maxLines: 1,
                              overflow: TextOverflow.fade,
                              softWrap: false,
                              style: AppTextStyles.label.copyWith(color: AppColors.violet800),
                            ),
                          )
                        : const SizedBox.shrink(),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
