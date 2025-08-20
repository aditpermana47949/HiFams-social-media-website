# HiFams - Website Media Sosial

HiFams adalah platform media sosial yang dirancang untuk memungkinkan pengguna terhubung dan berbagi informasi. Proyek ini dibangun dengan backend pada direktori `server` dan frontend pada direktori `client`.

## Instalasi dan Cara Menjalankan

Untuk menjalankan proyek ini di lingkungan lokal Anda, ikuti langkah-langkah di bawah ini.

### Prasyarat

* Node.js
* npm
* MongoDB

### 1. Clone Repositori

```bash
git clone [https://github.com/aditpermana47949/HiFams-social-media-website.git](https://github.com/aditpermana47949/HiFams-social-media-website.git)
cd HiFams-social-media-website
````

### 2\. Setup Backend

Pertama, masuk ke direktori server untuk menginstal dependensi dan melakukan konfigurasi.

```bash
cd server
npm install
```

Selanjutnya, buat file `.env` di dalam direktori `server` dan isi dengan konfigurasi yang diperlukan seperti koneksi database dan kunci rahasia. Contoh isi file `.env`:

```env
PORT=8000
MONGO_URL=mongodb_connection_string_anda
JWT_SECRET=kunci_rahasia_jwt_anda
```

### 3\. Setup Frontend

Kembali ke direktori utama, lalu masuk ke direktori client untuk menginstal dependensi.

```bash
cd ../client
npm install
```

### 4\. Jalankan Aplikasi

Buka dua terminal terpisah.

  * **Pada terminal pertama, jalankan server:**

<!-- end list -->

```bash
# Di dalam direktori /server
npm start
```

  * **Pada terminal kedua, jalankan client:**

<!-- end list -->

```bash
# Di dalam direktori /client
npm start
```
