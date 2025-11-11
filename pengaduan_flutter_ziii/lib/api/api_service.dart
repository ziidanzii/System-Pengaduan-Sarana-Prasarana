import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static String get baseUrl {
    if (kIsWeb) {
      // Saat dijalankan di browser, pakai host Laravel di mesin lokal
      return "http://127.0.0.1:8000/api";
    }

    // Untuk mobile dan desktop, gunakan localhost
    // Android emulator bisa pakai 10.0.2.2 jika perlu
    return "http://127.0.0.1:8000/api";
  }

  String? token;
  static const String _tokenKey = 'auth_token';

  // Initialize and load token from storage
  Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    token = prefs.getString(_tokenKey);
  }

  // Save token to storage
  Future<void> _saveToken(String newToken) async {
    token = newToken;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, newToken);
  }

  // Clear token from storage
  Future<void> clearToken() async {
    token = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
  }

  // Login user
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse("$baseUrl/login"),
        headers: {'Content-Type': 'application/json'},
        body: json.encode({
          "email": email,
          "password": password,
        }),
      );

      // Debug: print response untuk troubleshooting
      if (kDebugMode) {
        print('Login Response Status: ${response.statusCode}');
        print('Login Response Body: ${response.body}');
      }

      // Handle error response
      if (response.statusCode != 200) {
        try {
          final errorData = json.decode(response.body);
          return {
            'success': false,
            'message': errorData['message'] ?? 'Login gagal. Status: ${response.statusCode}',
          };
        } catch (_) {
          return {
            'success': false,
            'message': 'Login gagal. Status: ${response.statusCode}',
          };
        }
      }

      final data = json.decode(response.body);

      if (data['status'] == true) {
        await _saveToken(data['token']);
        return {
          'success': true,
          'message': data['message'] ?? 'Login berhasil',
          'user': data['user'],
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Login gagal. Periksa email atau password.',
        };
      }
    } catch (e) {
      if (kDebugMode) {
        print('Login Error: $e');
      }
      return {
        'success': false,
        'message': 'Terjadi kesalahan: ${e.toString()}. Pastikan server Laravel berjalan di http://127.0.0.1:8000',
      };
    }
  }

  // Logout user
  Future<bool> logout() async {
    try {
      if (token == null) {
        await clearToken();
        return true;
      }

      final response = await http.post(
        Uri.parse("$baseUrl/logout"),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      await clearToken();
      return response.statusCode == 200;
    } catch (e) {
      await clearToken();
      return false;
    }
  }

  // Ambil daftar lokasi
  Future<List<dynamic>> getLokasi() async {
    try {
      final response = await http.get(
        Uri.parse("$baseUrl/lokasi"),
        headers: {'Content-Type': 'application/json'},
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data is List ? data : [];
      } else {
        throw Exception('Gagal memuat daftar lokasi');
      }
    } catch (e) {
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  // Ambil daftar item berdasarkan lokasi
  Future<List<dynamic>> getItem({int? idLokasi}) async {
    try {
      String url = "$baseUrl/item";
      if (idLokasi != null) {
        url += "?id_lokasi=$idLokasi";
      }

      final response = await http.get(
        Uri.parse(url),
        headers: {'Content-Type': 'application/json'},
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data is List ? data : [];
      } else {
        throw Exception('Gagal memuat daftar item');
      }
    } catch (e) {
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  // Ajukan pengaduan
  Future<Map<String, dynamic>> ajukanPengaduan({
    required String namaPengaduan,
    required String deskripsi,
    required int idLokasi,
    int? idItem,
    XFile? fotoXFile,
    Uint8List? fotoBytes,
    String? fotoPath,
  }) async {
    try {
      if (token == null) {
        return {'success': false, 'message': 'Anda belum login'};
      }

      final uri = Uri.parse("$baseUrl/pengaduan");
      final request = http.MultipartRequest('POST', uri);

      request.headers['Authorization'] = 'Bearer $token';
      request.fields['nama_pengaduan'] = namaPengaduan;
      request.fields['deskripsi'] = deskripsi;
      request.fields['id_lokasi'] = idLokasi.toString();
      
      if (idItem != null) {
        request.fields['id_item'] = idItem.toString();
      }

      // Handle upload foto untuk web dan mobile
      if (fotoXFile != null) {
        if (kIsWeb && fotoBytes != null) {
          // Untuk web, gunakan bytes
          final filename = fotoXFile.name;
          request.files.add(
            http.MultipartFile.fromBytes(
              'foto',
              fotoBytes,
              filename: filename,
            ),
          );
        } else if (!kIsWeb && fotoPath != null) {
          // Untuk mobile, gunakan file path
          request.files.add(
            await http.MultipartFile.fromPath('foto', fotoPath),
          );
        }
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 201) {
        final data = json.decode(response.body);
        return {
          'success': true,
          'message': 'Pengaduan berhasil dikirim',
          'data': data,
        };
      } else {
        final errorData = json.decode(response.body);
        return {
          'success': false,
          'message': errorData['message'] ?? 'Gagal mengirim pengaduan',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.',
      };
    }
  }

  // Ambil riwayat pengaduan user
  Future<List<dynamic>> getRiwayat() async {
    if (token == null) {
      throw Exception('Anda belum login');
    }

    try {
      final response = await http.get(
        Uri.parse("$baseUrl/pengaduan"),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        // API returns array directly, not wrapped in 'data'
        return data is List ? data : [];
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat riwayat pengaduan');
      }
    } catch (e) {
      if (e.toString().contains('Session expired')) {
        rethrow;
      }
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  // ========== ADMIN METHODS ==========

  // Ambil semua pengaduan (untuk admin)
  Future<List<dynamic>> getAdminPengaduan({String? status, String? search}) async {
    if (token == null) {
      throw Exception('Anda belum login');
    }

    try {
      String url = "$baseUrl/admin/pengaduan";
      List<String> params = [];
      if (status != null && status.isNotEmpty) {
        params.add('status=$status');
      }
      if (search != null && search.isNotEmpty) {
        params.add('search=$search');
      }
      if (params.isNotEmpty) {
        url += '?' + params.join('&');
      }

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data is List ? data : [];
      } else if (response.statusCode == 403) {
        throw Exception('Akses ditolak. Hanya admin yang dapat mengakses.');
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat data pengaduan');
      }
    } catch (e) {
      if (e.toString().contains('Session expired') || e.toString().contains('Akses ditolak')) {
        rethrow;
      }
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  // Ambil detail pengaduan (untuk admin)
  Future<Map<String, dynamic>> getAdminPengaduanDetail(int id) async {
    if (token == null) {
      throw Exception('Anda belum login');
    }

    try {
      final response = await http.get(
        Uri.parse("$baseUrl/admin/pengaduan/$id"),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data;
      } else if (response.statusCode == 403) {
        throw Exception('Akses ditolak. Hanya admin yang dapat mengakses.');
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat detail pengaduan');
      }
    } catch (e) {
      if (e.toString().contains('Session expired') || e.toString().contains('Akses ditolak')) {
        rethrow;
      }
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  // Update status pengaduan (untuk admin)
  Future<Map<String, dynamic>> updatePengaduanStatus({
    required int id,
    required String status,
    String? saranPetugas,
  }) async {
    if (token == null) {
      return {'success': false, 'message': 'Anda belum login'};
    }

    try {
      final response = await http.put(
        Uri.parse("$baseUrl/admin/pengaduan/$id"),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode({
          'status': status,
          if (saranPetugas != null) 'saran_petugas': saranPetugas,
        }),
      );

      final data = json.decode(response.body);

      if (response.statusCode == 200 && data['status'] == true) {
        return {
          'success': true,
          'message': data['message'] ?? 'Status berhasil diperbarui',
          'data': data['data'],
        };
      } else if (response.statusCode == 403) {
        return {
          'success': false,
          'message': 'Akses ditolak. Hanya admin yang dapat mengakses.',
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal memperbarui status',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.',
      };
    }
  }
}
