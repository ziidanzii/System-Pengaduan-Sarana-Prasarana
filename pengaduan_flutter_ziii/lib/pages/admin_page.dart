import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import '../api/api_service.dart';
import 'login_page.dart';

class AdminPage extends StatefulWidget {
  final ApiService api;
  const AdminPage({super.key, required this.api});

  @override
  State<AdminPage> createState() => _AdminPageState();
}

class _AdminPageState extends State<AdminPage> {
  List<dynamic> pengaduan = [];
  bool isLoading = true;
  String? selectedStatus;
  final searchController = TextEditingController();

  final List<String> statusList = [
    'Semua',
    'Diajukan',
    'Disetujui',
    'Ditolak',
    'Diproses',
    'Selesai',
  ];

  @override
  void initState() {
    super.initState();
    loadPengaduan();
  }

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }

  Future<void> loadPengaduan() async {
    try {
      setState(() => isLoading = true);
      final status = selectedStatus == 'Semua' || selectedStatus == null ? null : selectedStatus;
      final search = searchController.text.isEmpty ? null : searchController.text;
      final data = await widget.api.getAdminPengaduan(status: status, search: search);
      setState(() {
        pengaduan = data;
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

  String getFotoUrl(dynamic item) {
    if (item['foto'] == null) return '';
    if (item['foto'].toString().startsWith('http')) {
      return item['foto'];
    }
    final baseUrl = kIsWeb ? 'http://127.0.0.1:8000' : 'http://127.0.0.1:8000';
    return '$baseUrl/storage/${item['foto']}';
  }

  Color getStatusColor(String? status) {
    switch (status) {
      case 'Diajukan':
        return Colors.orange;
      case 'Disetujui':
        return Colors.blue;
      case 'Ditolak':
        return Colors.red;
      case 'Diproses':
        return Colors.purple;
      case 'Selesai':
        return Colors.green;
      default:
        return Colors.grey;
    }
  }

  Future<void> updateStatus(int id, String newStatus) async {
    final result = await widget.api.updatePengaduanStatus(
      id: id,
      status: newStatus,
    );

    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? 'Status diperbarui'),
          backgroundColor: result['success'] == true ? Colors.green : Colors.red,
        ),
      );

      if (result['success'] == true) {
        loadPengaduan(); // Reload data
      }
    }
  }

  void showUpdateStatusDialog(int id, String currentStatus) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Ubah Status Pengaduan'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: statusList
              .where((s) => s != 'Semua' && s != currentStatus)
              .map((status) => ListTile(
                    title: Text(status),
                    onTap: () {
                      Navigator.pop(context);
                      updateStatus(id, status);
                    },
                  ))
              .toList(),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Admin - Manajemen Pengaduan"),
        actions: [
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
      body: Column(
        children: [
          // Filter dan Search
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              children: [
                // Search
                TextField(
                  controller: searchController,
                  decoration: InputDecoration(
                    labelText: 'Cari pengaduan',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: searchController.text.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.clear),
                            onPressed: () {
                              searchController.clear();
                              loadPengaduan();
                            },
                          )
                        : null,
                    border: const OutlineInputBorder(),
                  ),
                  onSubmitted: (_) => loadPengaduan(),
                ),
                const SizedBox(height: 12),
                // Status Filter
                DropdownButtonFormField<String>(
                  value: selectedStatus,
                  decoration: const InputDecoration(
                    labelText: 'Filter Status',
                    border: OutlineInputBorder(),
                  ),
                  items: statusList.map((status) {
                    return DropdownMenuItem<String>(
                      value: status == 'Semua' ? null : status,
                      child: Text(status),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() => selectedStatus = value);
                    loadPengaduan();
                  },
                ),
              ],
            ),
          ),
          // List Pengaduan
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : pengaduan.isEmpty
                    ? const Center(child: Text("Tidak ada pengaduan"))
                    : RefreshIndicator(
                        onRefresh: loadPengaduan,
                        child: ListView.builder(
                          itemCount: pengaduan.length,
                          itemBuilder: (context, index) {
                            final item = pengaduan[index];
                            final fotoUrl = getFotoUrl(item);

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
                                    Row(
                                      children: [
                                        Container(
                                          padding: const EdgeInsets.symmetric(
                                            horizontal: 8,
                                            vertical: 4,
                                          ),
                                          decoration: BoxDecoration(
                                            color: getStatusColor(item['status']),
                                            borderRadius: BorderRadius.circular(4),
                                          ),
                                          child: Text(
                                            item['status'] ?? 'Tidak diketahui',
                                            style: const TextStyle(
                                              color: Colors.white,
                                              fontSize: 12,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 4),
                                    if (item['user'] != null)
                                      Text('User: ${item['user']['nama_pengguna'] ?? 'Tidak diketahui'}'),
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
                                          const SizedBox(height: 16),
                                        ],
                                        if (item['saran_petugas'] != null) ...[
                                          const Text(
                                            'Saran Petugas:',
                                            style: TextStyle(fontWeight: FontWeight.bold),
                                          ),
                                          const SizedBox(height: 4),
                                          Text(item['saran_petugas']),
                                          const SizedBox(height: 16),
                                        ],
                                        // Button Update Status
                                        ElevatedButton.icon(
                                          onPressed: () => showUpdateStatusDialog(
                                            item['id_pengaduan'],
                                            item['status'] ?? '',
                                          ),
                                          icon: const Icon(Icons.edit),
                                          label: const Text('Ubah Status'),
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor: Colors.blue,
                                            foregroundColor: Colors.white,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }
}


