import 'dart:convert';
import 'dart:io';
import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:image_picker/image_picker.dart';

class ApiService {
  static String get baseUrl {
    if (kIsWeb) {
      // Saat dijalankan di browser, pakai host Laravel di mesin lokal
      return "http://127.0.0.1:8000/api";
    }

    if (Platform.isAndroid) {
      // Android emulator butuh 10.0.2.2 agar mengarah ke localhost laptop
      return "http://10.0.2.2:8000/api";
    }

    // Untuk Windows, iOS simulator, atau desktop lainnya bisa gunakan localhost
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
// Tambahkan method register di class ApiService
Future<Map<String, dynamic>> register({
  required String namaPengguna,
  required String username,
  required String email,
  required String password,
  required String confirmPassword,
}) async {
  try {
    final response = await http.post(
      Uri.parse("$baseUrl/register"),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({
        "nama_pengguna": namaPengguna,
        "username": username,
        "email": email,
        "password": password,
        "password_confirmation": confirmPassword,
      }),
    );

    // Debug: print response untuk troubleshooting
    if (kDebugMode) {
      print('Register Response Status: ${response.statusCode}');
      print('Register Response Body: ${response.body}');
    }

    // Handle error response
    if (response.statusCode != 200 && response.statusCode != 201) {
      try {
        final errorData = json.decode(response.body);
        return {
          'success': false,
          'message': errorData['message'] ?? 'Registrasi gagal. Status: ${response.statusCode}',
        };
      } catch (_) {
        return {
          'success': false,
          'message': 'Registrasi gagal. Status: ${response.statusCode}',
        };
      }
    }

    final data = json.decode(response.body);

    if (data['status'] == true) {
      // Auto login setelah registrasi berhasil
      await _saveToken(data['token']);
      return {
        'success': true,
        'message': data['message'] ?? 'Registrasi berhasil',
        'user': data['user'],
      };
    } else {
      return {
        'success': false,
        'message': data['message'] ?? 'Registrasi gagal',
      };
    }
  } catch (e) {
    if (kDebugMode) {
      print('Register Error: $e');
    }
    return {
      'success': false,
      'message': 'Terjadi kesalahan: ${e.toString()}',
    };
  }
}
// Tambahkan di class ApiService
Future<Map<String, dynamic>> updateProfile({
  required String namaPengguna,
  required String username,
  String? password,
}) async {
  try {
    if (token == null) {
      return {'success': false, 'message': 'Anda belum login'};
    }

    final response = await http.put(
      Uri.parse("$baseUrl/profile"),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'nama_pengguna': namaPengguna,
        'username': username,
        if (password != null && password.isNotEmpty) 'password': password,
      }),
    );

    if (kDebugMode) {
      print('Update Profile Response Status: ${response.statusCode}');
      print('Update Profile Response Body: ${response.body}');
    }

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      
      // Update data user di shared preferences
      if (data['status'] == true) {
        await _saveUserData(data['user']);
        return {
          'success': true,
          'message': data['message'] ?? 'Profil berhasil diperbarui',
          'user': data['user'],
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal memperbarui profil',
        };
      }
    } else {
      final errorData = json.decode(response.body);
      return {
        'success': false,
        'message': errorData['message'] ?? 'Gagal memperbarui profil',
      };
    }
  } catch (e) {
    if (kDebugMode) {
      print('Update Profile Error: $e');
    }
    return {
      'success': false,
      'message': 'Terjadi kesalahan: ${e.toString()}',
    };
  }
}
// Tambahkan di class ApiService
Future<Map<String, dynamic>?> getUserData() async {
  try {
    final prefs = await SharedPreferences.getInstance();
    final userData = prefs.getString('user_data');
    if (userData != null) {
      return json.decode(userData);
    }
    
    // Jika tidak ada data di shared preferences, coba ambil dari API
    if (token != null) {
      final response = await http.get(
        Uri.parse("$baseUrl/user"),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        await _saveUserData(data['data'] ?? data);
        return data['data'] ?? data;
      }
    }
    
    return null;
  } catch (e) {
    if (kDebugMode) {
      print('Error getting user data: $e');
    }
    return null;
  }
}

Future<void> _saveUserData(Map<String, dynamic> user) async {
  final prefs = await SharedPreferences.getInstance();
  await prefs.setString('user_data', json.encode(user));
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
      request.headers['Accept'] = 'application/json';
      
      // Pastikan semua field diisi dengan benar
      request.fields['nama_pengaduan'] = namaPengaduan.trim();
      request.fields['deskripsi'] = deskripsi.trim();
      request.fields['id_lokasi'] = idLokasi.toString();
      
      // Hanya kirim id_item jika tidak null dan tidak 0
      if (idItem != null && idItem > 0) {
        request.fields['id_item'] = idItem.toString();
      }

      if (kDebugMode) {
        print('=== UPLOAD PENGADUAN REQUEST ===');
        print('URL: $uri');
        print('Nama Pengaduan: $namaPengaduan');
        print('Deskripsi: $deskripsi');
        print('ID Lokasi: $idLokasi');
        print('ID Item: $idItem');
        print('Foto: ${fotoXFile != null ? "Ada" : "Tidak ada"}');
      }

      // Handle foto upload untuk web dan mobile
      if (fotoXFile != null) {
        if (kIsWeb) {
          // Untuk web, gunakan bytes
          if (fotoBytes != null) {
            final multipartFile = http.MultipartFile.fromBytes(
              'foto',
              fotoBytes,
              filename: fotoXFile.name,
            );
            request.files.add(multipartFile);
          }
        } else {
          // Untuk mobile, gunakan path
          if (fotoPath != null) {
            request.files.add(await http.MultipartFile.fromPath('foto', fotoPath));
          } else if (fotoXFile.path.isNotEmpty) {
            request.files.add(await http.MultipartFile.fromPath('foto', fotoXFile.path));
          }
        }
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      if (kDebugMode) {
        print('Response Status: ${response.statusCode}');
        print('Response Body: ${response.body}');
      }

      if (response.statusCode == 201) {
        final data = json.decode(response.body);
        return {
          'success': true,
          'message': 'Pengaduan berhasil dikirim',
          'data': data,
        };
      } else {
        // Handle error response
        String errorMessage = 'Gagal mengirim pengaduan';
        try {
          final errorData = json.decode(response.body);
          
          if (kDebugMode) {
            print('Error Data: $errorData');
          }
          
          if (errorData['errors'] != null) {
            // Handle validation errors - tampilkan semua error
            final errors = errorData['errors'] as Map<String, dynamic>;
            final errorMessages = <String>[];
            
            errors.forEach((key, value) {
              if (value is List && value.isNotEmpty) {
                errorMessages.add('${key}: ${value[0]}');
              } else if (value is String) {
                errorMessages.add('${key}: $value');
              }
            });
            
            if (errorMessages.isNotEmpty) {
              errorMessage = errorMessages.join('\n');
            } else {
              errorMessage = 'Validasi gagal. Periksa data yang diinput.';
            }
          } else if (errorData['message'] != null) {
            errorMessage = errorData['message'];
          }
        } catch (e) {
          if (kDebugMode) {
            print('Error parsing response: $e');
            print('Response body: ${response.body}');
          }
          errorMessage = 'Gagal mengirim pengaduan. Status: ${response.statusCode}';
        }
        
        if (kDebugMode) {
          print('Final error message: $errorMessage');
        }
        
        return {
          'success': false,
          'message': errorMessage,
        };
      }
    } catch (e) {
      if (kDebugMode) {
        print('Error ajukan pengaduan: $e');
        print('Error type: ${e.runtimeType}');
      }
      
      String errorMessage = 'Terjadi kesalahan. Periksa koneksi internet Anda.';
      
      // Handle specific error types
      if (e.toString().contains('SocketException') || 
          e.toString().contains('Failed host lookup') ||
          e.toString().contains('Connection refused')) {
        errorMessage = 'Tidak dapat terhubung ke server. Pastikan server Laravel berjalan di http://127.0.0.1:8000';
      } else if (e.toString().contains('TimeoutException')) {
        errorMessage = 'Waktu koneksi habis. Periksa koneksi internet Anda.';
      } else if (e.toString().contains('FormatException')) {
        errorMessage = 'Format data tidak valid. Silakan coba lagi.';
      }
      
      return {
        'success': false,
        'message': errorMessage,
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

  // Ambil daftar pengaduan untuk admin
  Future<List<dynamic>> getAdminPengaduan({String? status, String? search}) async {
    if (token == null) {
      throw Exception('Anda belum login');
    }

    try {
      String url = "$baseUrl/admin/pengaduan";
      final queryParams = <String, String>{};
      
      if (status != null && status.isNotEmpty) {
        queryParams['status'] = status;
      }
      
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      if (queryParams.isNotEmpty) {
        url += "?${Uri(queryParameters: queryParams).query}";
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
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat daftar pengaduan');
      }
    } catch (e) {
      if (e.toString().contains('Session expired')) {
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
          if (saranPetugas != null && saranPetugas.isNotEmpty)
            'saran_petugas': saranPetugas,
        }),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return {
          'success': true,
          'message': data['message'] ?? 'Status berhasil diperbarui',
          'data': data['data'],
        };
      } else {
        final errorData = json.decode(response.body);
        return {
          'success': false,
          'message': errorData['message'] ?? 'Gagal memperbarui status',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.',
      };
    }
  }

  // Ambil detail pengaduan admin
  Future<Map<String, dynamic>> getAdminPengaduanDetail(int id) async {
    if (token == null) throw Exception('Anda belum login');
    try {
      final response = await http.get(
        Uri.parse("$baseUrl/admin/pengaduan/$id"),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      if (response.statusCode == 200) {
        return json.decode(response.body) as Map<String, dynamic>;
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat detail pengaduan');
      }
    } catch (e) {
      if (e.toString().contains('Session expired')) rethrow;
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  // Hapus pengaduan (admin)
  Future<Map<String, dynamic>> deletePengaduanAdmin(int id) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.delete(
        Uri.parse("$baseUrl/admin/pengaduan/$id"),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return {'success': true, 'message': data['message'] ?? 'Berhasil dihapus'};
      } else {
        final data = json.decode(response.body);
        return {'success': false, 'message': data['message'] ?? 'Gagal menghapus'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  // ---------- MANAGE USERS (admin) ----------
  Future<List<dynamic>> getAdminUsers() async {
    if (token == null) throw Exception('Anda belum login');
    try {
      final response = await http.get(
        Uri.parse("$baseUrl/admin/users"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data is List ? data : [];
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat daftar user');
      }
    } catch (e) {
      if (e.toString().contains('Session expired')) rethrow;
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  Future<Map<String, dynamic>> createUser(Map<String, dynamic> payload) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.post(
        Uri.parse("$baseUrl/admin/users"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: json.encode(payload),
      );
      final data = json.decode(response.body);
      return response.statusCode == 201 ? {'success': true, 'message': data['message'] ?? 'User dibuat', 'data': data} : {'success': false, 'message': data['message'] ?? 'Gagal membuat user'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> updateUser(int id, Map<String, dynamic> payload) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.put(
        Uri.parse("$baseUrl/admin/users/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: json.encode(payload),
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'User diperbarui'} : {'success': false, 'message': data['message'] ?? 'Gagal memperbarui user'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> deleteUser(int id) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.delete(
        Uri.parse("$baseUrl/admin/users/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'User dihapus'} : {'success': false, 'message': data['message'] ?? 'Gagal menghapus user'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  // ---------- MANAGE ITEMS (admin) ----------
  Future<List<dynamic>> getAdminItems() async {
    if (token == null) throw Exception('Anda belum login');
    try {
      final response = await http.get(
        Uri.parse("$baseUrl/admin/items"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data is List ? data : [];
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat daftar items');
      }
    } catch (e) {
      if (e.toString().contains('Session expired')) rethrow;
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  Future<Map<String, dynamic>> createItem({
    required String namaItem,
    String? deskripsi,
    XFile? fotoXFile,
    Uint8List? fotoBytes,
    String? fotoPath,
  }) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final uri = Uri.parse("$baseUrl/admin/items");
      final request = http.MultipartRequest('POST', uri);
      request.headers['Authorization'] = 'Bearer $token';
      request.headers['Accept'] = 'application/json';
      request.fields['nama_item'] = namaItem;
      if (deskripsi != null) request.fields['deskripsi'] = deskripsi;
      if (fotoXFile != null) {
        if (kIsWeb) {
          if (fotoBytes != null) {
            request.files.add(http.MultipartFile.fromBytes('foto', fotoBytes, filename: fotoXFile.name));
          }
        } else {
          if (fotoPath != null) {
            request.files.add(await http.MultipartFile.fromPath('foto', fotoPath));
          } else if (fotoXFile.path.isNotEmpty) {
            request.files.add(await http.MultipartFile.fromPath('foto', fotoXFile.path));
          }
        }
      }
      final streamed = await request.send();
      final response = await http.Response.fromStream(streamed);
      final data = json.decode(response.body);
      if (response.statusCode == 201) return {'success': true, 'message': data['message'] ?? 'Item dibuat', 'data': data};
      return {'success': false, 'message': data['message'] ?? 'Gagal membuat item'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> updateItem(int id, Map<String, dynamic> payload) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.put(
        Uri.parse("$baseUrl/admin/items/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: json.encode(payload),
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'Item diperbarui'} : {'success': false, 'message': data['message'] ?? 'Gagal memperbarui item'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> deleteItem(int id) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.delete(
        Uri.parse("$baseUrl/admin/items/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'Item dihapus'} : {'success': false, 'message': data['message'] ?? 'Gagal menghapus item'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  // ---------- MANAGE LOKASI (admin) ----------
  Future<List<dynamic>> getAdminLokasi() async {
    if (token == null) throw Exception('Anda belum login');
    try {
      final response = await http.get(
        Uri.parse("$baseUrl/admin/lokasi"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data is List ? data : [];
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat daftar lokasi');
      }
    } catch (e) {
      if (e.toString().contains('Session expired')) rethrow;
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  Future<Map<String, dynamic>> createLokasi(String namaLokasi) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.post(
        Uri.parse("$baseUrl/admin/lokasi"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: json.encode({'nama_lokasi': namaLokasi}),
      );
      final data = json.decode(response.body);
      return response.statusCode == 201 ? {'success': true, 'message': data['message'] ?? 'Lokasi dibuat'} : {'success': false, 'message': data['message'] ?? 'Gagal membuat lokasi'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> updateLokasi(int id, String namaLokasi) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.put(
        Uri.parse("$baseUrl/admin/lokasi/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: json.encode({'nama_lokasi': namaLokasi}),
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'Lokasi diperbarui'} : {'success': false, 'message': data['message'] ?? 'Gagal memperbarui lokasi'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> deleteLokasi(int id) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.delete(
        Uri.parse("$baseUrl/admin/lokasi/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'Lokasi dihapus'} : {'success': false, 'message': data['message'] ?? 'Gagal menghapus lokasi'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  // ---------- MANAGE PETUGAS (admin) ----------
  Future<List<dynamic>> getAdminPetugas() async {
    if (token == null) throw Exception('Anda belum login');
    try {
      final response = await http.get(
        Uri.parse("$baseUrl/admin/petugas"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data is List ? data : [];
      } else if (response.statusCode == 401) {
        await clearToken();
        throw Exception('Session expired. Silakan login kembali.');
      } else {
        throw Exception('Gagal memuat daftar petugas');
      }
    } catch (e) {
      if (e.toString().contains('Session expired')) rethrow;
      throw Exception('Terjadi kesalahan. Periksa koneksi internet Anda.');
    }
  }

  Future<Map<String, dynamic>> createPetugas(Map<String, dynamic> payload) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.post(
        Uri.parse("$baseUrl/admin/petugas"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: json.encode(payload),
      );
      final data = json.decode(response.body);
      return response.statusCode == 201 ? {'success': true, 'message': data['message'] ?? 'Petugas dibuat'} : {'success': false, 'message': data['message'] ?? 'Gagal membuat petugas'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> updatePetugas(int id, Map<String, dynamic> payload) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.put(
        Uri.parse("$baseUrl/admin/petugas/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
        body: json.encode(payload),
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'Petugas diperbarui'} : {'success': false, 'message': data['message'] ?? 'Gagal memperbarui petugas'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  Future<Map<String, dynamic>> deletePetugas(int id) async {
    if (token == null) return {'success': false, 'message': 'Anda belum login'};
    try {
      final response = await http.delete(
        Uri.parse("$baseUrl/admin/petugas/$id"),
        headers: {'Authorization': 'Bearer $token', 'Content-Type': 'application/json'},
      );
      final data = json.decode(response.body);
      return response.statusCode == 200 ? {'success': true, 'message': data['message'] ?? 'Petugas dihapus'} : {'success': false, 'message': data['message'] ?? 'Gagal menghapus petugas'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan. Periksa koneksi internet Anda.'};
    }
  }

  // Helper method untuk mendapatkan URL foto
  static String getFotoUrl(String? fotoPath) {
    if (fotoPath == null || fotoPath.isEmpty) return '';
    
    // Jika sudah URL lengkap, return langsung
    if (fotoPath.startsWith('http')) {
      return fotoPath;
    }
    
    // Construct full URL - gunakan baseUrl yang sama dengan API
    // Untuk Android emulator, gunakan 10.0.2.2
    String storageBaseUrl;
    if (kIsWeb) {
      storageBaseUrl = 'http://127.0.0.1:8000';
    } else if (Platform.isAndroid) {
      storageBaseUrl = 'http://10.0.2.2:8000';
    } else {
      storageBaseUrl = 'http://127.0.0.1:8000';
    }
    
    return '$storageBaseUrl/storage/$fotoPath';
  }
}
