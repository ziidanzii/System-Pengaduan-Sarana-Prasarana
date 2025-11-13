import 'package:flutter/material.dart';
import '../api/api_service.dart';

class ProfilePage extends StatefulWidget {
  final ApiService api;
  const ProfilePage({super.key, required this.api});

  @override
  State<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  Map<String, dynamic>? userData;
  bool isLoading = true;
  bool isEditing = false;

  // Controller untuk form edit
  final namaController = TextEditingController();
  final usernameController = TextEditingController();
  final passwordController = TextEditingController();
  final confirmPasswordController = TextEditingController();

  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    try {
      final data = await widget.api.getUserData();
      setState(() {
        userData = data;
        // Set initial values untuk controller
        namaController.text = data?['nama_pengguna'] ?? '';
        usernameController.text = data?['username'] ?? '';
        passwordController.clear();
        confirmPasswordController.clear();
        isLoading = false;
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
        namaController.text = 'User';
        usernameController.text = 'user';
        passwordController.clear();
        confirmPasswordController.clear();
        isLoading = false;
      });
    }
  }

  Future<void> _updateProfile() async {
    // Validasi form
    if (namaController.text.isEmpty || usernameController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Nama dan username harus diisi'),
          backgroundColor: Color(0xFFF44336),
        ),
      );
      return;
    }

    // Validasi password jika diisi
    if (passwordController.text.isNotEmpty) {
      if (passwordController.text.length < 6) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Password minimal 6 karakter'),
            backgroundColor: Color(0xFFF44336),
          ),
        );
        return;
      }

      if (passwordController.text != confirmPasswordController.text) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Password dan konfirmasi password tidak sama'),
            backgroundColor: Color(0xFFF44336),
          ),
        );
        return;
      }
    }

    setState(() => isLoading = true);
    
    // Simulasi update profile
    try {
      // Anda perlu menambahkan method updateProfile di ApiService
      // final result = await widget.api.updateProfile(
      //   namaPengguna: namaController.text,
      //   username: usernameController.text,
      //   password: passwordController.text.isNotEmpty ? passwordController.text : null,
      // );
      
      await Future.delayed(Duration(seconds: 2));
      
      setState(() {
        userData = {
          'nama_pengguna': namaController.text,
          'username': usernameController.text,
          'email': userData?['email'] ?? 'user@example.com',
          'role': userData?['role'] ?? 'user'
        };
        isEditing = false;
        isLoading = false;
        
        // Clear password fields
        passwordController.clear();
        confirmPasswordController.clear();
        _obscurePassword = true;
        _obscureConfirmPassword = true;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Profil berhasil diperbarui'),
          backgroundColor: Color(0xFF4CAF50),
        ),
      );
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Gagal memperbarui profil: $e'),
          backgroundColor: Color(0xFFF44336),
        ),
      );
    }
  }

  void _cancelEdit() {
    // Reset ke nilai semula
    namaController.text = userData?['nama_pengguna'] ?? '';
    usernameController.text = userData?['username'] ?? '';
    passwordController.clear();
    confirmPasswordController.clear();
    _obscurePassword = true;
    _obscureConfirmPassword = true;
    setState(() => isEditing = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: Text(
          "Profil Pengguna",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w600,
          ),
        ),
        backgroundColor: Color(0xFFE65100),
        elevation: 0,
        iconTheme: IconThemeData(color: Colors.white),
        actions: [
          if (!isEditing && !isLoading)
            IconButton(
              icon: Icon(Icons.edit, color: Colors.white),
              onPressed: () => setState(() => isEditing = true),
              tooltip: 'Edit Profil',
            ),
        ],
      ),
      body: isLoading
          ? Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFE65100)),
              ),
            )
          : SingleChildScrollView(
              padding: EdgeInsets.all(24),
              child: Column(
                children: [
                  // Profile Header
                  Container(
                    width: double.infinity,
                    padding: EdgeInsets.all(24),
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
                      children: [
                        CircleAvatar(
                          radius: 40,
                          backgroundColor: Colors.white,
                          child: Icon(
                            Icons.person,
                            size: 40,
                            color: Color(0xFFE65100),
                          ),
                        ),
                        SizedBox(height: 16),
                        Text(
                          userData?['nama_pengguna'] ?? 'User',
                          style: TextStyle(
                            fontSize: 24,
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        SizedBox(height: 4),
                        Text(
                          '@${userData?['username'] ?? 'user'}',
                          style: TextStyle(
                            fontSize: 16,
                            color: Colors.white.withOpacity(0.9),
                          ),
                        ),
                        SizedBox(height: 8),
                        Container(
                          padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            (userData?['role'] ?? 'user').toUpperCase(),
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.white,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  SizedBox(height: 32),

                  // Profile Form
                  Container(
                    width: double.infinity,
                    padding: EdgeInsets.all(24),
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
                          "Informasi Profil",
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w600,
                            color: Color(0xFFE65100),
                          ),
                        ),
                        SizedBox(height: 4),
                        Text(
                          isEditing 
                              ? "Edit informasi profil Anda"
                              : "Detail informasi akun Anda",
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.grey[600],
                          ),
                        ),
                        SizedBox(height: 24),

                        // Nama Lengkap (Editable)
                        _buildEditableField(
                          label: "Nama Lengkap",
                          value: userData?['nama_pengguna'] ?? '',
                          isEditing: isEditing,
                          controller: namaController,
                          icon: Icons.person,
                          hintText: "Masukkan nama lengkap",
                        ),

                        SizedBox(height: 20),

                        // Username (Editable)
                        _buildEditableField(
                          label: "Username",
                          value: userData?['username'] ?? '',
                          isEditing: isEditing,
                          controller: usernameController,
                          icon: Icons.alternate_email,
                          hintText: "Masukkan username",
                        ),

                        SizedBox(height: 20),

                        // Email (Readonly)
                        _buildReadOnlyField(
                          label: "Email",
                          value: userData?['email'] ?? '',
                          icon: Icons.email,
                        ),

                        SizedBox(height: 20),

                        // Role (Readonly)
                        _buildReadOnlyField(
                          label: "Role",
                          value: (userData?['role'] ?? 'user').toUpperCase(),
                          icon: Icons.security,
                        ),

                        // Password Fields (hanya muncul saat edit)
                        if (isEditing) ...[
                          SizedBox(height: 20),
                          _buildPasswordField(
                            label: "Password Baru",
                            controller: passwordController,
                            obscureText: _obscurePassword,
                            onToggleVisibility: () {
                              setState(() => _obscurePassword = !_obscurePassword);
                            },
                            hintText: "Kosongkan jika tidak ingin mengubah",
                          ),

                          SizedBox(height: 20),

                          _buildPasswordField(
                            label: "Konfirmasi Password Baru",
                            controller: confirmPasswordController,
                            obscureText: _obscureConfirmPassword,
                            onToggleVisibility: () {
                              setState(() => _obscureConfirmPassword = !_obscureConfirmPassword);
                            },
                            hintText: "Ulangi password baru",
                          ),

                          SizedBox(height: 20),

                          Container(
                            padding: EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Color(0xFFE65100).withOpacity(0.05),
                              borderRadius: BorderRadius.circular(8),
                              border: Border.all(color: Color(0xFFE65100).withOpacity(0.2)),
                            ),
                            child: Row(
                              children: [
                                Icon(Icons.info, size: 16, color: Color(0xFFE65100)),
                                SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    'Kosongkan password jika tidak ingin mengubah',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],

                        SizedBox(height: 32),

                        // Action Buttons
                        if (isEditing) ...[
                          Row(
                            children: [
                              Expanded(
                                child: OutlinedButton(
                                  onPressed: _cancelEdit,
                                  style: OutlinedButton.styleFrom(
                                    padding: EdgeInsets.symmetric(vertical: 16),
                                    side: BorderSide(color: Colors.grey),
                                  ),
                                  child: Text(
                                    'Batal',
                                    style: TextStyle(color: Colors.grey[700]),
                                  ),
                                ),
                              ),
                              SizedBox(width: 16),
                              Expanded(
                                child: Container(
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
                                  child: TextButton(
                                    onPressed: isLoading ? null : _updateProfile,
                                    style: TextButton.styleFrom(
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                    ),
                                    child: isLoading
                                        ? SizedBox(
                                            width: 20,
                                            height: 20,
                                            child: CircularProgressIndicator(
                                              strokeWidth: 2,
                                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                            ),
                                          )
                                        : Text(
                                            "Simpan Perubahan",
                                            style: TextStyle(
                                              color: Colors.white,
                                              fontSize: 16,
                                              fontWeight: FontWeight.w600,
                                            ),
                                          ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildEditableField({
    required String label,
    required String value,
    required bool isEditing,
    required TextEditingController controller,
    required IconData icon,
    required String hintText,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 16, color: Color(0xFFE65100)),
            SizedBox(width: 8),
            Text(
              label,
              style: TextStyle(
                fontWeight: FontWeight.w600,
                color: Colors.grey[700],
              ),
            ),
          ],
        ),
        SizedBox(height: 8),
        isEditing
            ? Container(
                decoration: BoxDecoration(
                  color: Colors.grey[50],
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: Colors.grey.shade300,
                    width: 1,
                  ),
                ),
                child: TextField(
                  controller: controller,
                  style: TextStyle(
                    color: Colors.grey[800],
                    fontSize: 16,
                  ),
                  decoration: InputDecoration(
                    contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                    border: InputBorder.none,
                    hintText: hintText,
                    hintStyle: TextStyle(color: Colors.grey[400]),
                  ),
                ),
              )
            : Container(
                width: double.infinity,
                padding: EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.grey[50],
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  value,
                  style: TextStyle(
                    color: Colors.grey[700],
                    fontSize: 16,
                  ),
                ),
              ),
      ],
    );
  }

  Widget _buildReadOnlyField({
    required String label,
    required String value,
    required IconData icon,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 16, color: Color(0xFFE65100)),
            SizedBox(width: 8),
            Text(
              label,
              style: TextStyle(
                fontWeight: FontWeight.w600,
                color: Colors.grey[700],
              ),
            ),
            SizedBox(width: 8),
            Container(
              padding: EdgeInsets.symmetric(horizontal: 8, vertical: 2),
              decoration: BoxDecoration(
                color: Colors.grey[200],
                borderRadius: BorderRadius.circular(4),
              ),
              child: Text(
                'TIDAK BISA DIUBAH',
                style: TextStyle(
                  fontSize: 10,
                  color: Colors.grey[600],
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ],
        ),
        SizedBox(height: 8),
        Container(
          width: double.infinity,
          padding: EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.grey[100],
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: Colors.grey[300]!,
            ),
          ),
          child: Text(
            value,
            style: TextStyle(
              color: Colors.grey[600],
              fontSize: 16,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPasswordField({
    required String label,
    required TextEditingController controller,
    required bool obscureText,
    required VoidCallback onToggleVisibility,
    required String hintText,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(Icons.lock, size: 16, color: Color(0xFFE65100)),
            SizedBox(width: 8),
            Text(
              label,
              style: TextStyle(
                fontWeight: FontWeight.w600,
                color: Colors.grey[700],
              ),
            ),
          ],
        ),
        SizedBox(height: 8),
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
            controller: controller,
            obscureText: obscureText,
            style: TextStyle(
              color: Colors.grey[800],
              fontSize: 16,
            ),
            decoration: InputDecoration(
              contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              border: InputBorder.none,
              hintText: hintText,
              hintStyle: TextStyle(color: Colors.grey[400]),
              suffixIcon: IconButton(
                icon: Icon(
                  obscureText ? Icons.visibility : Icons.visibility_off,
                  color: Colors.grey[500],
                ),
                onPressed: onToggleVisibility,
              ),
            ),
          ),
        ),
      ],
    );
  }
}