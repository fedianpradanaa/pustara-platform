<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Email pendaftaran

Setiap deployment Pustaka Nusantara dan GIA e-Library menggunakan database, konfigurasi `.env`, dan worker antrean masing-masing. Jalankan migrasi pada **masing-masing database** sebelum menggunakan formulir atau menu approval:

```bash
php artisan migrate --force
```

Di `.env` deployment terkait, isi SMTP dengan akun email bisnis yang sesuai. Contoh untuk Hostinger Email (bukan Titan atau Google Workspace):

```dotenv
APP_BRAND=pustara
APP_URL=https://domain-platform-anda
QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=info@pustakanusantara.id
MAIL_PASSWORD="isi-password-email-di-server"
MAIL_FROM_ADDRESS=info@pustakanusantara.id
MAIL_FROM_NAME="Pustaka Nusantara"
```

Untuk GIA, gunakan `APP_BRAND=gia`, URL platform GIA, akun `info@pustaka-gia.id`, dan `MAIL_FROM_NAME="GIA e-Library"`. Isi password secara pribadi di server, jangan simpan di Git. Pastikan `MAIL_URL` tidak menimpa pengaturan SMTP ini. Jika memakai jenis layanan lain, periksa parameter di panel penyedia. Hostinger menyediakan detail di **Emails > Manage > Connect Apps & Devices**; lihat [petunjuk Hostinger](https://www.hostinger.com/support/1575756-how-to-get-email-account-configuration-details-for-hostinger-email/).

Konfigurasikan process manager server untuk menjalankan dan menjaga worker di direktori **setiap deployment**:

```bash
php artisan queue:work database --sleep=3 --tries=5 --timeout=60
```

### Setelah mengubah konfigurasi `.env`

Jalankan perintah berikut di direktori deployment yang `.env`-nya diubah:

```bash
php artisan config:cache
```

Jika menggunakan Supervisor, restart program worker yang sesuai, lalu periksa statusnya:

```bash
supervisorctl restart gia-queue
# atau: supervisorctl restart pustara-queue
supervisorctl status
```

Nama program harus sesuai dengan konfigurasi Supervisor di server, dan worker harus kembali berstatus `RUNNING`. Ulangi langkah ini di kedua deployment jika `.env` keduanya diubah. Jangan simpan password `.env` di Git.

Antrean email memakai koneksi database aplikasi dan tabel `jobs`. Tanpa worker, email tidak akan terkirim. Email yang gagal dicoba ulang hingga lima kali; periksa `php artisan queue:failed` dan gunakan `php artisan queue:retry <id>` setelah masalah SMTP diperbaiki. Email verifikasi supplier/distributor juga menggunakan antrean. `APP_URL` harus benar agar tautan verifikasi mengarah ke platform yang sesuai.

Pendaftaran client lama tanpa email tetap dapat diproses, tetapi admin menerima peringatan bahwa email status tidak dikirim. Pemberitahuan pendaftaran client baru dikirim setelah data tersimpan; persetujuan awal oleh distributor belum merupakan persetujuan akhir admin.
Pendaftaran client belum memiliki tautan verifikasi email; email yang dikirim hanya pemberitahuan status pendaftaran. Migrasi juga menambahkan kolom `email` yang dapat bernilai kosong pada `tclient_temp`.

### Lupa password supplier dan distributor

Supplier dan distributor dapat memilih **Lupa password?** di halaman masuk. Tautan reset dikirim melalui antrean database yang sama; worker dan SMTP pada deployment terkait harus aktif. Tautan berlaku 60 menit dan hanya dapat digunakan sekali. Setelah mengubah password, pengguna masuk kembali melalui halaman login. Akun client tidak menggunakan alur reset platform ini.

Kode referensi client harus cocok dengan `noref` perusahaan bertipe distributor (`tcompany.type = 2`) pada database deployment terkait. Distributor menentukan kode unik saat mendaftar dan memberikannya kepada client.

## Mapping buku dan PO

Admin platform dapat membuat mapping melalui **Data > Mapping Buku ke Client**. Client harus sudah disetujui; buku yang ditarik atau sedang direvisi tidak dapat dipilih. Nomor PO dibuat otomatis, tanggal PO mengikuti tanggal saat mapping dibuat, dan setiap buku memiliki jumlah copy.

PO pembelian langsung berstatus Approved dan baru muncul di supplier setelah admin memilih **Approve Supplier** pada Data PO. Persentase supplier diisi admin (lebih dari 0 sampai 100). PO hibah langsung dikirim ke supplier dengan harga detail nol dan tidak memerlukan pelunasan. PO hibah lama yang masih menunggu persetujuan juga langsung dikirim saat disetujui; hibah lama yang sudah berstatus Approved dapat dikirim melalui **Kirim Hibah**. Data PO historis tidak diubah otomatis.

Migrasi flag buku harus dijalankan pada database deployment sebelum memakai fitur ini. Untuk integrasi aplikasi client, katalog penjualan baru harus memeriksa `tbook.is_available = 1` dan `tbook.is_editing = 0`, sementara akses baca buku yang sudah dimiliki tetap berdasarkan mapping, tanpa menyaring kedua flag tersebut.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
