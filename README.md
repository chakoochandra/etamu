# eTamu

Aplikasi buku tamu digital. Silakan dipergunakan dan dimodifikasi untuk penggunaan pribadi / kantor / satker Anda. Dilarang memperjualbelikan aplikasi ini baik aslinya maupun hasil modifikasi. 

Fitur:
- Capture wajah
- Notifikasi info tamu by whatsapp (bisa didisable)
    Notifikasi dikirim ke
    -- Pejabat yang dituju oleh tamu
    -- Tamu
    -- operator (opsional)
- Riwayat tamu
- Riwayat pengiriman notifikasi
- Daftar pejabat yang dapat ditemui oleh tamu

Ingin bertanya? Hubungi saya di [whatsapp (chat only)](https://s.dialogwa.id/65f3b222ef52bc3780613e59_demo) 

-------------------------------

## INSTALASI


###  FILE INDEX.PHP
Duplikasi file index.example.php, file hasil duplikat rename menjadi index.php


###  MODIFIKASI DATABASE.PHP
1. Masuk ke folder application\config\
2. Duplikasi file database.example.php, file hasil duplikat rename menjadi database.php
3. Set hostname, database, username, dan password database


###  SQL
File: sql\tamuku.sql

Buat database dengan cara menjalankan tamuku.sql


###  MODIFIKASI KONFIGURASI DATABASE
Table: configs

```
* APP_VERSION
* APP_NAME 
* APP_SHORT_NAME
* SATKER_NAME
* SATKER_ADDRESS
* DIALOGWA_API_URL --string. url api dialogwa.id
* DIALOGWA_TOKEN --string. token dialogwa.id
* DIALOGWA_SESSION --string. sesi online dialogwa.id
* WA_TEST_TARGET --string. nomor WA untuk tes penerima notifikasi
* SEND_NOTIFICATION --tinyint. 0: tidak kirim notifikasi, 1: kirim notifikasi 
```


###  DISABLE / ENABLE NOTIFIKASI
Untuk mendisable notifikasi whatsapp, pada table configs, set value SEND_NOTIFICATION menjadi 0


###  DEPLOY PRODUCTION
> [!CAUTION]
> Pada mode Production, notifikasi akan dikirimkan ke nomor persons yang dituju oleh tamu juga ke nomor tamu yang bersangkutan.
> 
> 
> Setelah aplikasi siap untuk digunakan LIVE, berikut yang harus dilakukan :
> 1. Pada folder project, buka file index.php
> 2. Pada baris 57, ubah :
> 
> ```
> define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
> ```
> 
> menjadi
> 
> ```
> define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'production');
> ```
