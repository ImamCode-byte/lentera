import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../config/app_config.dart';
import '../models/work_model.dart';
import '../services/api_service.dart';
import '../widgets/state_widgets.dart';

class DetailScreen extends StatefulWidget {
  final int workId;
  const DetailScreen({super.key, required this.workId});

  @override
  State<DetailScreen> createState() => _DetailScreenState();
}

class _DetailScreenState extends State<DetailScreen> {
  bool _isLoading = true;
  String? _errorMessage;
  WorkModel? _work;
  String _selectedCitationStyle = 'apa';

  @override
  void initState() {
    super.initState();
    _loadWorkDetail();
  }

  Future<void> _loadWorkDetail() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getWorkDetail(widget.workId);

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result.isSuccess) {
          _work = result.data;
        } else {
          _errorMessage = result.message;
        }
      });
    }
  }

  void _copyToClipboard(String text, String formatName) {
    Clipboard.setData(ClipboardData(text: text));
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Format sitasi $formatName berhasil disalin ke papan klip.'),
        backgroundColor: const Color(AppConfig.tealPrimaryValue),
        duration: const Duration(seconds: 2),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(AppConfig.bgLightValue),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        title: const Text(
          'Detail Naskah Ilmiah',
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
        child: _isLoading
            ? const LoadingStateWidget(message: 'Memuat detail naskah...')
            : _errorMessage != null || _work == null
                ? ErrorStateWidget(
                    errorMessage: _errorMessage ?? 'Data tidak tersedia.',
                    onRetry: _loadWorkDetail,
                  )
                : SingleChildScrollView(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Tag Informasi
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: const Color(AppConfig.goldAccentValue).withOpacity(0.12),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                _work!.facultyCode.isNotEmpty ? _work!.facultyCode : 'UNIRA',
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: Color(AppConfig.goldAccentValue),
                                ),
                              ),
                            ),
                            const SizedBox(width: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: const Color(AppConfig.tealPrimaryValue).withOpacity(0.08),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                _work!.workType,
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: Color(AppConfig.tealPrimaryValue),
                                ),
                              ),
                            ),
                            const Spacer(),
                            Text(
                              'Tahun ${_work!.year}',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                                color: Colors.grey.shade600,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 14),

                        // Judul Karya
                        Text(
                          _work!.title,
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            color: Color(AppConfig.tealDarkValue),
                            height: 1.35,
                          ),
                        ),
                        const SizedBox(height: 16),

                        // Kartu Metadata Penulis & Prodi
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: BorderSide(color: Colors.grey.shade200),
                          ),
                          child: Column(
                            children: [
                              _buildMetaRow(Icons.person_rounded, 'Penulis', _work!.authorName),
                              if (_work!.authorNim != null && _work!.authorNim!.isNotEmpty)
                                _buildMetaRow(Icons.badge_rounded, 'NIM/NIP', _work!.authorNim!),
                              if (_work!.supervisor != null && _work!.supervisor!.isNotEmpty)
                                _buildMetaRow(Icons.school_rounded, 'Pembimbing', _work!.supervisor!),
                              _buildMetaRow(Icons.account_tree_rounded, 'Program Studi', _work!.studyProgram),
                              _buildMetaRow(Icons.apartment_rounded, 'Fakultas', _work!.facultyName),
                              _buildMetaRow(Icons.remove_red_eye_rounded, 'Keterbacaan', '${_work!.viewCount} kali dibaca · ${_work!.downloadCount} kali diunduh'),
                            ],
                          ),
                        ),
                        const SizedBox(height: 20),

                        // Abstrak
                        const Text(
                          'Abstrak Naskah',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: Color(AppConfig.tealDarkValue),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: BorderSide(color: Colors.grey.shade200),
                          ),
                          child: Text(
                            _work!.abstractText,
                            textAlign: TextAlign.justify,
                            style: const TextStyle(
                              fontSize: 13,
                              color: Colors.blueGrey,
                              height: 1.6,
                            ),
                          ),
                        ),
                        const SizedBox(height: 20),

                        // Generator Sitasi Otomatis
                        if (_work!.citations != null && _work!.citations!.isNotEmpty) ...[
                          const Text(
                            'Sitasi Karya Ilmiah',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Color(AppConfig.tealDarkValue),
                            ),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(12),
                              border: BorderSide(color: Colors.grey.shade200),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    _buildCitationChip('apa', 'APA 7th'),
                                    const SizedBox(width: 8),
                                    _buildCitationChip('ieee', 'IEEE'),
                                    const SizedBox(width: 8),
                                    _buildCitationChip('harvard', 'Harvard'),
                                  ],
                                ),
                                const SizedBox(height: 12),
                                Container(
                                  width: double.infinity,
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: const Color(AppConfig.bgLightValue),
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Text(
                                    _work!.citations![_selectedCitationStyle] ?? '',
                                    style: const TextStyle(fontSize: 12, color: Colors.blueGrey, height: 1.4),
                                  ),
                                ),
                                const SizedBox(height: 10),
                                Align(
                                  alignment: Alignment.centerRight,
                                  child: TextButton.icon(
                                    onPressed: () {
                                      final citationText = _work!.citations![_selectedCitationStyle] ?? '';
                                      _copyToClipboard(citationText, _selectedCitationStyle.toUpperCase());
                                    },
                                    icon: const Icon(Icons.copy_rounded, size: 16, color: Color(AppConfig.tealPrimaryValue)),
                                    label: const Text(
                                      'Salin Sitasi',
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: Color(AppConfig.tealPrimaryValue),
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 20),
                        ],

                        // Tombol Unduh PDF Naskah
                        if (_work!.hasFile)
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton.icon(
                              onPressed: () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text('Tautan unduhan PDF resmi tersedia melalui web portal Lentera.'),
                                    backgroundColor: Color(AppConfig.tealPrimaryValue),
                                  ),
                                );
                              },
                              icon: const Icon(Icons.download_rounded),
                              label: const Text('Unduh Berkas Full-Text (PDF)'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(AppConfig.tealPrimaryValue),
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(vertical: 14),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                ),
                              ),
                            ),
                          ),
                        const SizedBox(height: 30),
                      ],
                    ),
                  ),
      ),
    );
  }

  Widget _buildMetaRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: Colors.blueGrey),
          const SizedBox(width: 8),
          SizedBox(
            width: 100,
            child: Text(
              label,
              style: const TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.w500),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(fontSize: 12, color: Color(AppConfig.tealDarkValue), fontWeight: FontWeight.w600),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCitationChip(String styleKey, String label) {
    final isSelected = _selectedCitationStyle == styleKey;
    return ChoiceChip(
      label: Text(label),
      selected: isSelected,
      selectedColor: const Color(AppConfig.tealPrimaryValue),
      labelStyle: TextStyle(
        fontSize: 11,
        fontWeight: FontWeight.bold,
        color: isSelected ? Colors.white : Colors.blueGrey,
      ),
      backgroundColor: Colors.grey.shade100,
      onSelected: (val) {
        if (val) {
          setState(() {
            _selectedCitationStyle = styleKey;
          });
        }
      },
    );
  }
}
