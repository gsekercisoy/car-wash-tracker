<?php
// Tek tık kurulum: DB + tablolar + demo verisi
require_once __DIR__ . '/inc/db.php';
$pdo = db();
$pdo->exec(file_get_contents(__DIR__ . '/sql/schema.sql'));

// Demo kullanıcı
$var = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ((int)$var === 0) {
    $h = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (ad,kullanici_adi,sifre,rol) VALUES (?,?,?,?)")
        ->execute(['Yönetici', 'admin', $h, 'admin']);
    $h2 = password_hash('personel123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (ad,kullanici_adi,sifre,rol) VALUES (?,?,?,?)")
        ->execute(['Personel', 'personel', $h2, 'personel']);
    $pdo->exec("INSERT INTO hizmetler (baslik,fiyat) VALUES
      ('İç + Dış Yıkama',400),('Detaylı İç Temizlik',1200),('Pasta Cila',5000),('Seramik Kaplama',15000),('Motor Yıkama',600)");
    $pdo->exec("INSERT INTO urunler (baslik,fiyat,stok,min_stok) VALUES
      ('Cam Suyu 5L',250,40,10),('Cila 500ml',600,15,5),('Koku Kartı',50,200,30)");
    $pdo->exec("INSERT INTO cariler (tip,unvan,telefon) VALUES ('musteri','Filomobil A.Ş.','05320000000')");
}
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Kurulum tamam</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded-2xl shadow max-w-md text-center">
<h1 class="text-2xl font-bold mb-3">✅ Kurulum tamam</h1>
<p class="mb-4">Giriş bilgileri:<br><b>admin / admin123</b><br>personel / personel123</p>
<a href="login.php" class="bg-sky-600 text-white px-5 py-2 rounded-lg font-bold">Panele giriş yap</a>
</div></body></html>
