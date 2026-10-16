<?php
require_once __DIR__.'/inc/db.php';
$ok = false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    db()->prepare("INSERT INTO randevular (ad_soyad,telefon,plaka,tarih,saat,notlar) VALUES (?,?,?,?,?,?)")
      ->execute([$_POST['ad'],$_POST['tel'],plaka_temizle($_POST['plaka']??''),$_POST['tarih'],$_POST['saat'],$_POST['not']??'']);
    $ok = true;
}
$site = ayar('isletme_adi','Parla Oto Yıkama');
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Online Randevu — <?= e($site) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
<h1 class="text-2xl font-extrabold mb-1">📅 <?= e($site) ?></h1>
<p class="text-sm text-slate-500 mb-4">Onay beklemeden takvime düşer. Hatırlatma otomatik gider.</p>
<?php if($ok): ?><div class="bg-green-100 text-green-800 p-3 rounded-xl font-bold mb-3">✅ Randevunuz alındı! Sizi bekliyoruz.</div><?php endif; ?>
<form method="post" class="space-y-2">
<input name="ad" required placeholder="Ad Soyad" class="border rounded-lg p-2 w-full">
<input name="tel" required placeholder="Telefon" class="border rounded-lg p-2 w-full">
<input name="plaka" placeholder="Plaka" class="border rounded-lg p-2 w-full uppercase">
<div class="flex gap-2"><input name="tarih" type="date" required min="<?= date('Y-m-d') ?>" class="border rounded-lg p-2 w-full"><input name="saat" type="time" required class="border rounded-lg p-2 w-full"></div>
<input name="not" placeholder="Not (opsiyonel)" class="border rounded-lg p-2 w-full">
<button class="bg-sky-600 text-white w-full py-2 rounded-lg font-bold">Randevuyu Oluştur</button></form>
<p class="text-center text-sm mt-3"><a href="index.php" class="text-sky-600">← Siteye dön</a></p>
</div></body></html>
