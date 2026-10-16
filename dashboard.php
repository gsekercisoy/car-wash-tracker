<?php
require_once __DIR__.'/inc/db.php';
giris_gerekli();
$pdo = db();
$bugun = date('Y-m-d');
$aktif = $pdo->query("SELECT COUNT(*) FROM arac_kartlari WHERE durum!='teslim'")->fetchColumn();
$ciro = $pdo->query("SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gelir' AND tarih='$bugun'")->fetchColumn();
$gider = $pdo->query("SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gider' AND tarih='$bugun'")->fetchColumn();
$rand = $pdo->query("SELECT COUNT(*) FROM randevular WHERE tarih='$bugun' AND durum='bekliyor'")->fetchColumn();
$kartlar = $pdo->query("SELECT * FROM arac_kartlari WHERE durum!='teslim' ORDER BY updated_at DESC LIMIT 20")->fetchAll();
$dusuk = $pdo->query("SELECT * FROM urunler WHERE stok<=min_stok")->fetchAll();
$title = 'Panel';
require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">Günaydın, <?= e($user['ad']) ?> 👋</h1>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
  <div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Aktif araç</div><div class="text-3xl font-extrabold"><?= (int)$aktif ?></div></div>
  <div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Bugünkü ciro</div><div class="text-3xl font-extrabold text-green-600"><?= para($ciro) ?></div></div>
  <div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Bugünkü gider</div><div class="text-3xl font-extrabold text-red-600"><?= para($gider) ?></div></div>
  <div class="bg-white rounded-xl p-4 shadow"><div class="text-sm text-slate-500">Bugünkü randevu</div><div class="text-3xl font-extrabold"><?= (int)$rand ?></div></div>
</div>
<div class="grid md:grid-cols-3 gap-4">
<div class="md:col-span-2 bg-white rounded-xl shadow p-4">
  <div class="flex justify-between items-center mb-3"><h2 class="font-bold">🔧 Sahadaki araçlar</h2><a href="araclar.php" class="text-sky-600 text-sm font-bold">+ Yeni kart</a></div>
  <?php if (!$kartlar): ?><p class="text-slate-500 text-sm">Aktif araç yok. Yeni kart açın.</p><?php endif; ?>
  <?php foreach ($kartlar as $k): ?>
    <a href="arac-detay.php?id=<?= $k['id'] ?>" class="flex justify-between items-center border rounded-lg px-3 py-2 mb-2 hover:bg-slate-50">
      <b><?= e($k['plaka']) ?></b><span class="text-sm"><?= e($k['musteri_ad']) ?></span>
      <span class="text-xs font-bold px-2 py-1 rounded bg-sky-100"><?= durum_etiket($k['durum']) ?></span>
      <span class="text-sm font-bold"><?= para($k['toplam']) ?></span>
    </a>
  <?php endforeach; ?>
</div>
<div class="bg-white rounded-xl shadow p-4">
  <h2 class="font-bold mb-3">⚠️ Kritik stok</h2>
  <?php if (!$dusuk): ?><p class="text-sm text-slate-500">Sorun yok.</p><?php endif; ?>
  <?php foreach ($dusuk as $u): ?>
    <div class="text-sm border rounded-lg p-2 mb-2">🧴 <?= e($u['baslik']) ?> — <b class="text-red-600"><?= (int)$u['stok'] ?> adet</b></div>
  <?php endforeach; ?>
  <a href="randevular.php" class="block mt-3 text-sm text-sky-600 font-bold">Bugünkü randevuları gör →</a>
</div>
</div>
<?php require __DIR__.'/inc/footer.php'; ?>
