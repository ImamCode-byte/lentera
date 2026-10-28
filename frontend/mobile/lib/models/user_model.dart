class UserModel {
  final int id;
  final String name;
  final String email;
  final String username;
  final String role;
  final String nimNip;
  final int prodiId;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.username,
    required this.role,
    required this.nimNip,
    required this.prodiId,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      name: json['name']?.toString() ?? '',
      email: json['email']?.toString() ?? '',
      username: json['username']?.toString() ?? '',
      role: json['role']?.toString() ?? 'author',
      nimNip: json['nim_nip']?.toString() ?? '',
      prodiId: json['prodi_id'] is int ? json['prodi_id'] : int.tryParse(json['prodi_id'].toString()) ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'username': username,
      'role': role,
      'nim_nip': nimNip,
      'prodi_id': prodiId,
    };
  }
}
