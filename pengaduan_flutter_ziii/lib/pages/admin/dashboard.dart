import 'package:flutter/material.dart';
import '../../api/api_service.dart';
import 'users.dart';
import 'items.dart';
import 'lokasi.dart';
import 'petugas.dart';
import 'pengaduan.dart';

class AdminDashboardPage extends StatelessWidget {
  final ApiService api;
  const AdminDashboardPage({super.key, required this.api});

  Widget _tile(BuildContext c, IconData icon, String label, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: Colors.grey.withOpacity(0.06), blurRadius: 8)],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 36, color: const Color(0xFFE65100)),
            const SizedBox(height: 12),
            Text(label, style: const TextStyle(fontWeight: FontWeight.w600)),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final tiles = [
      {'icon': Icons.people, 'label': 'Manajemen User', 'page': UsersAdminPage(api: api)},
      {'icon': Icons.inventory_2, 'label': 'Manajemen Items', 'page': ItemsAdminPage(api: api)},
      {'icon': Icons.place, 'label': 'Manajemen Lokasi', 'page': LokasiAdminPage(api: api)},
      {'icon': Icons.engineering, 'label': 'Manajemen Petugas', 'page': PetugasAdminPage(api: api)},
      {'icon': Icons.report_problem, 'label': 'Manajemen Pengaduan', 'page': PengaduanAdminPage(api: api)},
    ];

    return Scaffold(
      appBar: AppBar(title: const Text('Admin Dashboard'), backgroundColor: const Color(0xFFE65100)),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: GridView.count(
          crossAxisCount: 2,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          children: tiles.map((t) {
            return _tile(context, t['icon'] as IconData, t['label'] as String, () {
              Navigator.push(context, MaterialPageRoute(builder: (_) => t['page'] as Widget));
            });
          }).toList(),
        ),
      ),
    );
  }
}