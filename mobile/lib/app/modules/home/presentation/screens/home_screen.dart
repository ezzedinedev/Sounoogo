import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../core/widgets/premium_nav_bar.dart';
import '../../../courses/presentation/screens/courses_tab.dart';
import '../../../listening/presentation/screens/listening_tab.dart';
import '../../../professionals/presentation/screens/professionals_tab.dart';
import '../../../profile/presentation/screens/profile_tab.dart';
import '../controllers/home_controller.dart';
import 'home_tab.dart';

class HomeScreen extends GetView<HomeController> {
  const HomeScreen({super.key});

  static const _items = [
    PremiumNavItem(icon: Icons.home_outlined, activeIcon: Icons.home_rounded, label: 'Accueil'),
    PremiumNavItem(icon: Icons.chat_bubble_outline_rounded, activeIcon: Icons.chat_bubble_rounded, label: 'Écoute'),
    PremiumNavItem(icon: Icons.medical_services_outlined, activeIcon: Icons.medical_services_rounded, label: 'Pros'),
    PremiumNavItem(icon: Icons.auto_stories_outlined, activeIcon: Icons.auto_stories_rounded, label: 'Parcours'),
    PremiumNavItem(icon: Icons.person_outline_rounded, activeIcon: Icons.person_rounded, label: 'Moi'),
  ];

  @override
  Widget build(BuildContext context) {
    return Obx(
      () => Scaffold(
        extendBody: true,
        body: IndexedStack(
          index: controller.tab.value,
          children: const [HomeTab(), ListeningTab(), ProfessionalsTab(), CoursesTab(), ProfileTab()],
        ),
        bottomNavigationBar: PremiumNavBar(items: _items, currentIndex: controller.tab.value, onTap: controller.goTo),
      ),
    );
  }
}
