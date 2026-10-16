<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli(); $pdo = db();
$t1 = $_GET['t1']??date('Y-m-01'); $t2 = $_GET['t2']??date('Y-m-d');
$ciro = $pdo->prepare("SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gelir' AND tarih BETWEEN ? AND ?"); $ciro->execute([$t1,$t2]); $ciro=$ciro->fetchColumn();
$gider = $pdo->prepare("SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gider' AND tarih BETWEEN ? AND ?"); $gider->execute([$t1,$t2]); $gider=$gider->fetchColumn();
$arac = $pdo->prepare("SELECT COUNT(*) FROM arac_kartlari WHERE DATE(created_at) BETWEEN ? AND ?"); $arac->execute([$t1,$t2]); $arac=$arac->fetchColumn();
$perf = $pdo->prepare("SELECT personel, COUNT(*) adet, COALESCE(SUM(toplam),0) tutar FROM arac_kartlari WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY personel"); $perf->execute([$t1,$t2]); $perf=$perf->fetchAll();
$hs = $pdo->prepare("SELECT baslik, COUNT(*) adet, SUM((birim_fiyat*adet)-indirim) tutar FROM kart_kalemleri k JOIN arac_kartlari a ON a.id=k.kart_id WHERE DATE(a.created_at) BETWEEN ? AND ? GROUP BY baslik ORDER BY tutar DESC LIMIT 15"); $hs->execute([$t1,$t2]); $hs=$hs->fetchAll();
if (isset($_GET['csv'])) {
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=rapor.csv');
    echo "\xEF\xBB\xBF"; echo "Baslik;Adet;Tutar\n";
    foreach($hs as $h) echo "{$h['baslik']};{$h['adet']};{$h['tutar']}\n";
    exit;
}
$title='Raporlar'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">📈 Raporlar</h1>
<form class="flex gap-2 mb-4 bg-white p-3 rounded-xl shadow">
<input type="date" name="t1" value="<?= e($t1) ?>" class="border rounded p-2"><input type="date" name="t2" value="<?= e($t2) ?>" class="border rounded p-2">
<button class="bg-slate-800 text-white px-4 rounded font-bold">Filtrele</button>
<a href="?t1=<?= e($t1) ?>&t2=<?= e($t2) ?>&csv=1" class="bg-green-600 text-white px-4 py-2 rounded font-bold">⬇ Excel (CSV)</a>
<button type="button" onclick="window.print()" class="bg-white border px-4 py-2 rounded font-bold">🖨 PDF / Yazdır</button></form>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
<div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Ciro</div><div class="text-2xl font-extrabold text-green-600"><?= para($ciro) ?></div></div>
<div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Gider</div><div class="text-2xl font-extrabold text-red-600"><?= para($gider) ?></div></div>
<div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Net</div><div class="text-2xl font-extrabold"><?= para($ciro-$gider) ?></div></div>
<div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Araç sayısı</div><div class="text-2xl font-extrabold"><?= (int)$arac ?></div></div>
</div>
<div class="grid md:grid-cols-2 gap-4">
<div class="bg-white rounded-xl shadow p-4"><h2 class="font-bold mb-2">👷 Personel performansı</h2>
<table class="w-full text-sm"><?php foreach($perf as $p): ?><tr class="border-b"><td class="py-1"><?= e($p['personel']?:'—') ?></td><td><?= (int)$p['adet'] ?> araç</td><td class="text-right font-bold"><?= para($p['tutar']) ?></td></tr><?php endforeach; ?></table></div>
<div class="bg-white rounded-xl shadow p-4"><h2 class="font-bold mb-2">🔥 En çok satan hizmet/ürün</h2>
<table class="w-full text-sm"><?php foreach($hs as $h): ?><tr class="border-b"><td class="py-1"><?= e($h['baslik']) ?></td><td><?= (int)$h['adet'] ?></td><td class="text-right font-bold"><?= para($h['tutar']) ?></td></tr><?php endforeach; ?></table></div>
</div>
<?php require __DIR__.'/inc/footer.php'; ?>
