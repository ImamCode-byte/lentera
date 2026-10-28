class WorkModel {
  final int id;
  final String title;
  final String abstractText;
  final int year;
  final String? supervisor;
  final String? status;
  final int viewCount;
  final int downloadCount;
  final String? publishedAt;
  final String authorName;
  final String? authorNim;
  final String studyProgram;
  final String facultyName;
  final String facultyCode;
  final String workType;
  final bool hasFile;
  final String? downloadUrl;
  final Map<String, String>? citations;

  WorkModel({
    required this.id,
    required this.title,
    required this.abstractText,
    required this.year,
    this.supervisor,
    this.status,
    required this.viewCount,
    required this.downloadCount,
    this.publishedAt,
    required this.authorName,
    this.authorNim,
    required this.studyProgram,
    required this.facultyName,
    required this.facultyCode,
    required this.workType,
    this.hasFile = false,
    this.downloadUrl,
    this.citations,
  });

  factory WorkModel.fromJson(Map<String, dynamic> json) {
    Map<String, String>? parsedCitations;
    if (json['citations'] is Map) {
      parsedCitations = (json['citations'] as Map).map(
        (key, value) => MapEntry(key.toString(), value.toString()),
      );
    }

    return WorkModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title']?.toString() ?? '',
      abstractText: json['abstract']?.toString() ?? '',
      year: json['year'] is int ? json['year'] : int.tryParse(json['year'].toString()) ?? 0,
      supervisor: json['supervisor']?.toString(),
      status: json['status']?.toString(),
      viewCount: json['view_count'] is int ? json['view_count'] : int.tryParse(json['view_count'].toString()) ?? 0,
      downloadCount: json['download_count'] is int ? json['download_count'] : int.tryParse(json['download_count'].toString()) ?? 0,
      publishedAt: json['published_at']?.toString(),
      authorName: json['author'] is Map ? (json['author']['name']?.toString() ?? '') : (json['author_name']?.toString() ?? ''),
      authorNim: json['author'] is Map ? json['author']['nim_nip']?.toString() : json['author_nim']?.toString(),
      studyProgram: json['study_program']?.toString() ?? json['prodi_name']?.toString() ?? '',
      facultyName: json['faculty'] is Map ? (json['faculty']['name']?.toString() ?? '') : (json['faculty_name']?.toString() ?? ''),
      facultyCode: json['faculty'] is Map ? (json['faculty']['code']?.toString() ?? '') : (json['faculty_code']?.toString() ?? ''),
      workType: json['work_type']?.toString() ?? json['work_type_name']?.toString() ?? '',
      hasFile: json['has_file'] == true || (json['download_url'] != null),
      downloadUrl: json['download_url']?.toString(),
      citations: parsedCitations,
    );
  }
}
