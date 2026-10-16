<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli(); $pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (($_POST['t']??'')==='hizmet') {
        if (isset($_POST['sil'])) $pdo->prepare("DELETE FROM hizmetler WHERE id=?")->execute([(int)$_POST['sil']]);
        else $pdo->prepare("INSERT INTO hizmetler (baslik,fiyat) VALUES (?,?)")->execute([$_POST['b'],$_POST['f']]);
    } else {
        if (isset($_POST['sil'])) $pdo->prepare("DELETE FROM urunler WHERE id=?")->execute([(int)$_POST['sil']]);
        elseif (isset($_POST['stokid'])) $pdo->prepare("UPDATE urunler SET stok=? WHERE id=?")->execute([(int)$_POST['stok'],(int)$_POST['stokid']]);
        else $pdo->prepare("INSERT INTO urunler (baslik,fiyat,stok,min_stok) VALUES (?,?,?,?)")->execute([$_POST['b'],$_POST['f'],$_POST['s'],$_POST['ms']]);
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
}
$tek = basename($_SERVER['PHP_SELF'])==='hizmetler.php';
$title = $tek?'Hizmetler':'Ürünler'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4"><?= $tek?'✂️ Hizmetler':'🧴 Ürün & Stok' ?></h1>
<div class="flex gap-2 mb-4"><a href="hizmetler.php" class="px-4 py-2 rounded-lg font-bold <?= $tek?'bg-sky-600 text-white':'bg-white' ?>">Hizmetler</a>
<a href="urunler.php" class="px-4 py-2 rounded-lg font-bold <?= !$tek?'bg-sky-600 text-white':'bg-white' ?>">Ürünler</a></div>
<?php if ($tek): $rows=$pdo->query("SELECT * FROM hizmetler ORDER BY id DESC")->fetchAll(); ?>
<form method="post" class="bg-white rounded-xl shadow p-4 flex gap-2 mb-4">
<input type="hidden" name="t" value="hizmet"><input name="b" required placeholder="Hizmet adı" class="border rounded-lg p-2 flex-1">
<input name="f" type="number" step="0.01" placeholder="Fiyat" class="border rounded-lg p-2 w-32"><button class="bg-sky-600 text-white px-4 rounded-lg font-bold">Ekle</button></form>
<div class="bg-white rounded-xl shadow p-4"><?php foreach($rows as $r): ?>
<div class="flex justify-between border-b py-2 text-sm"><span><?= e($r['baslik']) ?></span><span class="font-bold"><?= para($r['fiyat']) ?>
<form method="post" class="inline"><input type="hidden" name="t" value="hizmet"><input type="hidden" name="sil" value="<?= $r['id'] ?>"><button class="text-red-500 text-xs ml-2">sil</button></form></span></div>
<?php endforeach; ?></div>
<?php else: $rows=$pdo->query("SELECT * FROM urunler ORDER BY id DESC")->fetchAll(); ?>
<form method="post" class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-2 mb-4">
<input type="hidden" name="t" value="urun"><input name="b" required placeholder="Ürün adı" class="border rounded-lg p-2 flex-1">
<input name="f" type="number" step="0.01" placeholder="Fiyat" class="border rounded-lg p-2 w-28">
<input name="s" type="number" placeholder="Stok" class="border rounded-lg p-2 w-24"><input name="ms" type="number" value="5" placeholder="Min" class="border rounded-lg p-2 w-20">
<button class="bg-emerald-600 text-white px-4 rounded-lg font-bold">Ekle</button></form>
<div class="bg-white rounded-xl shadow p-4"><?php foreach($rows as $r): $kritik=$r['stok']<=$r['min_stok']; ?>
<div class="flex justify-between items-center border-b py-2 text-sm <?= $kritik?'bg-red-50':'' ?>">
<span>🧴 <?= e($r['baslik']) ?> — <?= para($r['fiyat']) ?> <?= $kritik?'<b class="text-red-600">KRİTİK</b>':'' ?></span>
<span class="flex gap-2 items-center"><form method="post" class="flex gap-1"><input type="hidden" name="t" value="urun"><input type="hidden" name="stokid" value="<?= $r['id'] ?>">
<input name="stok" type="number" value="<?= $r['stok'] ?>" class="border rounded p-1 w-20"><button class="text-xs bg-slate-800 text-white px-2 rounded">stok</button></form>
<form method="post"><input type="hidden" name="t" value="urun"><input type="hidden" name="sil" value="<?= $r['id'] ?>"><button class="text-red-500 text-xs">sil</button></form></span></div>
<?php endforeach; ?></div>
<?php endif; ?>
<?php require __DIR__.'/inc/footer.php'; ?>
