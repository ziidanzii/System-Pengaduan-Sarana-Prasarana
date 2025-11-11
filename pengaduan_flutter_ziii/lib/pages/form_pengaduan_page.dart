import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../api/api_service.dart';
import 'riwayat_page.dart';
import 'login_page.dart';

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
  Uint8List? fotoBytes; // Untuk web
  // Untuk mobile, kita akan pass path langsung ke API
  String? fotoPath; // Path untuk mobile
  bool isLoading = false;
  bool isLoadingData = true;

  // Data untuk dropdown
  List<dynamic> listLokasi = [];
  List<dynamic> listItem = [];
  int? selectedLokasiId;
  int? selectedItemId;

  @override
  void initState() {
    super.initState();
    loadLokasi();
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
          SnackBar(content: Text('Gagal memuat lokasi: ${e.toString()}')),
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
        selectedItemId = null; // Reset item saat lokasi berubah
        isLoadingData = false;
      });
    } catch (e) {
      setState(() => isLoadingData = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memuat item: ${e.toString()}')),
        );
      }
    }
  }

  void onLokasiChanged(int? idLokasi) {
    setState(() {
      selectedLokasiId = idLokasi;
      selectedItemId = null; // Reset item saat lokasi berubah
      listItem = []; // Clear item list
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
        // Baca sebagai bytes untuk semua platform (web dan mobile)
        picked.readAsBytes().then((bytes) {
          if (mounted) {
            setState(() {
              fotoBytes = bytes;
              if (!kIsWeb) {
                // Simpan path untuk mobile (untuk upload)
                fotoPath = picked.path;
              }
            });
          }
        });
      });
    }
  }

  Future<void> kirimAduan() async {
    // Validasi form
    if (namaController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Nama pengaduan harus diisi")),
      );
      return;
    }
    if (deskripsiController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Deskripsi harus diisi")),
      );
      return;
    }
    if (selectedLokasiId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Lokasi harus dipilih")),
      );
      return;
    }

    setState(() => isLoading = true);
    final result = await widget.api.ajukanPengaduan(
      namaPengaduan: namaController.text,
      deskripsi: deskripsiController.text,
      idLokasi: selectedLokasiId!,
      idItem: selectedItemId, // Optional
      fotoXFile: fotoXFile,
      fotoBytes: fotoBytes,
      fotoPath: fotoPath,
    );
    setState(() => isLoading = false);

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Pengaduan berhasil dikirim')),
      );
      // Clear form setelah berhasil
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
        SnackBar(content: Text(result['message'] ?? 'Gagal mengirim pengaduan')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Ajukan Pengaduan"),
        actions: [
          IconButton(
            icon: const Icon(Icons.history),
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => RiwayatPage(api: widget.api)),
            ),
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () async {
              final confirm = await showDialog<bool>(
                context: context,
                builder: (context) => AlertDialog(
                  title: const Text('Logout'),
                  content: const Text('Apakah Anda yakin ingin logout?'),
                  actions: [
                    TextButton(
                      onPressed: () => Navigator.pop(context, false),
                      child: const Text('Batal'),
                    ),
                    TextButton(
                      onPressed: () => Navigator.pop(context, true),
                      child: const Text('Logout'),
                    ),
                  ],
                ),
              );
              
              if (confirm == true) {
                await widget.api.logout();
                if (mounted) {
                  Navigator.pushReplacement(
                    context,
                    MaterialPageRoute(builder: (_) => const LoginPage()),
                  );
                }
              }
            },
          ),
        ],
      ),
      body: isLoadingData
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  TextField(
                    controller: namaController,
                    decoration: const InputDecoration(labelText: "Nama Pengaduan"),
                  ),
                  const SizedBox(height: 16),
                  TextField(
                    controller: deskripsiController,
                    decoration: const InputDecoration(labelText: "Deskripsi"),
                    maxLines: 4,
                  ),
                  const SizedBox(height: 16),
                  // Dropdown Lokasi
                  DropdownButtonFormField<int>(
                    value: selectedLokasiId,
                    decoration: const InputDecoration(
                      labelText: "Pilih Lokasi",
                      border: OutlineInputBorder(),
                    ),
                    items: listLokasi.map<DropdownMenuItem<int>>((lokasi) {
                      return DropdownMenuItem<int>(
                        value: lokasi['id_lokasi'],
                        child: Text(lokasi['nama_lokasi'] ?? ''),
                      );
                    }).toList(),
                    onChanged: onLokasiChanged,
                  ),
                  const SizedBox(height: 16),
                  // Dropdown Item (hanya muncul jika lokasi sudah dipilih)
                  if (selectedLokasiId != null)
                    DropdownButtonFormField<int>(
                      value: selectedItemId,
                      decoration: const InputDecoration(
                        labelText: "Pilih Item (Opsional)",
                        border: OutlineInputBorder(),
                      ),
                      items: [
                        const DropdownMenuItem<int>(
                          value: null,
                          child: Text("Tidak memilih item"),
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
                    ),
                  const SizedBox(height: 16),
                  // Foto
                  (fotoXFile != null)
                      ? Column(
                          children: [
                            // Tampilkan gambar menggunakan Image.memory untuk semua platform
                            fotoBytes != null
                                ? Image.memory(
                                    fotoBytes!,
                                    height: 150,
                                    fit: BoxFit.cover,
                                  )
                                : const SizedBox(height: 150, child: Center(child: CircularProgressIndicator())),
                            const SizedBox(height: 8),
                            ElevatedButton(
                              onPressed: () => setState(() {
                                fotoXFile = null;
                                fotoBytes = null;
                                fotoPath = null;
                              }),
                              style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                              child: const Text("Hapus Foto"),
                            ),
                          ],
                        )
                      : const Text("Foto (opsional)"),
                  ElevatedButton(
                    onPressed: pilihFoto,
                    child: const Text("Pilih Foto"),
                  ),
                  const SizedBox(height: 20),
                  isLoading
                      ? const CircularProgressIndicator()
                      : ElevatedButton(
                          onPressed: kirimAduan,
                          child: const Text("Kirim Pengaduan"),
                        ),
                ],
              ),
            ),
    );
  }
}
