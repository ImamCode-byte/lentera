import 'package:flutter_test/flutter_test.dart';
import 'package:lentera_mobile/main.dart';

void main() {
  testWidgets('Verifikasi inisialisasi awal aplikasi mobile Lentera', (WidgetTester tester) async {
    // Bangun aplikasi Lentera
    await tester.pumpWidget(const LenteraApp());

    // Verifikasi judul dan elemen utama beranda terpasang
    expect(find.text('Lentera'), findsWidgets);
    expect(find.text('UNIVERSITAS MADURA'), findsWidgets);
  });
}
