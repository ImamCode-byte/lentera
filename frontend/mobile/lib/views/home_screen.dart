import 'package:flutter/material.dart';
import '../config/app_config.dart';
import '../models/work_model.dart';
import '../models/faculty_model.dart';
import '../services/api_service.dart';
import '../widgets/state_widgets.dart';
import 'search_screen.dart';
import 'detail_screen.dart';
import 'login_screen.dart';
import 'author_works_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  bool _isLoading = true;
  String? _errorMessage;
  List<WorkModel> _recentWorks = [];
  List<FacultyModel> _faculties = [];
  bool _isLoggedIn = false;
  String _userName = '';

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  Future<void> _loadInitialData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    // Periksa status login
    final user = await ApiService.getCurrentUser();
    final token = await ApiService.getToken();

    final worksResult = await ApiService.getRecentWorks(limit: 10);
    final facultiesResult = await ApiService.getFaculties();

    if (mounted) {
      setState(() {
        _isLoggedIn = token != null && user != null;
        _userName = user?.name ?? '';

        if (worksResult.isSuccess) {
          _recentWorks = worksResult.data ?? [];
        } else {
          _errorMessage = worksResult.message;
        }

        if (facultiesResult.isSuccess) {
          _faculties = facultiesResult.data ?? [];
        }
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(AppConfig.bgLightValue),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: const Color(AppConfig.tealPrimaryValue),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(Icons.local_fire_department_rounded, color: Colors.white, size: 20),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: const [
                Text(
                  AppConfig.appName,
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    color: Color(AppConfig.tealPrimaryValue),
                    letterSpacing: -0.5,
                  ),
                ),
                Text(
                  'UNIVERSITAS MADURA',
                  style: TextStyle(
                    fontSize: 9,
                    fontWeight: FontWeight.bold,
                    color: Color(AppConfig.goldAccentValue),
                    letterSpacing: 0.5,
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.search_rounded, color: Color(AppConfig.tealDarkValue)),
            tooltip: 'Cari Naskah',
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const SearchScreen()),
              );
            },
          ),
          if (_isLoggedIn)
            PopupMenuButton<String>(
              icon: const Icon(Icons.account_circle_rounded, color: Color(AppConfig.tealPrimaryValue)),
              onSelected: (value) async {
                if (value == 'my_works') {
                  Navigator.push(
                    context,
                    MaterialPageRoute(builder: (_) => const AuthorWorksScreen()),
                  );
                } else if (value == 'logout') {
                  await ApiService.clearSession();
                  _loadInitialData();
                }
              },
              itemBuilder: (context) => [
                PopupMenuItem(
                  enabled: false,
                  child: Text('Halo, $_userName', style: const TextStyle(fontWeight: FontWeight.bold)),
                ),
                const PopupMenuItem(
                  value: 'my_works',
                  child: Row(
                    children: [
                      Icon(Icons.library_books_rounded, size: 18, color: Colors.blueGrey),
                      SizedBox(width: 8),
                      Text('Karya Saya'),
                    ],
                  ),
                ),
                const PopupMenuItem(
                  value: 'logout',
                  child: Row(
                    children: [
                      Icon(Icons.logout_rounded, size: 18, color: Colors.redAccent),
                      SizedBox(width: 8),
                      Text('Keluar Akun'),
                    ],
                  ),
                ),
              ],
            )
          else
            TextButton(
              onPressed: () async {
                final result = await Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const LoginScreen()),
                );
                if (result == true) {
                  _loadInitialData();
                }
              },
              child: const Text(
                'Masuk',
                style: TextStyle(
                  color: Color(AppConfig.tealPrimaryValue),
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
        ],
      ),
      body: SafeArea(
        child: RefreshIndicator(
          color: const Color(AppConfig.tealPrimaryValue),
          onRefresh: _loadInitialData,
          child: _isLoading
              ? const LoadingStateWidget(message: 'Memuat etalase Lentera...')
              : _errorMessage != null && _recentWorks.isEmpty
                  ? ErrorStateWidget(
                      errorMessage: _errorMessage!,
                      onRetry: _loadInitialData,
                    )
                  : SingleChildScrollView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Banner Hero Selamat Datang
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [Color(AppConfig.tealDarkValue), Color(AppConfig.tealPrimaryValue)],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              borderRadius: BorderRadius.circular(16),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(AppConfig.tealPrimaryValue).withOpacity(0.2),
                                  blurRadius: 12,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Repositori Ilmiah Digital',
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 20,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                const Text(
                                  'Akses skripsi, tesis, dan jurnal terkurasi sivitas akademika Universitas Madura.',
                                  style: TextStyle(
                                    color: Colors.white70,
                                    fontSize: 12,
                                    height: 1.4,
                                  ),
                                ),
                                const SizedBox(height: 16),
                                GestureDetector(
                                  onTap: () {
                                    Navigator.push(
                                      context,
                                      MaterialPageRoute(builder: (_) => const SearchScreen()),
                                    );
                                  },
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                                    decoration: BoxDecoration(
                                      color: Colors.white,
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Row(
                                      children: const [
                                        Icon(Icons.search_rounded, color: Colors.blueGrey, size: 20),
                                        SizedBox(width: 10),
                                        Text(
                                          'Cari judul, penulis, kata kunci...',
                                          style: TextStyle(color: Colors.blueGrey, fontSize: 13),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),

                          const SizedBox(height: 24),

                          // Bagian Fakultas
                          if (_faculties.isNotEmpty) ...[
                            const Text(
                              'Jelajah per Fakultas',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: Color(AppConfig.tealDarkValue),
                              ),
                            ),
                            const SizedBox(height: 12),
                            SizedBox(
                              height: 42,
                              child: ListView.separated(
                                scrollDirection: Axis.horizontal,
                                itemCount: _faculties.length,
                                separatorBuilder: (_, __) => const SizedBox(width: 8),
                                itemBuilder: (context, index) {
                                  final faculty = _faculties[index];
                                  return ActionChip(
                                    label: Text('${faculty.code} (${faculty.worksCount})'),
                                    labelStyle: const TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: Color(AppConfig.tealPrimaryValue),
                                    ),
                                    backgroundColor: Colors.white,
                                    side: BorderSide(color: Colors.grey.shade300),
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                                    onPressed: () {
                                      Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                          builder: (_) => SearchScreen(initialFacultyCode: faculty.code),
                                        ),
                                      );
                                    },
                                  );
                                },
                              ),
                            ),
                            const SizedBox(height: 24),
                          ],

                          // Bagian Naskah Terbaru
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'Karya Terbaru Terbit',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: Color(AppConfig.tealDarkValue),
                                ),
                              ),
                              TextButton(
                                onPressed: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(builder: (_) => const SearchScreen()),
                                  );
                                },
                                child: const Text(
                                  'Lihat Semua',
                                  style: TextStyle(
                                    color: Color(AppConfig.tealPrimaryValue),
                                    fontWeight: FontWeight.w600,
                                    fontSize: 13,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),

                          if (_recentWorks.isEmpty)
                            const EmptyStateWidget(
                              title: 'Belum Ada Naskah Terbit',
                              message: 'Koleksi karya ilmiah yang berstatus terbit akan muncul di sini.',
                            )
                          else
                            ListView.builder(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: _recentWorks.length,
                              itemBuilder: (context, index) {
                                final work = _recentWorks[index];
                                return WorkCardWidget(
                                  work: work,
                                  onTap: () {
                                    Navigator.push(
                                      context,
                                      MaterialPageRoute(
                                        builder: (_) => DetailScreen(workId: work.id),
                                      ),
                                    );
                                  },
                                );
                              },
                            ),
                        ],
                      ),
                    ),
        ),
      ),
    );
  }
}
