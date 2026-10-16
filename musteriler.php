<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli(); $pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (($_POST['islem']??'')==='sil') { $pdo->prepare("DELETE FROM musteriler WHERE id=?")->execute([(int)$_POST['id']]); }
    else {
        $pdo->prepare("INSERT INTO musteriler (ad_soyad,telefon,plaka,marka,model,renk,notlar) VALUES (?,?,?,?,?,?,?)")
            ->execute([$_POST['ad'],$_POST['tel'],plaka_temizle($_POST['plaka']),$_POST['marka']??'',$_POST['model']??'',$_POST['renk']??'',$_POST['not']??'']);
    }
    header('Location: musteriler.php'); exit;
}
$q = trim($_GET['q']??'');
$s = $pdo->prepare("SELECT * FROM musteriler WHERE ad_soyad LIKE ? OR plaka LIKE ? OR telefon LIKE ? ORDER BY id DESC LIMIT 200");
$s->execute(["%$q%","%$q%","%$q%"]); $rows = $s->fetchAll();
$title='Müşteriler'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">👥 Müşteriler</h1>
<div class="grid md:grid-cols-3 gap-4">
<form method="post" class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">+ Yeni müşteri</h2>
<input name="ad" required placeholder="Ad Soyad" class="border rounded-lg p-2 w-full mb-2">
<input name="tel" placeholder="Telefon" class="border rounded-lg p-2 w-full mb-2">
<input name="plaka" required placeholder="Plaka" class="border rounded-lg p-2 w-full mb-2 uppercase">
<div class="flex gap-2 mb-2"><input name="marka" placeholder="Marka" class="border rounded-lg p-2 w-full"><input name="model" placeholder="Model" class="border rounded-lg p-2 w-full"></div>
<input name="renk" placeholder="Renk" class="border rounded-lg p-2 w-full mb-2">
<input name="not" placeholder="Not" class="border rounded-lg p-2 w-full mb-2">
<button class="bg-sky-600 text-white w-full py-2 rounded-lg font-bold">Kaydet</button>
</form>
<div class="md:col-span-2 bg-white rounded-xl shadow p-4">
<form class="flex gap-2 mb-3"><input name="q" value="<?= e($q) ?>" placeholder="Plaka / ad / telefon ara…" class="border rounded-lg p-2 flex-1"><button class="bg-slate-800 text-white px-4 rounded-lg">Ara</button></form>
<table class="w-full text-sm"><tr class="text-left text-slate-500"><th>Plaka</th><th>Ad</th><th>Telefon</th><th>Araç</th><th></th></tr>
<?php foreach($rows as $r): ?><tr class="border-b">
<td class="py-1 font-bold"><?= e($r['plaka']) ?></td><td><?= e($r['ad_soyad']) ?></td><td><?= e($r['telefon']) ?></td>
<td class="text-xs"><?= e(trim($r['marka'].' '.$r['model'].' '.$r['renk'])) ?></td>
<td><form method="post" onsubmit="return confirm('Silinsin mi?')"><input type="hidden" name="islem" value="sil"><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="text-red-500 text-xs">sil</button></form></td>
</tr><?php endforeach; ?></table>
</div></div>
<?php require __DIR__.'/inc/footer.php'; ?>
