import 'package:flutter/material.dart';
import '../../api/api_service.dart';

class ItemsAdminPage extends StatefulWidget {
  final ApiService api;
  const ItemsAdminPage({super.key, required this.api});

  @override
  State<ItemsAdminPage> createState() => _ItemsAdminPageState();
}

class _ItemsAdminPageState extends State<ItemsAdminPage> {
  List<dynamic> items = [];
  bool loading = true;
  final searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    loadItems();
  }

  Future<void> loadItems() async {
    setState(() => loading = true);
    try {
      final data = await widget.api.getItem();
      setState(() { items = data; loading = false; });
    } catch (e) {
      setState(() => loading = false);
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Gagal memuat items')));
    }
  }

  String fotoUrl(String? foto) => ApiService.getFotoUrl(foto);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Manajemen Items'), backgroundColor: const Color(0xFFE65100)),
      body: Column(
        children: [
          Padding(padding: const EdgeInsets.all(16.0), child: Row(children: [
            Expanded(child: TextField(controller: searchController, decoration: const InputDecoration(prefixIcon: Icon(Icons.search), hintText: 'Cari items...'))),
            const SizedBox(width:8),
            ElevatedButton(onPressed: loadItems, style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF2E7D32)), child: const Text('Refresh'))
          ])),
          Expanded(
            child: loading ? const Center(child: CircularProgressIndicator()) :
            items.isEmpty ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [ Icon(Icons.inventory_2, size:80, color: Colors.grey[400]), const SizedBox(height:12), Text('Belum ada items', style: TextStyle(color: Colors.grey[600])) ])) :
            ListView.separated(
              padding: const EdgeInsets.all(16),
              separatorBuilder: (_,__)=> const SizedBox(height:12),
              itemCount: items.length,
              itemBuilder: (context, i) {
                final it = items[i];
                final url = fotoUrl(it['foto']);
                return Card(
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: ListTile(
                    leading: url.isNotEmpty ? ClipRRect(borderRadius: BorderRadius.circular(8), child: Image.network(url, width:56, height:56, fit: BoxFit.cover, errorBuilder: (_,__,___)=> const Icon(Icons.image_not_supported))) :
                      Container(width:56, height:56, decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(8)), child: const Icon(Icons.image)),
                    title: Text(it['nama_item'] ?? '-'),
                    subtitle: it['deskripsi'] != null ? Text(it['deskripsi'], maxLines:2, overflow: TextOverflow.ellipsis) : const Text('-', style: TextStyle(color: Colors.grey)),
                    trailing: Wrap(spacing:8, children: [
                      IconButton(icon: const Icon(Icons.edit), onPressed: () { /* TODO: navigate to edit */ }),
                      IconButton(icon: const Icon(Icons.delete), onPressed: () { /* TODO: delete */ }),
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
        onPressed: () { /* TODO: create item */ },
        icon: const Icon(Icons.add), label: const Text('Tambah Item'),
      ),
    );
  }
}