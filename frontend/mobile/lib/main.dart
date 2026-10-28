import 'package:flutter/material.dart';
import 'config/app_config.dart';
import 'views/home_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const LenteraApp());
}

class LenteraApp extends StatelessWidget {
  const LenteraApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: AppConfig.appName,
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(AppConfig.tealPrimaryValue),
          primary: const Color(AppConfig.tealPrimaryValue),
          secondary: const Color(AppConfig.goldAccentValue),
          surface: Colors.white,
        ),
        scaffoldBackgroundColor: const Color(AppConfig.bgLightValue),
        appBarTheme: const AppBarTheme(
          backgroundColor: Colors.white,
          foregroundColor: Color(AppConfig.tealDarkValue),
          elevation: 0,
        ),
      ),
      home: const HomeScreen(),
    );
  }
}
