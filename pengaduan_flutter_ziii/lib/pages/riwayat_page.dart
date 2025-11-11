import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import '../api/api_service.dart';

class RiwayatPage extends StatefulWidget {
  final ApiService api;
  const RiwayatPage({super.key, required this.api});

  @override
  State<RiwayatPage> createState() => _RiwayatPageState();
}

class _RiwayatPageState extends State<RiwayatPage> {
  List<dynamic> riwayat = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    loadRiwayat();
  }

  Future<void> loadRiwayat() async {
    try {
      final data = await widget.api.getRiwayat();
      setState(() {
        riwayat = data;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString().replaceAll('Exception: ', ''))),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Riwayat Pengaduan")),
      body: isLoading
          ? const Center(child: CircularProgressIndicator())
          : riwayat.isEmpty
              ? const Center(child: Text("Belum ada pengaduan"))
              : RefreshIndicator(
                  onRefresh: loadRiwayat,
                  child: ListView.builder(
                    itemCount: riwayat.length,
                    itemBuilder: (context, index) {
                      final item = riwayat[index];
                      String fotoUrl = '';
                      if (item['foto'] != null) {
                        // Handle foto URL - bisa berupa path relatif atau URL lengkap
                        if (item['foto'].toString().startsWith('http')) {
                          fotoUrl = item['foto'];
                        } else {
                          // Construct full URL from base URL
                          final baseUrl = kIsWeb 
                              ? 'http://127.0.0.1:8000'
                              : 'http://127.0.0.1:8000';
                          fotoUrl = '$baseUrl/storage/${item['foto']}';
                        }
                      }
                      
                      return Card(
                        margin: const EdgeInsets.all(8),
                        child: ExpansionTile(
                          title: Text(
                            item['nama_pengaduan'] ?? 'Tanpa nama',
                            style: const TextStyle(fontWeight: FontWeight.bold),
                          ),
                          subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const SizedBox(height: 4),
                              Text('Status: ${item['status'] ?? 'Tidak diketahui'}'),
                              if (item['lokasi'] != null) Text('Lokasi: ${item['lokasi']}'),
                              if (item['tgl_pengajuan'] != null)
                                Text('Tanggal: ${item['tgl_pengajuan']}'),
                            ],
                          ),
                          trailing: fotoUrl.isNotEmpty
                              ? ClipRRect(
                                  borderRadius: BorderRadius.circular(4),
                                  child: Image.network(
                                    fotoUrl,
                                    width: 60,
                                    height: 60,
                                    fit: BoxFit.cover,
                                    errorBuilder: (context, error, stackTrace) {
                                      return const Icon(Icons.image_not_supported, size: 40);
                                    },
                                  ),
                                )
                              : null,
                          children: [
                            Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  if (item['deskripsi'] != null) ...[
                                    const Text(
                                      'Deskripsi:',
                                      style: TextStyle(fontWeight: FontWeight.bold),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(item['deskripsi']),
                                    const SizedBox(height: 16),
                                  ],
                                  if (fotoUrl.isNotEmpty) ...[
                                    const Text(
                                      'Foto Pengaduan:',
                                      style: TextStyle(fontWeight: FontWeight.bold),
                                    ),
                                    const SizedBox(height: 8),
                                    GestureDetector(
                                      onTap: () {
                                        showDialog(
                                          context: context,
                                          builder: (context) => Dialog(
                                            child: Column(
                                              mainAxisSize: MainAxisSize.min,
                                              children: [
                                                AppBar(
                                                  title: const Text('Foto Pengaduan'),
                                                  leading: IconButton(
                                                    icon: const Icon(Icons.close),
                                                    onPressed: () => Navigator.pop(context),
                                                  ),
                                                ),
                                                Expanded(
                                                  child: InteractiveViewer(
                                                    child: Image.network(
                                                      fotoUrl,
                                                      fit: BoxFit.contain,
                                                      errorBuilder: (context, error, stackTrace) {
                                                        return const Center(
                                                          child: Icon(Icons.image_not_supported, size: 100),
                                                        );
                                                      },
                                                    ),
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                        );
                                      },
                                      child: ClipRRect(
                                        borderRadius: BorderRadius.circular(8),
                                        child: Image.network(
                                          fotoUrl,
                                          width: double.infinity,
                                          height: 200,
                                          fit: BoxFit.cover,
                                          errorBuilder: (context, error, stackTrace) {
                                            return Container(
                                              height: 200,
                                              color: Colors.grey[300],
                                              child: const Center(
                                                child: Icon(Icons.image_not_supported, size: 50),
                                              ),
                                            );
                                          },
                                        ),
                                      ),
                                    ),
                                  ],
                                  if (item['saran_petugas'] != null) ...[
                                    const SizedBox(height: 16),
                                    const Text(
                                      'Saran Petugas:',
                                      style: TextStyle(fontWeight: FontWeight.bold),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(item['saran_petugas']),
                                  ],
                                ],
                              ),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
