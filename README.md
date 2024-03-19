# Laravel Starter Code - JTI Polinema

## Panduan
    1. Clone repo ini
    2. Jalankan `composer install`
    3. Copy file `.env.example` menjadi `.env`
    4. Jalankan `php artisan key:generate`
    5. Buat database baru dan pastikan menggunakan collation utf8mb4_unicode_ci
    6. Sesuaikan konfigurasi database di file `.env`
    7. Jalankan `php artisan migrate --seed`
    8. Jalankan `php artisan serve`
    9. Buka browser dan akses `http://localhost:8000`

## Flow Add Menu/Submenu
    1. Insert menu di DB atau setting > menu (login as superadmin)
    2. Set hak akses menu
    3. Buat MVC yg dibutuhkan. Viewpath samakan dengan value di DB. Pastikan diakhir ada tanda titik (.) --> jika lupa, maka akan error view not found. Sesuaikan dengan path view.
    4. Set route di `web_<user>.php` sesuai kebutuhan.

**Catatan:** perlu logout, karena hak akses menu disimpan pada sessions

## php artisan command

Clear cache

    php artisan cache:clear

Route clear

    php artisan route:clear

Route list

    php artisan route:list


