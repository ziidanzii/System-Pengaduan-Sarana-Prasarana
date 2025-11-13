// filepath: d:\UKK Pengaduan sarpras\projeck ukk\pengaduan_flutter_ziii\lib\pages\admin\lokasi.dart
import 'package:flutter/material.dart';
import '../../api/api_service.dart';

class LokasiAdminPage extends StatefulWidget {
  final ApiService api;
  const LokasiAdminPage({super.key, required this.api});

  @override
  State<LokasiAdminPage> createState() => _LokasiAdminPageState();
}

class _LokasiAdminPageState extends State<LokasiAdminPage> {
  List<dynamic> lokasi = [];
  bool loading = true;
  final searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    loadLokasi();
  }

  Future<void> loadLokasi() async {
    setState(() => loading = true);
    try {
      final data = await widget.api.getLokasi();
      setState(() { lokasi = data; loading = false; });
    } catch (e) {
      setState(() => loading = false);
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Gagal memuat lokasi')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Manajemen Lokasi'), backgroundColor: const Color(0xFFE65100)),
      body: Column(
        children: [
          Padding(padding: const EdgeInsets.all(16.0), child: Row(children: [
            Expanded(child: TextField(controller: searchController, decoration: const InputDecoration(prefixIcon: Icon(Icons.search), hintText: 'Cari lokasi...'))),
            const SizedBox(width:8),
            ElevatedButton(onPressed: loadLokasi, child: const Text('Refresh'))
          ])),
          Expanded(
            child: loading ? const Center(child: CircularProgressIndicator()) :
            lokasi.isEmpty ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [ Icon(Icons.place, size:80, color: Colors.grey[400]), const SizedBox(height:12), Text('Belum ada lokasi', style: TextStyle(color: Colors.grey[600])) ])) :
            ListView.separated(
              padding: const EdgeInsets.all(16),
              separatorBuilder: (_,__)=> const SizedBox(height:12),
              itemCount: lokasi.length,
              itemBuilder: (context, i) {
                final l = lokasi[i];
                return Card(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: ListTile(
                    leading: CircleAvatar(backgroundColor: Theme.of(context).primaryColor, child: Text((l['nama_lokasi'] ?? '-').toString().substring(0,1).toUpperCase(), style: const TextStyle(color: Colors.white))),
                    title: Text(l['nama_lokasi'] ?? '-'),
                    subtitle: Text('ID: ${l['id_lokasi'] ?? '-'}'),
                    trailing: Wrap(spacing:8, children: [
                      IconButton(icon: const Icon(Icons.visibility), onPressed: () { /* TODO: show items in lokasi */ }),
                      IconButton(icon: const Icon(Icons.delete), onPressed: () { /* TODO: delete lokasi */ }),
                    ]),
                  ),
                );
              }
            ),
          )
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: const Color(0xFF2E7D32),
        onPressed: () { /* TODO: create lokasi */ },
        icon: const Icon(Icons.add), label: const Text('Tambah Lokasi'),
      ),
    );
  }
}