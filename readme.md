# REVISI SISTEM

## REVISI 1

-✅Laporan Transaksi SPP : export persiswa di ganti lihat saja samakan dengan Trnasaksi SPP

- ✅ done: Kelola Data Pemasukan dihilangkan untuk kelola data ini tambahkan pada laporan pemasukan secara otomatis
  per tanggal masuk (tambahkan grafik)
- ✅Kelola Data Pengeluaran: tambahkan pengajuan ke kepala sekolah dengan tombol validasi untuk persetujuan kepala sekolah
- ✅Dashboard di perkecil dan tampilkan Grafik pemasukan
- ✅Belum Lunas/Tunggak Muncul di Laporan dan Dashboard ketika di klik nama langusng ke detail spp siswa
- ✅Ganti button hapus dan ubh jadi icon pada kelola data siswa

## REVISI 2

- ✅ Halaman Dashboard
  -- tambahkan filter perkelas dan export sebagai excell
  -- perbaiki belum lunas, expired, dan pending tampil yatim piatu tidak tampil didashboard karna 0
- ✅ Halaman Transakasi
  -- buat 2.5 menit pada bayar otomatis jika sudah 2,5 menit maka expired status kembali belum lunas dan tombol bayar otomatis manual muncul kembali bisa di klik, tambahkan tombol lanjut jika belum 2,5 menit walaupun diclose waktu terus berjalan
- ✅ Laporan Transaksi SPP
  -- tombol export per kelas dan pertahun ganti dengan tombol Export didalamnya baru ada select dropdown perkelas dan pertahun
- ✅ tambahkan data dummy secara otomatis

JANGAN LUPA CONFIG MITRAND DIGANTI dan URL disesuaikan samakan dengan yang di .env
config/mitrand:
'finish_redirect_url' => env('MIDTRANS_FINISH_REDIRECT_URL', 'http://localhost:8000'),
'unfinish_redirect_url' => env('MIDTRANS_UNFINISH_REDIRECT_URL', 'http://localhost:8000'),
'error_redirect_url' => env('MIDTRANS_ERROR_REDIRECT_URL', 'http://localhost:8000'),

jalankan schedjule:
php artisan schedule:work
