import 'package:flutter/material.dart';
import '../config/app_config.dart';
import '../models/work_model.dart';
import '../services/api_service.dart';
import '../widgets/state_widgets.dart';
import 'detail_screen.dart';

class AuthorWorksScreen extends StatefulWidget {
  const AuthorWorksScreen({super.key});

  @override
  State<AuthorWorksScreen> createState() => _AuthorWorksScreenState();
}

class _AuthorWorksScreenState extends State<AuthorWorksScreen> {
  bool _isLoading = true;
  String? _errorMessage;
  List<WorkModel> _authorWorks = [];

  @override
  void initState() {
    super.initState();
    _loadAuthorWorks();
  }

  Future<void> _loadAuthorWorks() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await ApiService.getAuthorWorks();

    if (mounted) {
      setState(() {
        _isLoading = false;
        if (result.isSuccess) {
          _authorWorks = result.data ?? [];
        } else {
          _errorMessage = result.message;
        }
      });
    }
  }

  Color _getStatusColor(String? status) {
    switch (status) {
      case 'published':
        return Colors.green;
      case 'in_review':
        return const Color(AppConfig.tealPrimaryValue);
      case 'revision':
        return Colors.orange;
      case 'waiting_payment':
        return Colors.blue;
      case 'rejected':
        return Colors.red;
      case 'submitted':
        return Colors.purple;
      default:
        return Colors.grey;
    }
  }

  String _getStatusLabel(String? status) {
    switch (status) {
      case 'published':
        return 'Terbit (Published)';
      case 'in_review':
        return 'Sedang Ditelaah';
      case 'revision':
        return 'Menunggu Revisi';
      case 'waiting_payment':
        return 'Menunggu Pembayaran';
      case 'rejected':
        return 'Ditolak';
      case 'submitted':
        return 'Terkirim (Antre)';
      case 'draft':
        return 'Draf';
      default:
        return status ?? 'Draf';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(AppConfig.bgLightValue),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        title: const Text(
          'Karya Ilmiah Saya',
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
        child: RefreshIndicator(
          color: const Color(AppConfig.tealPrimaryValue),
          onRefresh: _loadAuthorWorks,
          child: _isLoading
              ? const LoadingStateWidget(message: 'Memuat naskah karya Anda...')
              : _errorMessage != null && _authorWorks.isEmpty
                  ? ErrorStateWidget(
                      errorMessage: _errorMessage!,
                      onRetry: _loadAuthorWorks,
                    )
                  : _authorWorks.isEmpty
                      ? const EmptyStateWidget(
                          icon: Icons.post_add_rounded,
                          title: 'Belum Ada Karya yang Diunggah',
                          message: 'Anda belum memiliki naskah ilmiah yang diajukan ke sistem repositori Lentera.',
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.all(16),
                          itemCount: _authorWorks.length,
                          itemBuilder: (context, index) {
                            final work = _authorWorks[index];
                            final statusColor = _getStatusColor(work.status);
                            final statusLabel = _getStatusLabel(work.status);

                            return Card(
                              elevation: 0,
                              margin: const EdgeInsets.only(bottom: 12),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(14),
                                side: BorderSide(color: Colors.grey.shade200),
                              ),
                              color: Colors.white,
                              child: InkWell(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                      builder: (_) => DetailScreen(workId: work.id),
                                    ),
                                  );
                                },
                                borderRadius: BorderRadius.circular(14),
                                child: Padding(
                                  padding: const EdgeInsets.all(16),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Container(
                                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                            decoration: BoxDecoration(
                                              color: statusColor.withOpacity(0.12),
                                              borderRadius: BorderRadius.circular(6),
                                            ),
                                            child: Text(
                                              statusLabel,
                                              style: TextStyle(
                                                fontSize: 11,
                                                fontWeight: FontWeight.bold,
                                                color: statusColor,
                                              ),
                                            ),
                                          ),
                                          const Spacer(),
                                          Text(
                                            'Tahun ${work.year}',
                                            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 10),
                                      Text(
                                        work.title,
                                        style: const TextStyle(
                                          fontSize: 15,
                                          fontWeight: FontWeight.bold,
                                          color: Color(AppConfig.tealDarkValue),
                                          height: 1.3,
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Row(
                                        children: [
                                          Icon(Icons.category_rounded, size: 14, color: Colors.grey.shade600),
                                          const SizedBox(width: 4),
                                          Text(
                                            '${work.workType} · ${work.studyProgram}',
                                            style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            );
                          },
                        ),
        ),
      ),
    );
  }
}
