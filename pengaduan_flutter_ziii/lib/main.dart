import 'package:flutter/material.dart';
import 'pages/login_page.dart';
import 'api/api_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final api = ApiService();
  await api.init();
  
  runApp(MyApp(api: api));
}

class MyApp extends StatelessWidget {
  final ApiService api;
  const MyApp({super.key, required this.api});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Aplikasi Pengaduan Sarpras',
      theme: ThemeData(
        primarySwatch: Colors.blue,
        useMaterial3: true,
      ),
      // Selalu mulai dari halaman login
      home: const LoginPage(),
      debugShowCheckedModeBanner: false,
    );
  }
}
