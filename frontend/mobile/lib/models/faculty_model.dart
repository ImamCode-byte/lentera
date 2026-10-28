class FacultyModel {
  final int id;
  final String code;
  final String name;
  final int worksCount;

  FacultyModel({
    required this.id,
    required this.code,
    required this.name,
    required this.worksCount,
  });

  factory FacultyModel.fromJson(Map<String, dynamic> json) {
    return FacultyModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      code: json['code']?.toString() ?? '',
      name: json['name']?.toString() ?? '',
      worksCount: json['works_count'] is int ? json['works_count'] : int.tryParse(json['works_count'].toString()) ?? 0,
    );
  }
}
