// filepath: d:\UKK Pengaduan sarpras\projeck ukk\pengaduan_flutter_ziii\lib\pages\admin\users.dart
import 'package:flutter/material.dart';
import '../../api/api_service.dart';

class UsersAdminPage extends StatefulWidget {
  final ApiService api;
  const UsersAdminPage({super.key, required this.api});

  @override
  State<UsersAdminPage> createState() => _UsersAdminPageState();
}

class _UsersAdminPageState extends State<UsersAdminPage> {
  List<dynamic> users = [];
  bool loading = true;
  final searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    // TODO: implement API client method (e.g. getAdminUsers) and load real data
    loadSample();
  }

  Future<void> loadSample() async {
    await Future.delayed(const Duration(milliseconds: 300));
    setState(() {
      loading = false;
      users = [
        {'nama_pengguna': 'Budi', 'username': 'budi01', 'email': 'budi@example.com', 'role': 'pengguna'},
        {'nama_pengguna': 'Admin', 'username': 'admin', 'email': 'admin@example.com', 'role': 'administrator'},
      ];
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Manajemen User'), backgroundColor: const Color(0xFFE65100)),
      body: Column(children: [
        Padding(padding: const EdgeInsets.all(16.0), child: Row(children: [
          Expanded(child: TextField(controller: searchController, decoration: const InputDecoration(prefixIcon: Icon(Icons.search), hintText: 'Cari user...'))),
          const SizedBox(width:8),
          ElevatedButton(onPressed: loadSample, child: const Text('Refresh'))
        ])),
        Expanded(
          child: loading ? const Center(child: CircularProgressIndicator()) :
          ListView.separated(
            padding: const EdgeInsets.all(16),
            separatorBuilder: (_,__)=> const SizedBox(height:12),
            itemCount: users.length,
            itemBuilder: (context, i) {
              final u = users[i];
              return Card(
                child: ListTile(
                  leading: CircleAvatar(backgroundColor: Theme.of(context).primaryColor, child: Text((u['nama_pengguna'] ?? '-').toString().substring(0,1).toUpperCase(), style: const TextStyle(color: Colors.white))),
                  title: Text(u['nama_pengguna'] ?? '-'),
                  subtitle: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(u['username'] ?? '-'),
                    const SizedBox(height:4),
                    Text(u['email'] ?? '-', style: const TextStyle(fontSize:12, color: Colors.grey)),
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
      floatingActionButton: FloatingActionButton.extended(onPressed: () {}, icon: const Icon(Icons.add), label: const Text('Tambah User')),
    );
  }
}