<?php
require_once __DIR__.'/inc/db.php'; admin_gerekli(); $pdo = db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['sil'])) $pdo->prepare("DELETE FROM users WHERE id=? AND kullanici_adi!='admin'")->execute([(int)$_POST['sil']]);
    else $pdo->prepare("INSERT INTO users (ad,kullanici_adi,sifre,rol) VALUES (?,?,?,?)")
        ->execute([$_POST['ad'],$_POST['k'],password_hash($_POST['s'],PASSWORD_DEFAULT),$_POST['rol']]);
    header('Location: kullanicilar.php'); exit;
}
$rows = $pdo->query("SELECT id,ad,kullanici_adi,rol,aktif,created_at FROM users ORDER BY id")->fetchAll();
$title='Kullanıcılar'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">👤 Kullanıcılar & Yetkiler</h1>
<div class="grid md:grid-cols-3 gap-4">
<form method="post" class="bg-white rounded-xl shadow p-4"><h2 class="font-bold mb-2">+ Kullanıcı</h2>
<input name="ad" required placeholder="Ad" class="border rounded-lg p-2 w-full mb-2"><input name="k" required placeholder="Kullanıcı adı" class="border rounded-lg p-2 w-full mb-2">
<input name="s" required type="password" placeholder="Şifre" class="border rounded-lg p-2 w-full mb-2">
<select name="rol" class="border rounded-lg p-2 w-full mb-2"><option value="personel">Personel (sade ekran)</option><option value="admin">Admin (tam yetki)</option></select>
<button class="bg-sky-600 text-white w-full py-2 rounded-lg font-bold">Oluştur</button>
<p class="text-xs text-slate-500 mt-2">Personel; rapor, kullanıcı ve ayar menülerini göremez.</p></form>
<div class="md:col-span-2 bg-white rounded-xl shadow p-4"><table class="w-full text-sm">
<tr class="text-slate-500 text-left"><th>Ad</th><th>Kullanıcı</th><th>Rol</th><th></th></tr>
<?php foreach($rows as $r): ?><tr class="border-b"><td class="py-1"><?= e($r['ad']) ?></td><td><?= e($r['kullanici_adi']) ?></td><td><?= e($r['rol']) ?></td>
<td><?php if($r['kullanici_adi']!=='admin'): ?><form method="post" onsubmit="return confirm('Silinsin mi?')"><input type="hidden" name="sil" value="<?= $r['id'] ?>"><button class="text-red-500 text-xs">sil</button></form><?php endif; ?></td></tr><?php endforeach; ?>
</table></div></div>
<?php require __DIR__.'/inc/footer.php'; ?>
