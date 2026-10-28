import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../models/work_model.dart';
import '../models/user_model.dart';
import '../models/faculty_model.dart';

class ApiResult<T> {
  final bool isSuccess;
  final T? data;
  final String message;

  ApiResult.success(this.data, [this.message = 'Berhasil memproses data.']) : isSuccess = true;
  ApiResult.failure(this.message) : isSuccess = false, data = null;
}

class ApiService {
  static const String tokenKey = 'lentera_auth_token';
  static const String userKey = 'lentera_user_data';

  // Helper HTTP Headers
  static Future<Map<String, String>> _getHeaders({bool requireAuth = false}) async {
    final headers = {
      'Content-Type': 'application/json; charset=UTF-8',
      'Accept': 'application/json',
    };
    if (requireAuth) {
      final token = await getToken();
      if (token != null && token.isNotEmpty) {
        headers['Authorization'] = 'Bearer $token';
      }
    }
    return headers;
  }

  // Pengelolaan Sesi Token
  static Future<void> saveSession(String token, UserModel user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(tokenKey, token);
    await prefs.setString(userKey, jsonEncode(user.toJson()));
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(tokenKey);
  }

  static Future<UserModel?> getCurrentUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(userKey);
    if (userJson != null) {
      try {
        return UserModel.fromJson(jsonDecode(userJson));
      } catch (_) {
        return null;
      }
    }
    return null;
  }

  static Future<void> clearSession() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(tokenKey);
    await prefs.remove(userKey);
  }

  // 1. Autentikasi Login
  static Future<ApiResult<Map<String, dynamic>>> login(String identifier, String password) async {
    final url = Uri.parse('${AppConfig.baseUrl}/auths/logins.php');
    try {
      final headers = await _getHeaders();
      final body = jsonEncode({
        'identifiers': identifier.trim(),
        'passwords': password,
      });

      final response = await http
          .post(url, headers: headers, body: body)
          .timeout(AppConfig.timeoutDuration);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['success'] == true) {
        final token = decoded['data']['token']?.toString() ?? '';
        final user = UserModel.fromJson(decoded['data']['user']);
        await saveSession(token, user);
        return ApiResult.success({'token': token, 'user': user}, decoded['message'] ?? 'Login berhasil.');
      } else {
        return ApiResult.failure(decoded['message'] ?? 'Identitas atau kata sandi tidak valid.');
      }
    } on SocketException {
      return ApiResult.failure('Gagal terhubung ke server. Periksa koneksi internet atau status server Lentera.');
    } on TimeoutException {
      return ApiResult.failure('Koneksi ke server batas waktu terlampaui (timeout). Silakan coba kembali.');
    } catch (e) {
      return ApiResult.failure('Terjadi kendala jaringan: ${e.toString()}');
    }
  }

  // 2. Mengambil Karya Ilmiah Terkini
  static Future<ApiResult<List<WorkModel>>> getRecentWorks({int limit = 10}) async {
    final url = Uri.parse('${AppConfig.baseUrl}/workss/recents.php?limits=$limit');
    try {
      final headers = await _getHeaders();
      final response = await http.get(url, headers: headers).timeout(AppConfig.timeoutDuration);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['success'] == true) {
        final List items = decoded['data'] ?? [];
        final works = items.map((item) => WorkModel.fromJson(item)).toList();
        return ApiResult.success(works);
      }
      return ApiResult.failure(decoded['message'] ?? 'Gagal memuat katalog karya.');
    } on SocketException {
      return ApiResult.failure('Koneksi offline: Tidak dapat menjangkau server.');
    } on TimeoutException {
      return ApiResult.failure('Waktu koneksi habis saat memuat data beranda.');
    } catch (e) {
      return ApiResult.failure('Galat data: ${e.toString()}');
    }
  }

  // 3. Pencarian & Filter Katalog
  static Future<ApiResult<List<WorkModel>>> searchWorks({
    String query = '',
    String facultyCode = '',
    int limit = 15,
  }) async {
    final queryParams = <String, String>{
      'limits': limit.toString(),
    };
    if (query.isNotEmpty) queryParams['q'] = query;
    if (facultyCode.isNotEmpty) queryParams['faculties'] = facultyCode;

    final url = Uri.parse('${AppConfig.baseUrl}/workss/searchs.php').replace(queryParameters: queryParams);
    try {
      final headers = await _getHeaders();
      final response = await http.get(url, headers: headers).timeout(AppConfig.timeoutDuration);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['success'] == true) {
        final List items = decoded['data']?['items'] ?? [];
        final works = items.map((item) => WorkModel.fromJson(item)).toList();
        return ApiResult.success(works, decoded['message'] ?? 'Hasil pencarian ditemukan.');
      }
      return ApiResult.failure(decoded['message'] ?? 'Gagal memproses pencarian.');
    } on SocketException {
      return ApiResult.failure('Perangkat sedang offline.');
    } on TimeoutException {
      return ApiResult.failure('Pencarian memerlukan waktu terlalu lama.');
    } catch (e) {
      return ApiResult.failure('Galat pencarian: ${e.toString()}');
    }
  }

  // 4. Detail Karya Ilmiah
  static Future<ApiResult<WorkModel>> getWorkDetail(int id) async {
    final url = Uri.parse('${AppConfig.baseUrl}/workss/details.php?ids=$id');
    try {
      final headers = await _getHeaders(requireAuth: true);
      final response = await http.get(url, headers: headers).timeout(AppConfig.timeoutDuration);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['success'] == true) {
        final work = WorkModel.fromJson(decoded['data']);
        return ApiResult.success(work);
      }
      return ApiResult.failure(decoded['message'] ?? 'Naskah karya tidak ditemukan.');
    } on SocketException {
      return ApiResult.failure('Gagal memuat detail karya secara offline.');
    } on TimeoutException {
      return ApiResult.failure('Batas waktu koneksi terlampaui saat memuat detail.');
    } catch (e) {
      return ApiResult.failure('Galat: ${e.toString()}');
    }
  }

  // 5. Daftar Fakultas
  static Future<ApiResult<List<FacultyModel>>> getFaculties() async {
    final url = Uri.parse('${AppConfig.baseUrl}/facultiess/indexs.php');
    try {
      final headers = await _getHeaders();
      final response = await http.get(url, headers: headers).timeout(AppConfig.timeoutDuration);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['success'] == true) {
        final List items = decoded['data'] ?? [];
        final faculties = items.map((item) => FacultyModel.fromJson(item)).toList();
        return ApiResult.success(faculties);
      }
      return ApiResult.failure(decoded['message'] ?? 'Gagal memuat daftar fakultas.');
    } catch (e) {
      return ApiResult.failure('Tidak dapat memuat fakultas: ${e.toString()}');
    }
  }

  // 6. Karya Saya (Penulis Terautentikasi)
  static Future<ApiResult<List<WorkModel>>> getAuthorWorks() async {
    final url = Uri.parse('${AppConfig.baseUrl}/authors/workss.php');
    try {
      final headers = await _getHeaders(requireAuth: true);
      final response = await http.get(url, headers: headers).timeout(AppConfig.timeoutDuration);

      final decoded = jsonDecode(response.body);
      if (response.statusCode == 200 && decoded['success'] == true) {
        final List items = decoded['data']?['works'] ?? [];
        final works = items.map((item) => WorkModel.fromJson(item)).toList();
        return ApiResult.success(works);
      }
      return ApiResult.failure(decoded['message'] ?? 'Gagal memuat karya Anda.');
    } on SocketException {
      return ApiResult.failure('Gagal memuat karya pribadi saat offline.');
    } catch (e) {
      return ApiResult.failure('Kendala autentikasi: ${e.toString()}');
    }
  }
}
