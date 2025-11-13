// filepath: d:\UKK Pengaduan sarpras\projeck ukk\pengaduan_flutter_ziii\lib\pages\admin\petugas.dart
import 'package:flutter/material.dart';
import '../../api/api_service.dart';

class PetugasAdminPage extends StatefulWidget {
  final ApiService api;
  const PetugasAdminPage({super.key, required this.api});

  @override
  State<PetugasAdminPage> createState() => _PetugasAdminPageState();
}

class _PetugasAdminPageState extends State<PetugasAdminPage> {
  List<dynamic> petugas = [];
  bool loading = true;
  final searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    // TODO: implement API in ApiService (e.g. getPetugas) then call it here
    loadSample();
  }

  Future<void> loadSample() async {
    await Future.delayed(const Duration(milliseconds: 300));
    setState(() {
      loading = false;
      petugas = [
        // sample placeholders
        {'nama': 'Andi', 'telp': '0812-xxxx', 'gender': 'L', 'username': 'andi123'},
        {'nama': 'Siti', 'telp': '0813-xxxx', 'gender': 'P', 'username': 'siti01'},
      ];
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Manajemen Petugas'), backgroundColor: const Color(0xFFE65100)),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16.0), child: Row(children: [
          Expanded(child: TextField(controller: searchController, decoration: const InputDecoration(prefixIcon: Icon(Icons.search), hintText: 'Cari petugas...'))),
          const SizedBox(width:8),
          ElevatedButton(onPressed: loadSample, child: const Text('Refresh'))
        ])),
        Expanded(
          child: loading ? const Center(child: CircularProgressIndicator()) :
          ListView.separated(
            padding: const EdgeInsets.all(16),
            separatorBuilder: (_,__)=> const SizedBox(height:12),
            itemCount: petugas.length,
            itemBuilder: (context, i) {
              final p = petugas[i];
              return Card(
                child: ListTile(
                  leading: CircleAvatar(backgroundColor: Theme.of(context).primaryColor, child: Text((p['nama'] ?? '-').toString().substring(0,1).toUpperCase(), style: const TextStyle(color: Colors.white))),
                  title: Text(p['nama'] ?? '-'),
                  subtitle: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(p['telp'] ?? '-'),
                    const SizedBox(height:4),
                    Text(p['gender'] == 'L' ? 'Laki-laki' : 'Perempuan', style: const TextStyle(fontSize:12, color: Colors.grey)),
                  ]),
                  trailing: Wrap(spacing:8, children: [
                    IconButton(icon: const Icon(Icons.edit), onPressed: () {}),
                    IconButton(icon: const Icon(Icons.delete), onPressed: () {}),
                  ]),
                ),
              );
            }
          )
        )
      ]),
      floatingActionButton: FloatingActionButton.extended(onPressed: () {}, icon: const Icon(Icons.add), label: const Text('Tambah Petugas')),
    );
  }
}