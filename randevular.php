<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli(); $pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['durumid'])) { $pdo->prepare("UPDATE randevular SET durum=? WHERE id=?")->execute([$_POST['durum'],(int)$_POST['durumid']]); }
    else { $pdo->prepare("INSERT INTO randevular (ad_soyad,telefon,plaka,tarih,saat,notlar) VALUES (?,?,?,?,?,?)")
        ->execute([$_POST['ad'],$_POST['tel'],plaka_temizle($_POST['plaka']??''),$_POST['tarih'],$_POST['saat'],$_POST['not']??'']); }
    header('Location: randevular.php'); exit;
}
$rows = $pdo->query("SELECT * FROM randevular ORDER BY tarih DESC, saat DESC LIMIT 100")->fetchAll();
$title='Randevular'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">📅 Randevular</h1>
<p class="text-sm mb-3">Dışa açık link: <code class="bg-white px-2 py-1 rounded"><?= BASE_URL ?>/randevu-al.php</code> — müşteri buradan alır, onay beklemeden düşer.</p>
<div class="grid md:grid-cols-3 gap-4">
<form method="post" class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">+ Manuel randevu</h2>
<input name="ad" required placeholder="Ad Soyad" class="border rounded-lg p-2 w-full mb-2">
<input name="tel" required placeholder="Telefon" class="border rounded-lg p-2 w-full mb-2">
<input name="plaka" placeholder="Plaka" class="border rounded-lg p-2 w-full mb-2">
<div class="flex gap-2 mb-2"><input name="tarih" type="date" required value="<?= date('Y-m-d') ?>" class="border rounded-lg p-2 w-full"><input name="saat" type="time" required class="border rounded-lg p-2 w-full"></div>
<input name="not" placeholder="Not" class="border rounded-lg p-2 w-full mb-2">
<button class="bg-sky-600 text-white w-full py-2 rounded-lg font-bold">Kaydet</button></form>
<div class="md:col-span-2 bg-white rounded-xl shadow p-4">
<table class="w-full text-sm"><tr class="text-slate-500 text-left"><th>Tarih</th><th>Müşteri</th><th>Durum</th><th></th></tr>
<?php foreach($rows as $r): ?><tr class="border-b">
<td class="py-1 font-bold"><?= e($r['tarih']) ?> <?= substr(e($r['saat']),0,5) ?></td>
<td><?= e($r['ad_soyad']) ?><br><span class="text-xs text-slate-500"><?= e($r['telefon']) ?> · <?= e($r['plaka']) ?></span></td>
<td><?= e($r['durum']) ?></td>
<td><form method="post" class="flex gap-1"><input type="hidden" name="durumid" value="<?= $r['id'] ?>">
<select name="durum" class="border rounded text-xs p-1"><option>bekliyor</option><option>tamamlandi</option><option>iptal</option></select>
<button class="text-xs bg-slate-800 text-white px-2 rounded">ok</button></form></td></tr>
<?php endforeach; ?></table></div></div>
<?php require __DIR__.'/inc/footer.php'; ?>
