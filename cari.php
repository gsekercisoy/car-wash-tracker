<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli(); $pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (($_POST['islem']??'')==='cari') $pdo->prepare("INSERT INTO cariler (tip,unvan,telefon) VALUES (?,?,?)")->execute([$_POST['tip'],$_POST['unvan'],$_POST['tel']]);
    if (($_POST['islem']??'')==='hareket') {
        $pdo->prepare("INSERT INTO cari_hareketler (cari_id,yon,tutar,aciklama) VALUES (?,?,?,?)")->execute([(int)$_POST['cari'],$_POST['yon'],$_POST['tutar'],$_POST['acik']]);
        $pdo->exec("UPDATE cariler SET bakiye=(SELECT COALESCE(SUM(CASE WHEN yon='borc' THEN tutar ELSE -tutar END),0) FROM cari_hareketler WHERE cari_id=".(int)$_POST['cari'].") WHERE id=".(int)$_POST['cari']);
    }
    header('Location: cari.php'); exit;
}
$cariler = $pdo->query("SELECT * FROM cariler ORDER BY id DESC")->fetchAll();
$hareketler = $pdo->query("SELECT h.*, c.unvan FROM cari_hareketler h JOIN cariler c ON c.id=h.cari_id ORDER BY h.id DESC LIMIT 100")->fetchAll();
$title='Cari'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">💳 Cari Takip</h1>
<div class="grid md:grid-cols-3 gap-4">
<div class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">+ Cari aç</h2>
<form method="post" class="flex flex-col gap-2"><input type="hidden" name="islem" value="cari">
<select name="tip" class="border rounded-lg p-2"><option value="musteri">Müşteri</option><option value="tedarikci">Tedarikçi</option><option value="personel">Personel</option></select>
<input name="unvan" required placeholder="Unvan" class="border rounded-lg p-2"><input name="tel" placeholder="Telefon" class="border rounded-lg p-2">
<button class="bg-sky-600 text-white py-2 rounded-lg font-bold">Kaydet</button></form>
<h2 class="font-bold mt-4 mb-2">+ Hareket</h2>
<form method="post" class="flex flex-col gap-2"><input type="hidden" name="islem" value="hareket">
<select name="cari" class="border rounded-lg p-2"><?php foreach($cariler as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['unvan']) ?> (<?= para($c['bakiye']) ?>)</option><?php endforeach; ?></select>
<select name="yon" class="border rounded-lg p-2"><option value="borc">Borç (alacak)</option><option value="tahsilat">Tahsilat</option></select>
<input name="tutar" type="number" step="0.01" required placeholder="Tutar" class="border rounded-lg p-2">
<input name="acik" placeholder="Açıklama" class="border rounded-lg p-2">
<button class="bg-emerald-600 text-white py-2 rounded-lg font-bold">İşle</button></form>
</div>
<div class="md:col-span-2 space-y-4">
<div class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">Cariler</h2>
<table class="w-full text-sm"><?php foreach($cariler as $c): ?><tr class="border-b"><td class="py-1"><?= e($c['unvan']) ?> <span class="text-xs text-slate-500">(<?= e($c['tip']) ?>)</span></td><td class="text-right font-bold <?= $c['bakiye']>0?'text-red-600':'text-green-600' ?>"><?= para($c['bakiye']) ?></td></tr><?php endforeach; ?></table></div>
<div class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">Son hareketler <button onclick="window.print()" class="text-xs bg-slate-800 text-white px-2 py-1 rounded ml-2">PDF / Yazdır</button></h2>
<table class="w-full text-sm"><tr class="text-slate-500 text-left"><th>Tarih</th><th>Cari</th><th>Yön</th><th class="text-right">Tutar</th></tr>
<?php foreach($hareketler as $h): ?><tr class="border-b"><td class="py-1 text-xs"><?= e($h['created_at']) ?></td><td><?= e($h['unvan']) ?><br><span class="text-xs text-slate-500"><?= e($h['aciklama']) ?></span></td><td><?= e($h['yon']) ?></td><td class="text-right font-bold"><?= para($h['tutar']) ?></td></tr><?php endforeach; ?></table></div>
</div></div>
<?php require __DIR__.'/inc/footer.php'; ?>
