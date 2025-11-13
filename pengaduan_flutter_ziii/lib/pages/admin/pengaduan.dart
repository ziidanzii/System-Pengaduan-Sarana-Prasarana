import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import '../../api/api_service.dart';
import '../login_page.dart';

class PengaduanAdminPage extends StatefulWidget {
  final ApiService api;
  const PengaduanAdminPage({super.key, required this.api});

  @override
  State<PengaduanAdminPage> createState() => _PengaduanAdminPageState();
}

class _PengaduanAdminPageState extends State<PengaduanAdminPage> {
  List<dynamic> pengaduan = [];
  bool isLoading = true;
  bool isRefreshing = false;
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
      setState(() { isLoading = true; });
      final status = selectedStatus == 'Semua' || selectedStatus == null ? null : selectedStatus;
      final search = searchController.text.isEmpty ? null : searchController.text;
      final data = await widget.api.getAdminPengaduan(status: status, search: search);
      setState(() {
        pengaduan = data;
        isLoading = false;
        isRefreshing = false;
      });
    } catch (e) {
      setState(() { isLoading = false; isRefreshing = false; });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString().replaceAll('Exception: ', ''))),
        );
      }
    }
  }

  String getFotoUrl(dynamic item) {
    if (item == null) return '';
    if (item['foto'] == null || item['foto'].toString().isEmpty) return '';
    return ApiService.getFotoUrl(item['foto']);
  }

  String _getItemName(dynamic itemObj) {
    if (itemObj == null) return '';
    if (itemObj is String) return itemObj;
    if (itemObj is Map) {
      return (itemObj['nama_item'] ?? itemObj['nama'] ?? itemObj['name'] ?? itemObj['item'] ?? '').toString();
    }
    return itemObj.toString();
  }

  String _formatStatus(String status) {
    switch (status.toLowerCase()) {
      case 'diajukan': return 'Diajukan';
      case 'disetujui': return 'Disetujui';
      case 'ditolak': return 'Ditolak';
      case 'diproses': return 'Diproses';
      case 'selesai': return 'Selesai';
      default: return status;
    }
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'diajukan': return Colors.orange;
      case 'disetujui': return Colors.blue;
      case 'ditolak': return Colors.red;
      case 'diproses': return Colors.purple;
      case 'selesai': return Colors.green;
      default: return Colors.grey;
    }
  }

  Widget _buildStatusBadge(String status) {
    final color = _getStatusColor(status);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.08),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.9), width: 1),
      ),
      child: Text(_formatStatus(status),
        style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.w600),
      ),
    );
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '-';
    try {
      final date = DateTime.parse(dateString);
      return '${date.day}/${date.month}/${date.year} ${date.hour}:${date.minute.toString().padLeft(2,'0')}';
    } catch (e) {
      return dateString;
    }
  }

  Future<void> updateStatus(int id, String newStatus) async {
    final result = await widget.api.updatePengaduanStatus(id: id, status: newStatus);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Status diperbarui'),
          backgroundColor: result['success'] == true ? Colors.green : Colors.red),
      );
      if (result['success'] == true) loadPengaduan();
    }
  }

  void showUpdateStatusDialog(int id, String currentStatus) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Ubah Status Pengaduan'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: statusList.where((s) => s != 'Semua' && s != currentStatus).map((status) =>
            ListTile(title: Text(status), onTap: () {
              Navigator.pop(context);
              updateStatus(id, status);
            })
          ).toList(),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Manajemen Pengaduan'),
        backgroundColor: const Color(0xFFE65100),
        actions: [
          IconButton(icon: const Icon(Icons.logout), onPressed: () async {
            final confirm = await showDialog<bool>(context: context, builder: (c) => AlertDialog(
              title: const Text('Logout'), content: const Text('Apakah Anda yakin ingin logout?'),
              actions: [
                TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Batal')),
                TextButton(onPressed: () => Navigator.pop(c, true), child: const Text('Logout')),
              ],
            ));
            if (confirm == true) {
              await widget.api.logout();
              if (mounted) Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => const LoginPage()), (r) => false);
            }
          }),
        ],
      ),
      body: Column(
        children: [
          Padding(padding: const EdgeInsets.all(16.0), child: Column(
            children: [
              TextField(
                controller: searchController,
                decoration: InputDecoration(
                  labelText: 'Cari pengaduan', prefixIcon: const Icon(Icons.search),
                  suffixIcon: searchController.text.isNotEmpty ? IconButton(icon: const Icon(Icons.clear), onPressed: () { searchController.clear(); loadPengaduan(); }) : null,
                  border: const OutlineInputBorder(),
                ),
                onSubmitted: (_) => loadPengaduan(),
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: selectedStatus,
                decoration: const InputDecoration(labelText: 'Filter Status', border: OutlineInputBorder()),
                items: statusList.map((s) => DropdownMenuItem<String>(value: s == 'Semua' ? null : s, child: Text(s))).toList(),
                onChanged: (v) { setState(() => selectedStatus = v); loadPengaduan(); },
              ),
            ],
          )),
          Expanded(
            child: isLoading ? const Center(child: CircularProgressIndicator()) :
            pengaduan.isEmpty ? Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
              Icon(Icons.report_problem, size: 80, color: Colors.grey[400]),
              const SizedBox(height: 16),
              Text("Tidak ada pengaduan", style: TextStyle(fontSize: 18, color: Colors.grey[600])),
            ])) :
            RefreshIndicator(
              onRefresh: () { setState(() => isRefreshing = true); return loadPengaduan(); },
              color: const Color(0xFFE65100),
              child: Stack(children: [
                ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: pengaduan.length,
                  itemBuilder: (context, index) {
                    final item = pengaduan[index];
                    final fotoUrl = getFotoUrl(item);
                    return Container(
                      margin: const EdgeInsets.only(bottom: 16),
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16),
                        boxShadow: [BoxShadow(color: Colors.grey.withOpacity(0.08), blurRadius: 10, offset: const Offset(0,3))],
                        border: Border.all(color: Colors.grey.shade200, width: 1)
                      ),
                      child: ExpansionTile(
                        leading: Container(width:44,height:44,decoration: BoxDecoration(color: const Color(0xFFFF7043).withOpacity(0.1),borderRadius: BorderRadius.circular(10)),child: const Icon(Icons.report_problem, color: Color(0xFFE65100), size: 20)),
                        title: Text(item['nama_pengaduan'] ?? 'Tanpa nama', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 16), maxLines:1, overflow: TextOverflow.ellipsis),
                        subtitle: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const SizedBox(height:6),
                          _buildStatusBadge(item['status'] ?? 'Tidak diketahui'),
                          const SizedBox(height:8),
                          if (item['lokasi'] != null) Row(children: [ Icon(Icons.location_on, size:14, color: Colors.grey[600]), const SizedBox(width:4), Expanded(child: Text('${item['lokasi']}', style: TextStyle(fontSize:12, color: Colors.grey[600]), maxLines:1, overflow: TextOverflow.ellipsis)) ]),
                          if (item['tgl_pengajuan'] != null) Row(children: [ Icon(Icons.calendar_today, size:14, color: Colors.grey[600]), const SizedBox(width:4), Text(_formatDate(item['tgl_pengajuan']), style: TextStyle(fontSize:12, color: Colors.grey[600])) ]),
                        ]),
                        trailing: fotoUrl.isNotEmpty ? ClipRRect(borderRadius: BorderRadius.circular(8), child: Image.network(fotoUrl, width:60, height:60, fit: BoxFit.cover, errorBuilder: (c,e,s) => const Icon(Icons.image_not_supported, size:40))) :
                          Container(width:60, height:60, decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(8)), child: const Icon(Icons.photo, color: Colors.grey, size:32)),
                        children: [
                          Padding(padding: const EdgeInsets.all(16.0), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            if (item['deskripsi'] != null) ...[
                              Row(children: const [ Icon(Icons.description, size:16, color: Color(0xFFE65100)), SizedBox(width:8), Text('Deskripsi:', style: TextStyle(fontWeight: FontWeight.w600)) ]),
                              const SizedBox(height:8),
                              Container(width: double.infinity, padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: Colors.grey[50], borderRadius: BorderRadius.circular(8)), child: Text(item['deskripsi'])),
                              const SizedBox(height:16),
                            ],
                            if (item['item'] != null) ...[
                              Row(children: const [ Icon(Icons.category, size:16, color: Color(0xFFE65100)), SizedBox(width:8), Text('Item:', style: TextStyle(fontWeight: FontWeight.w600)) ]),
                              const SizedBox(height:8),
                              Container(width: double.infinity, padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: Colors.grey[50], borderRadius: BorderRadius.circular(8)), child: Text(_getItemName(item['item']))),
                              const SizedBox(height:16),
                            ],
                            if (item['foto'] != null && item['foto'].toString().isNotEmpty) ...[
                              Row(children: const [ Icon(Icons.photo, size:16, color: Color(0xFFE65100)), SizedBox(width:8), Text('Foto Pengaduan:', style: TextStyle(fontWeight: FontWeight.w600)) ]),
                              const SizedBox(height:8),
                              ClipRRect(borderRadius: BorderRadius.circular(8), child: Image.network(fotoUrl, width: double.infinity, height:200, fit: BoxFit.cover, errorBuilder: (c,e,s) => Container(height:200, color: Colors.grey[300], child: Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: const [ Icon(Icons.image_not_supported, size:50), SizedBox(height:8), Text('Gagal memuat foto') ]))))),
                              const SizedBox(height:16),
                            ],
                            if (item['saran_petugas'] != null) ...[
                              Row(children: const [ Icon(Icons.construction, size:16, color: Color(0xFFE65100)), SizedBox(width:8), Text('Saran Petugas:', style: TextStyle(fontWeight: FontWeight.w600)) ]),
                              const SizedBox(height:8),
                              Container(width: double.infinity, padding: const EdgeInsets.all(12), decoration: BoxDecoration(color: const Color(0xFFE65100).withOpacity(0.05), borderRadius: BorderRadius.circular(8), border: Border.all(color: const Color(0xFFE65100).withOpacity(0.2))), child: Text(item['saran_petugas'])),
                              const SizedBox(height:16),
                            ],
                            ElevatedButton.icon(
                              onPressed: () => showUpdateStatusDialog(item['id_pengaduan'], item['status'] ?? ''),
                              icon: const Icon(Icons.edit), label: const Text('Ubah Status'),
                              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFE65100), foregroundColor: Colors.white),
                            )
                          ]))
                        ],
                      ),
                    );
                  }
                ),
                if (isRefreshing) const Positioned(top:0,left:0,right:0,child: LinearProgressIndicator(backgroundColor: Colors.transparent, valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFE65100)))),
              ]),
            ),
          ),
        ],
      ),
    );
  }
}
