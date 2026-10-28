import 'package:flutter/material.dart';
import '../config/app_config.dart';
import '../models/work_model.dart';
import '../models/faculty_model.dart';
import '../services/api_service.dart';
import '../widgets/state_widgets.dart';
import 'detail_screen.dart';

class SearchScreen extends StatefulWidget {
  final String? initialFacultyCode;
  const SearchScreen({super.key, this.initialFacultyCode});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final TextEditingController _searchController = TextEditingController();
  bool _isLoading = false;
  String? _errorMessage;
  List<WorkModel> _results = [];
  List<FacultyModel> _faculties = [];
  String _selectedFaculty = '';

  @override
  void initState() {
    super.initState();
    _selectedFaculty = widget.initialFacultyCode ?? '';
    _loadFacultiesAndSearch();
  }

  Future<void> _loadFacultiesAndSearch() async {
    final facResult = await ApiService.getFaculties();
    if (mounted && facResult.isSuccess) {
      setState(() {
        _faculties = facResult.data ?? [];
      });
    }
    _performSearch();
  }

  Future<void> _performSearch() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final query = _searchController.text.trim();
    final result = await ApiService.searchWorks(
      query: query,
      facultyCode: _selectedFaculty,
      limit: 20,
    );

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result.isSuccess) {
          _results = result.data ?? [];
        } else {
          _errorMessage = result.message;
        }
      });
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(AppConfig.bgLightValue),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        title: const Text(
          'Katalog & Pencarian',
          style: TextStyle(
            color: Color(AppConfig.tealDarkValue),
            fontWeight: FontWeight.bold,
            fontSize: 17,
          ),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Color(AppConfig.tealDarkValue), size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SafeArea(
        child: Column(
          children: [
            // Panel Kotak Pencarian & Filter
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              color: Colors.white,
              child: Column(
                children: [
                  TextField(
                    controller: _searchController,
                    textInputAction: TextInputAction.search,
                    onSubmitted: (_) => _performSearch(),
                    decoration: InputDecoration(
                      hintText: 'Cari judul karya, penulis, atau kata kunci...',
                      hintStyle: const TextStyle(fontSize: 13, color: Colors.blueGrey),
                      prefixIcon: const Icon(Icons.search_rounded, color: Color(AppConfig.tealPrimaryValue)),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear_rounded, size: 18),
                              onPressed: () {
                                _searchController.clear();
                                _performSearch();
                              },
                            )
                          : null,
                      filled: true,
                      fillColor: const Color(AppConfig.bgLightValue),
                      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide(color: Colors.grey.shade300),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: Color(AppConfig.tealPrimaryValue), width: 1.5),
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  // Filter Fakultas
                  Row(
                    children: [
                      const Icon(Icons.filter_list_rounded, size: 18, color: Colors.blueGrey),
                      const SizedBox(width: 8),
                      Expanded(
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<String>(
                            value: _selectedFaculty.isEmpty ? '' : _selectedFaculty,
                            isDense: true,
                            isExpanded: true,
                            items: [
                              const DropdownMenuItem(value: '', child: Text('Semua Fakultas (UNIRA)', style: TextStyle(fontSize: 13))),
                              ..._faculties.map((f) => DropdownMenuItem(
                                    value: f.code,
                                    child: Text('${f.code} - ${f.name}', style: const TextStyle(fontSize: 13)),
                                  )),
                            ],
                            onChanged: (val) {
                              setState(() {
                                _selectedFaculty = val ?? '';
                              });
                              _performSearch();
                            },
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // Daftar Hasil
            Expanded(
              child: _isLoading
                  ? const LoadingStateWidget(message: 'Mencari naskah ilmiah...')
                  : _errorMessage != null && _results.isEmpty
                      ? ErrorStateWidget(
                          errorMessage: _errorMessage!,
                          onRetry: _performSearch,
                        )
                      : _results.isEmpty
                          ? const EmptyStateWidget(
                              title: 'Naskah Tidak Ditemukan',
                              message: 'Coba ubah kata kunci pencarian Anda atau pilih fakultas yang berbeda.',
                            )
                          : ListView.builder(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                              itemCount: _results.length,
                              itemBuilder: (context, index) {
                                final work = _results[index];
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
            ),
          ],
        ),
      ),
    );
  }
}
