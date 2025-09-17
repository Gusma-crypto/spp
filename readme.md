# REVISI SISTEM

-✅Laporan Transaksi SPP : export persiswa di ganti lihat saja samakan dengan Trnasaksi SPP

- ✅ done: Kelola Data Pemasukan dihilangkan untuk kelola data ini tambahkan pada laporan pemasukan secara otomatis
  per tanggal masuk (tambahkan grafik)
- ✅Kelola Data Pengeluaran: tambahkan pengajuan ke kepala sekolah dengan tombol validasi untuk persetujuan kepala sekolah
- ✅Dashboard di perkecil dan tampilkan Grafik pemasukan
- ✅Belum Lunas/Tunggak Muncul di Laporan dan Dashboard ketika di klik nama langusng ke detail spp siswa
- ✅Ganti button hapus dan ubh jadi icon pada kelola data siswa

JANGAN LUPA CONFIG MITRAND DIGANTI dan URL disesuaikan samakan dengan yang di .env
config/mitrand:
'finish_redirect_url' => env('MIDTRANS_FINISH_REDIRECT_URL', 'http://localhost:8000'),
'unfinish_redirect_url' => env('MIDTRANS_UNFINISH_REDIRECT_URL', 'http://localhost:8000'),
'error_redirect_url' => env('MIDTRANS_ERROR_REDIRECT_URL', 'http://localhost:8000'),

jalankan schedjule:
php artisan schedule:work
