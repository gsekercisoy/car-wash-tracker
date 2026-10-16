<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli(); $pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pdo->prepare("INSERT INTO kasa_hareketler (yon,baslik,tutar,yontem,personel,tarih) VALUES (?,?,?,?,?,?)")
        ->execute([$_POST['yon'],$_POST['baslik'],$_POST['tutar'],$_POST['yontem']??'nakit',$_SESSION['user']['ad']??'',$_POST['tarih']??date('Y-m-d')]);
    header('Location: kasa.php'); exit;
}
$t1 = $_GET['t1']??date('Y-m-01'); $t2 = $_GET['t2']??date('Y-m-d');
$s = $pdo->prepare("SELECT * FROM kasa_hareketler WHERE tarih BETWEEN ? AND ? ORDER BY tarih DESC, id DESC LIMIT 200");
$s->execute([$t1,$t2]); $rows = $s->fetchAll();
$g = $pdo->prepare("SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gelir' AND tarih BETWEEN ? AND ?"); $g->execute([$t1,$t2]); $gelir=$g->fetchColumn();
$gd = $pdo->prepare("SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gider' AND tarih BETWEEN ? AND ?"); $gd->execute([$t1,$t2]); $gider=$gd->fetchColumn();
$title='Kasa'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">💰 Kasa / Gelir-Gider</h1>
<div class="grid grid-cols-3 gap-3 mb-4">
<div class="bg-green-100 rounded-xl p-4 font-bold">Gelir<br><span class="text-2xl"><?= para($gelir) ?></span></div>
<div class="bg-red-100 rounded-xl p-4 font-bold">Gider<br><span class="text-2xl"><?= para($gider) ?></span></div>
<div class="bg-white rounded-xl p-4 font-bold shadow">Net<br><span class="text-2xl"><?= para($gelir-$gider) ?></span></div>
</div>
<div class="grid md:grid-cols-3 gap-4">
<form method="post" class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">+ Gelir / Gider ekle</h2>
<select name="yon" class="border rounded-lg p-2 w-full mb-2"><option value="gelir">Gelir</option><option value="gider">Gider</option></select>
<input name="baslik" required placeholder="Başlık (kira, deterjan…)" class="border rounded-lg p-2 w-full mb-2">
<input name="tutar" type="number" step="0.01" required placeholder="Tutar" class="border rounded-lg p-2 w-full mb-2">
<div class="flex gap-2 mb-2"><select name="yontem" class="border rounded-lg p-2 w-full"><option value="nakit">Nakit</option><option value="kart">Kart</option><option value="havale">Havale</option></select>
<input name="tarih" type="date" value="<?= date('Y-m-d') ?>" class="border rounded-lg p-2 w-full"></div>
<button class="bg-sky-600 text-white w-full py-2 rounded-lg font-bold">Kaydet</button></form>
<div class="md:col-span-2 bg-white rounded-xl shadow p-4">
<form class="flex gap-2 mb-3"><input type="date" name="t1" value="<?= e($t1) ?>" class="border rounded p-2"><input type="date" name="t2" value="<?= e($t2) ?>" class="border rounded p-2"><button class="bg-slate-800 text-white px-4 rounded">Filtrele</button></form>
<table class="w-full text-sm"><tr class="text-slate-500 text-left"><th>Tarih</th><th>Başlık</th><th class="text-right">Tutar</th></tr>
<?php foreach($rows as $r): ?><tr class="border-b"><td class="py-1 text-xs"><?= e($r['tarih']) ?></td>
<td><?= $r['yon']==='gelir'?'🟢':'🔴' ?> <?= e($r['baslik']) ?> <span class="text-xs text-slate-400">(<?= e($r['yontem']) ?> · <?= e($r['personel']) ?>)</span></td>
<td class="text-right font-bold <?= $r['yon']==='gelir'?'text-green-600':'text-red-600' ?>"><?= ($r['yon']==='gider'?'-':'').para($r['tutar']) ?></td></tr><?php endforeach; ?></table>
</div></div>
<?php require __DIR__.'/inc/footer.php'; ?>
