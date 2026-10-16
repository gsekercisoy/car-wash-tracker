<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli(); $pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['sil'])) $pdo->prepare("DELETE FROM notlar WHERE id=?")->execute([(int)$_POST['sil']]);
    else $pdo->prepare("INSERT INTO notlar (baslik,icerik) VALUES (?,?)")->execute([$_POST['b'],$_POST['i']]);
    header('Location: notlar.php'); exit;
}
$q = trim($_GET['q']??'');
$s = $pdo->prepare("SELECT * FROM notlar WHERE baslik LIKE ? OR icerik LIKE ? ORDER BY id DESC"); $s->execute(["%$q%","%$q%"]);
$title='Notlar'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">📝 Notlar</h1>
<div class="grid md:grid-cols-3 gap-4">
<form method="post" class="bg-white rounded-xl shadow p-4"><h2 class="font-bold mb-2">+ Not</h2>
<input name="b" required placeholder="Başlık" class="border rounded-lg p-2 w-full mb-2"><textarea name="i" required placeholder="İçerik" class="border rounded-lg p-2 w-full mb-2" rows="4"></textarea>
<button class="bg-sky-600 text-white w-full py-2 rounded-lg font-bold">Kaydet</button></form>
<div class="md:col-span-2 bg-white rounded-xl shadow p-4">
<form class="flex gap-2 mb-3"><input name="q" value="<?= e($q) ?>" placeholder="Not ara…" class="border rounded-lg p-2 flex-1"><button class="bg-slate-800 text-white px-4 rounded-lg">Ara</button></form>
<?php foreach($s as $n): ?><div class="border rounded-lg p-2 mb-2 text-sm"><b><?= e($n['baslik']) ?></b> <span class="text-xs text-slate-400"><?= e($n['created_at']) ?></span><p><?= nl2br(e($n['icerik'])) ?></p>
<form method="post" class="inline"><input type="hidden" name="sil" value="<?= $n['id'] ?>"><button class="text-red-500 text-xs">sil</button></form></div><?php endforeach; ?>
</div></div>
<?php require __DIR__.'/inc/footer.php'; ?>
