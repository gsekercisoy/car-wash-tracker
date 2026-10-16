<?php require_once __DIR__.'/inc/db.php';
$site = 'CarMek Klon — Oto Yıkama Programı'; $isletme = ayar('isletme_adi','Parla Oto Yıkama'); $renk = ayar('renk','#0ea5e9');
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($site) ?></title><script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css"></head>
<body class="bg-white text-slate-800">
<header class="hero-grad text-white">
<nav class="max-w-6xl mx-auto flex justify-between items-center p-4">
<b class="text-xl">🚗 Oto Yıkama Takip</b>
<div class="hidden md:flex gap-4 text-sm"><a href="#nasil-calisir">Nasıl Çalışır?</a><a href="#ozellikler">Özellikler</a><a href="#fiyat">Fiyat</a><a href="#sss">SSS</a><a href="#iletisim">İletişim</a></div>
<div class="flex gap-2"><a href="randevu-al.php" class="bg-white text-slate-900 px-4 py-2 rounded-lg text-sm font-bold">Randevu Al</a>
<a href="login.php" class="px-4 py-2 rounded-lg text-sm font-bold" style="background:<?= e($renk) ?>">Panele Gir</a></div></nav>
<div class="max-w-6xl mx-auto p-8 md:p-16 text-center">
<h1 class="text-3xl md:text-5xl font-extrabold mb-4">Plakayı okutun, gerisini sistem takip etsin.</h1>
<p class="text-slate-200 max-w-2xl mx-auto mb-6">Kabulden teslimata her adım kayıt altında: müşteri SMS ile bilgilendirilir, ciro-stok-personel tek panelde toplanır. PHP + MySQL, XAMPP'ta çalışır.</p>
<div class="flex justify-center gap-3"><a href="login.php" class="bg-white text-slate-900 px-6 py-3 rounded-xl font-bold">Ücretsiz deneyin (admin/admin123)</a>
<a href="randevu-al.php" class="border border-white px-6 py-3 rounded-xl font-bold">Online randevu →</a></div>
<div class="flex justify-center gap-6 mt-8 text-sm text-slate-200"><span>⭐ 5,0 kullanıcı puanı (demo)</span><span>📱 iOS • Android • Web</span><span>💬 WhatsApp + SMS</span></div>
</div></header>

<section id="nasil-calisir" class="max-w-6xl mx-auto p-8">
<h2 class="text-2xl font-extrabold mb-1">Nasıl çalışır?</h2>
<p class="text-slate-500 mb-4">Beş adım — panelde tek tuşla ilerler.</p>
<div class="grid md:grid-cols-5 gap-3">
<?php $adim=[['Kabul','Plaka + telefon ile araç kartı açılır, SMS gider'],['Devam','Aracı işleme al'],['Bitti','İşlemi bitir'],['Çağrıldı','"Aracınız hazır" SMS gider'],['Teslim','Ödemeyi al, kodu doğrula, teslim et']];
foreach($adim as $i=>$a): ?><div class="border rounded-xl p-4"><div class="text-xs font-bold text-sky-600"><?= $i+1 ?>. ADIM</div><b><?= $a[0] ?></b><p class="text-sm text-slate-500"><?= $a[1] ?></p></div><?php endforeach; ?>
</div></section>

<section id="ozellikler" class="bg-slate-50"><div class="max-w-6xl mx-auto p-8">
<h2 class="text-2xl font-extrabold mb-4">Özellikler</h2>
<div class="grid md:grid-cols-4 gap-3 text-sm">
<?php $oz=['Araç & müşteri kaydı'=>'Plaka yazarken bulur, geçmiş işlemler gelir','Plaka okuma'=>'Fotoğraftan okuma (demo) + elle giriş','Hizmet & ürün'=>'Satıra indirim, stoktan otomatik düşüm','Personel atama'=>'Kim hangi işi yaptı, performans raporu','Cari takip'=>'Açık hesap, borç-tahsilat, ekstre','Kasa & gider'=>'Günlük ciro, tarih aralıklı rapor','Online randevu'=>'Onaysız takvime düşer, hatırlatma gider','SMS log'=>'Kabul + hazır mesajları, toplu kampanya altyapısı','Kullanıcı & yetki'=>'Admin tam, personel sade ekran','Rapor & Excel'=>'Ciro, gider, personel, CSV indir','Notlar'=>'Hızlı not, anında arama','Fiş yazdırma'=>'Teslim ekranında tek tuşla yazdır'];
foreach($oz as $k=>$v): ?><div class="bg-white rounded-xl p-4 shadow-sm"><b><?= $k ?></b><p class="text-slate-500"><?= $v ?></p></div><?php endforeach; ?>
</div></div></section>

<section id="fiyat" class="max-w-6xl mx-auto p-8 text-center">
<h2 class="text-2xl font-extrabold">Tek plan, aylık lisans — bu klonda ücretsiz</h2>
<p class="text-slate-500 mb-4">Sınırsız kullanıcı • Tüm modüller açık • Veriler sizde (localhost)</p>
<a href="login.php" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold">Hemen panele gir →</a></section>

<section id="sss" class="bg-slate-50"><div class="max-w-3xl mx-auto p-8">
<h2 class="text-2xl font-extrabold mb-4">Sık sorulan sorular</h2>
<details class="bg-white rounded-xl p-4 mb-2 shadow-sm"><summary class="font-bold">Hangi cihazlarda çalışır?</summary><p class="text-sm text-slate-500">İnternete bağlı her cihaz: telefon, tablet, PC. Responsive panel.</p></details>
<details class="bg-white rounded-xl p-4 mb-2 shadow-sm"><summary class="font-bold">Plaka nasıl okunuyor?</summary><p class="text-sm text-slate-500">Demoda simüle edilir; üretimde Plate Recognizer API anahtarı <code>araclar.php → plakaOku()</code> fonksiyonuna bağlanır.</p></details>
<details class="bg-white rounded-xl p-4 mb-2 shadow-sm"><summary class="font-bold">SMS gerçekten gidiyor mu?</summary><p class="text-sm text-slate-500">Hayır — <code>sms_log</code> tablosuna düşer, SMS sayfasında görülür. NetGSM/Twilio entegrasyonu <code>inc/db.php → sms_gonder()</code> içine eklenir.</p></details>
<details class="bg-white rounded-xl p-4 mb-2 shadow-sm"><summary class="font-bold">Kurulum nasıl?</summary><p class="text-sm text-slate-500">Aşağıdaki 3 adıma bakın. XAMPP + <code>kurulum.php</code> yeterli.</p></details>
</div></div></section>

<section id="iletisim" class="max-w-6xl mx-auto p-8">
<h2 class="text-2xl font-extrabold mb-4">Kurulum (XAMPP)</h2>
<ol class="bg-slate-900 text-white rounded-2xl p-6 text-sm space-y-2 list-decimal list-inside">
<li>Bu klasörü <code>C:\xampp\htdocs\oto-yikama</code> olarak kopyalayın (klasör adı birebir bu olmalı).</li>
<li>XAMPP'ta <b>Apache + MySQL</b>'i başlatın.</li>
<li>Tarayıcıda <code>http://localhost/oto-yikama/kurulum.php</code> adresini açın.</li>
<li><code>http://localhost/oto-yikama/login.php</code> → <b>admin / admin123</b> ile girin.</li>
</ol>
<p class="text-sm text-slate-500 mt-3">Farklı klasör adı kullanırsanız <code>inc/db.php</code> içindeki <code>BASE_URL</code> sabitini güncelleyin.</p>
</section>
<footer class="bg-slate-900 text-slate-300 text-center text-sm p-6">© 2026 <?= e($isletme) ?> — Eğitim amaçlı hazırlanmıştır. <a class="underline" href="login.php">Panel</a> · <a class="underline" href="randevu-al.php">Randevu</a></footer>
</body></html>
