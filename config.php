<?php
// Konfigurasi aplikasi Data Mahasiswa (PHP + Google Sheets via Apps Script)
return [
    // URL Web App hasil Deploy Apps Script (berakhiran /exec)
    'script_url'  => 'https://script.google.com/macros/s/AKfycbxnAaSx9JopfnL29Jqy3DiKvhtlH-QBMq3cWhDZrzqPjT76lc0PBQl-PprMA2gbN-CUVg/exec',
    // Nilai ambang kelulusan
    'batas_lulus' => 80,
    // Set false HANYA jika di XAMPP/localhost muncul error SSL certificate
    'verify_ssl'  => true,
];
