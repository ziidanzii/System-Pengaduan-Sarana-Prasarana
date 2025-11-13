import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../api/api_service.dart';
import 'riwayat_page.dart';
import 'login_page.dart';
import 'profile_page.dart';

class FormPengaduanPage extends StatefulWidget {
  final ApiService api;
  const FormPengaduanPage({super.key, required this.api});

  @override
  State<FormPengaduanPage> createState() => _FormPengaduanPageState();
}

class _FormPengaduanPageState extends State<FormPengaduanPage> {
  final namaController = TextEditingController();
  final deskripsiController = TextEditingController();
  XFile? fotoXFile;
  Uint8List? fotoBytes;
  String? fotoPath;
  bool isLoading = false;
  bool isLoadingData = true;
  Map<String, dynamic>? userData;

  // Data untuk dropdown
  List<dynamic> listLokasi = [];
  List<dynamic> listItem = [];
  int? selectedLokasiId;
  int? selectedItemId;

  @override
  void initState() {
    super.initState();
    _loadUserData();
    loadLokasi();
  }

  Future<void> _loadUserData() async {
    try {
      final data = await widget.api.getUserData();
      setState(() {
        userData = data;
      });
    } catch (e) {
      // Fallback data
      setState(() {
        userData = {
          'nama_pengguna': 'User',
          'username': 'user',
          'email': 'user@example.com',
          'role': 'user'
        };
      });
    }
  }

  Future<void> loadLokasi() async {
    try {
      setState(() => isLoadingData = true);
      final data = await widget.api.getLokasi();
      setState(() {
        listLokasi = data;
        isLoadingData = false;
      });
    } catch (e) {
      setState(() => isLoadingData = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal memuat lokasi: ${e.toString()}'),
            backgroundColor: Color(0xFFF44336),
          ),
        );
      }
    }
  }

  Future<void> loadItem(int idLokasi) async {
    try {
      setState(() => isLoadingData = true);
      final data = await widget.api.getItem(idLokasi: idLokasi);
      setState(() {
        listItem = data;
        selectedItemId = null;
        isLoadingData = false;
      });
    } catch (e) {
      setState(() => isLoadingData = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal memuat item: ${e.toString()}'),
            backgroundColor: Color(0xFFF44336),
          ),
        );
      }
    }
  }

  void onLokasiChanged(int? idLokasi) {
    setState(() {
      selectedLokasiId = idLokasi;
      selectedItemId = null;
      listItem = [];
    });
    if (idLokasi != null) {
      loadItem(idLokasi);
    }
  }

  Future<void> pilihFoto() async {
    final picked = await ImagePicker().pickImage(source: ImageSource.gallery);
    if (picked != null) {
      setState(() {
        fotoXFile = picked;
        picked.readAsBytes().then((bytes) {
          if (mounted) {
            setState(() {
              fotoBytes = bytes;
              if (!kIsWeb) {
                fotoPath = picked.path;
              }
            });
          }
        });
      });
    }
  }

  Future<void> kirimAduan() async {
    if (namaController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Nama pengaduan harus diisi"),
          backgroundColor: Color(0xFFFF5722),
        ),
      );
      return;
    }
    if (deskripsiController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Deskripsi harus diisi"),
          backgroundColor: Color(0xFFFF5722),
        ),
      );
      return;
    }
    if (selectedLokasiId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Lokasi harus dipilih"),
          backgroundColor: Color(0xFFFF5722),
        ),
      );
      return;
    }

    setState(() => isLoading = true);
    final result = await widget.api.ajukanPengaduan(
      namaPengaduan: namaController.text,
      deskripsi: deskripsiController.text,
      idLokasi: selectedLokasiId!,
      idItem: selectedItemId,
      fotoXFile: fotoXFile,
      fotoBytes: fotoBytes,
      fotoPath: fotoPath,
    );
    setState(() => isLoading = false);

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? 'Pengaduan berhasil dikirim'),
          backgroundColor: Color(0xFF4CAF50),
        ),
      );
      namaController.clear();
      deskripsiController.clear();
      setState(() {
        selectedLokasiId = null;
        selectedItemId = null;
        listItem = [];
        fotoXFile = null;
        fotoBytes = null;
        fotoPath = null;
      });
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? 'Gagal mengirim pengaduan'),
          backgroundColor: Color(0xFFF44336),
        ),
      );
    }
  }

  void _showLogoutDialog() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Logout'),
        content: Text('Apakah Anda yakin ingin logout?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: Text(
              'Logout',
              style: TextStyle(color: Color(0xFFE65100)),
            ),
          ),
        ],
      ),
    );
    
    if (confirm == true) {
      await widget.api.logout();
      if (mounted) {
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => const LoginPage()),
          (route) => false,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          "Ajukan Pengaduan",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w600,
          ),
        ),
        backgroundColor: Color(0xFFE65100),
        elevation: 0,
        iconTheme: IconThemeData(color: Colors.white),
      ),
      body: Column(
        children: [
          // Custom Navigation Bar
          Container(
            width: double.infinity,
            padding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withOpacity(0.1),
                  blurRadius: 10,
                  offset: Offset(0, 2),
                ),
              ],
              border: Border(
                bottom: BorderSide(
                  color: Colors.grey.shade200,
                  width: 1,
                ),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                // Tombol Riwayat
                _buildNavButton(
                  icon: Icons.history,
                  label: 'Riwayat',
                  onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => RiwayatPage(api: widget.api)),
                  ),
                ),
                
                // Tombol Profil
                _buildNavButton(
                  icon: Icons.person,
                  label: 'Profil',
                  onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => ProfilePage(api: widget.api)),
                  ),
                ),
                
                // Tombol Logout
                _buildNavButton(
                  icon: Icons.logout,
                  label: 'Logout',
                  onTap: _showLogoutDialog,
                  isLogout: true,
                ),
              ],
            ),
          ),

          // Content
          Expanded(
            child: isLoadingData
                ? const Center(
                    child: CircularProgressIndicator(
                      valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFE65100)),
                    ),
                  )
                : SingleChildScrollView(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Header Section
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              colors: [Color(0xFFFF7043), Color(0xFFE65100)],
                              begin: Alignment.centerLeft,
                              end: Alignment.centerRight,
                            ),
                            borderRadius: BorderRadius.circular(16),
                            boxShadow: [
                              BoxShadow(
                                color: Color(0xFFFF7043).withOpacity(0.3),
                                blurRadius: 10,
                                offset: Offset(0, 5),
                              ),
                            ],
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                "Selamat Datang,",
                                style: TextStyle(
                                  fontSize: 18,
                                  color: Colors.white,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                userData?['nama_pengguna'] ?? "User",
                                style: TextStyle(
                                  fontSize: 24,
                                  color: Colors.white,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                "Ajukan pengaduan sarana prasarana dengan mengisi form di bawah",
                                style: TextStyle(
                                  fontSize: 14,
                                  color: Colors.white.withOpacity(0.9),
                                ),
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: 32),

                        // Form Container
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(24),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.grey.withOpacity(0.1),
                                blurRadius: 15,
                                offset: Offset(0, 5),
                              ),
                            ],
                            border: Border.all(
                              color: Colors.grey.shade200,
                              width: 1,
                            ),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                "Form Pengaduan",
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w600,
                                  color: Color(0xFFE65100),
                                ),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                "Isi form berikut untuk mengajukan pengaduan",
                                style: TextStyle(
                                  fontSize: 14,
                                  color: Colors.grey[600],
                                ),
                              ),
                              const SizedBox(height: 24),

                              // Form fields tetap sama...
                              Container(
                                decoration: BoxDecoration(
                                  color: Colors.grey[50],
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(
                                    color: Colors.grey.shade300,
                                    width: 1,
                                  ),
                                ),
                                child: TextField(
                                  controller: namaController,
                                  style: TextStyle(
                                    color: Colors.grey[800],
                                    fontSize: 16,
                                  ),
                                  decoration: InputDecoration(
                                    labelText: "Nama Pengaduan",
                                    labelStyle: TextStyle(color: Colors.grey[600]),
                                    prefixIcon: Container(
                                      margin: EdgeInsets.only(left: 8, right: 8),
                                      child: Icon(Icons.title, color: Color(0xFFFF7043)),
                                    ),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                                    hintText: "Masukkan nama pengaduan",
                                    hintStyle: TextStyle(color: Colors.grey[400]),
                                  ),
                                ),
                              ),

                              const SizedBox(height: 20),

                              // ... (sisa form fields tetap sama)
                              Container(
                                decoration: BoxDecoration(
                                  color: Colors.grey[50],
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(
                                    color: Colors.grey.shade300,
                                    width: 1,
                                  ),
                                ),
                                child: TextField(
                                  controller: deskripsiController,
                                  style: TextStyle(
                                    color: Colors.grey[800],
                                    fontSize: 16,
                                  ),
                                  maxLines: 4,
                                  decoration: InputDecoration(
                                    labelText: "Deskripsi Pengaduan",
                                    labelStyle: TextStyle(color: Colors.grey[600]),
                                    alignLabelWithHint: true,
                                    prefixIcon: Container(
                                      margin: EdgeInsets.only(left: 8, right: 8, bottom: 40),
                                      child: Icon(Icons.description, color: Color(0xFFFF7043)),
                                    ),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                                    hintText: "Jelaskan detail pengaduan Anda...",
                                    hintStyle: TextStyle(color: Colors.grey[400]),
                                  ),
                                ),
                              ),

                              const SizedBox(height: 20),

                              Container(
                                decoration: BoxDecoration(
                                  color: Colors.grey[50],
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(
                                    color: Colors.grey.shade300,
                                    width: 1,
                                  ),
                                ),
                                child: DropdownButtonFormField<int>(
                                  value: selectedLokasiId,
                                  decoration: InputDecoration(
                                    labelText: "Pilih Lokasi",
                                    labelStyle: TextStyle(color: Colors.grey[600]),
                                    prefixIcon: Container(
                                      margin: EdgeInsets.only(left: 8, right: 8),
                                      child: Icon(Icons.location_on, color: Color(0xFFFF7043)),
                                    ),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.symmetric(horizontal: 12),
                                  ),
                                  items: listLokasi.map<DropdownMenuItem<int>>((lokasi) {
                                    return DropdownMenuItem<int>(
                                      value: lokasi['id_lokasi'],
                                      child: Text(lokasi['nama_lokasi'] ?? ''),
                                    );
                                  }).toList(),
                                  onChanged: onLokasiChanged,
                                  style: TextStyle(color: Colors.grey[800], fontSize: 16),
                                ),
                              ),

                              const SizedBox(height: 20),

                              if (selectedLokasiId != null)
                                Container(
                                  decoration: BoxDecoration(
                                    color: Colors.grey[50],
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(
                                      color: Colors.grey.shade300,
                                      width: 1,
                                    ),
                                  ),
                                  child: DropdownButtonFormField<int>(
                                    value: selectedItemId,
                                    decoration: InputDecoration(
                                      labelText: "Pilih Item (Opsional)",
                                      labelStyle: TextStyle(color: Colors.grey[600]),
                                      prefixIcon: Container(
                                        margin: EdgeInsets.only(left: 8, right: 8),
                                        child: Icon(Icons.category, color: Color(0xFFFF7043)),
                                      ),
                                      border: InputBorder.none,
                                      contentPadding: EdgeInsets.symmetric(horizontal: 12),
                                    ),
                                    items: [
                                      DropdownMenuItem<int>(
                                        value: null,
                                        child: Text(
                                          "Tidak memilih item",
                                          style: TextStyle(color: Colors.grey[600]),
                                        ),
                                      ),
                                      ...listItem.map<DropdownMenuItem<int>>((item) {
                                        return DropdownMenuItem<int>(
                                          value: item['id_item'],
                                          child: Text(item['nama_item'] ?? ''),
                                        );
                                      }),
                                    ],
                                    onChanged: (value) {
                                      setState(() => selectedItemId = value);
                                    },
                                    style: TextStyle(color: Colors.grey[800], fontSize: 16),
                                  ),
                                ),

                              if (selectedLokasiId != null) const SizedBox(height: 20),

                              // Foto Section dan Submit Button tetap sama...
                              Container(
                                width: double.infinity,
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: Colors.grey[50],
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(
                                    color: Colors.grey.shade300,
                                    width: 1,
                                  ),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Icon(Icons.photo_camera, color: Color(0xFFFF7043)),
                                        const SizedBox(width: 8),
                                        Text(
                                          "Foto Bukti (Opsional)",
                                          style: TextStyle(
                                            fontSize: 16,
                                            color: Colors.grey[700],
                                            fontWeight: FontWeight.w500,
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 12),
                                    
                                    if (fotoXFile != null) ...[
                                      Container(
                                        width: double.infinity,
                                        height: 150,
                                        decoration: BoxDecoration(
                                          borderRadius: BorderRadius.circular(8),
                                          border: Border.all(color: Colors.grey.shade300),
                                        ),
                                        child: fotoBytes != null
                                            ? ClipRRect(
                                                borderRadius: BorderRadius.circular(8),
                                                child: Image.memory(
                                                  fotoBytes!,
                                                  fit: BoxFit.cover,
                                                ),
                                              )
                                            : Center(
                                                child: CircularProgressIndicator(
                                                  valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFE65100)),
                                                ),
                                              ),
                                      ),
                                      const SizedBox(height: 12),
                                      Container(
                                        width: double.infinity,
                                        child: ElevatedButton(
                                          onPressed: () => setState(() {
                                            fotoXFile = null;
                                            fotoBytes = null;
                                            fotoPath = null;
                                          }),
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor: Colors.red,
                                            padding: EdgeInsets.symmetric(vertical: 12),
                                          ),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.center,
                                            children: [
                                              Icon(Icons.delete, color: Colors.white),
                                              SizedBox(width: 8),
                                              Text(
                                                "Hapus Foto",
                                                style: TextStyle(color: Colors.white),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ),
                                    ] else
                                      Container(
                                        width: double.infinity,
                                        child: ElevatedButton(
                                          onPressed: pilihFoto,
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor: Colors.white,
                                            padding: EdgeInsets.symmetric(vertical: 12),
                                            side: BorderSide(color: Color(0xFFFF7043)),
                                          ),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.center,
                                            children: [
                                              Icon(Icons.add_photo_alternate, color: Color(0xFFFF7043)),
                                              SizedBox(width: 8),
                                              Text(
                                                "Pilih Foto",
                                                style: TextStyle(color: Color(0xFFFF7043)),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ),
                                  ],
                                ),
                              ),

                              const SizedBox(height: 32),

                              Container(
                                width: double.infinity,
                                height: 56,
                                decoration: BoxDecoration(
                                  gradient: LinearGradient(
                                    colors: [Color(0xFFFF7043), Color(0xFFE65100)],
                                    begin: Alignment.centerLeft,
                                    end: Alignment.centerRight,
                                  ),
                                  borderRadius: BorderRadius.circular(12),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Color(0xFFFF7043).withOpacity(0.4),
                                      blurRadius: 10,
                                      offset: Offset(0, 5),
                                    ),
                                  ],
                                ),
                                child: isLoading
                                    ? Center(
                                        child: SizedBox(
                                          width: 20,
                                          height: 20,
                                          child: CircularProgressIndicator(
                                            strokeWidth: 2,
                                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                          ),
                                        ),
                                      )
                                    : TextButton(
                                        onPressed: kirimAduan,
                                        style: TextButton.styleFrom(
                                          shape: RoundedRectangleBorder(
                                            borderRadius: BorderRadius.circular(12),
                                          ),
                                        ),
                                        child: Row(
                                          mainAxisAlignment: MainAxisAlignment.center,
                                          children: [
                                            Text(
                                              "Kirim Pengaduan",
                                              style: TextStyle(
                                                color: Colors.white,
                                                fontSize: 16,
                                                fontWeight: FontWeight.w600,
                                              ),
                                            ),
                                            SizedBox(width: 8),
                                            Icon(Icons.send, color: Colors.white, size: 20),
                                          ],
                                        ),
                                      ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 20),
                      ],
                    ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildNavButton({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
    bool isLogout = false,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: isLogout ? Colors.red.shade200 : Colors.grey.shade300,
            width: 1,
          ),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              size: 20,
              color: isLogout ? Colors.red : Color(0xFFE65100),
            ),
            SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w500,
                color: isLogout ? Colors.red : Color(0xFFE65100),
              ),
            ),
          ],
        ),
      ),
    );
  }
}