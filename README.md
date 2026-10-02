# Aplikasi Data Mahasiswa (PHP + Google Sheets via Apps Script)

## Persiapan
1. Buat Google Sheets dengan header baris 1: NIM | Nama Mahasiswa | Poin 1 | Poin 2 | Poin 3, lalu isi data mulai baris 2.
2. Buka Extensions > Apps Script, tempel isi `apps_script/Code.gs` (sesuaikan `SHEET_NAME`), lalu simpan.
3. Deploy > New deployment > jenis **Web app** > Execute as: **Me** > Who has access: **Anyone**. Salin URL Web App (berakhiran `/exec`).
4. Tempel URL tersebut pada `script_url` di `config.php`.
5. Jalankan di server PHP (XAMPP/hosting) dengan ekstensi cURL aktif, lalu buka `index.php`.
   Setiap mengubah Code.gs, lakukan Deploy > Manage deployments > Edit > New version agar perubahan berlaku.

## Fitur
- Tampil data + Rata-rata, Nilai Tertinggi, Nilai Terendah, Keterangan (LULUS jika rata-rata >= 80)
- Tambah, Ubah, Hapus data (terhubung langsung ke Google Sheets)
- Inovasi: sorting rata-rata, filter LULUS/BELUM LULUS, jumlah mahasiswa, rata-rata seluruh mahasiswa, pencarian, tampilan responsive (Bootstrap)
