class AppConfig {
  static const String appName = 'Lentera';
  static const String universityName = 'Universitas Madura';
  static const String appTagline = 'Menerangi Karya, Menginspirasi Generasi';

  // Alamat base API yang dapat dikonfigurasi secara dinamis via compile-time define
  // Contoh: flutter run --dart-define=API_URL=http://192.168.1.10/lentera/backend/api
  static const String defaultApiUrl = 'http://10.0.2.2/lentera/backend/api';
  
  static String get baseUrl {
    const String configuredUrl = String.fromEnvironment('API_URL');
    if (configuredUrl.isNotEmpty) {
      return configuredUrl;
    }
    return defaultApiUrl;
  }

  // Batas waktu koneksi jaringan (detik)
  static const Duration timeoutDuration = Duration(seconds: 15);

  // Palet Warna Resmi Lentera Design System
  static const int tealPrimaryValue = 0xFF0F4C5C;
  static const int tealDarkValue    = 0xFF0A2F3A;
  static const int goldAccentValue  = 0xFFFB8B24;
  static const int bgLightValue     = 0xFFF8FAFC;
}
