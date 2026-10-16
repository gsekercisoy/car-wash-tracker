<?php
require_once __DIR__.'/inc/db.php';
giris_gerekli();
$pdo = db();
$id = (int)($_GET['id'] ?? 0);
$kart = $pdo->prepare("SELECT * FROM arac_kartlari WHERE id=?");
$kart->execute([$id]); $k = $kart->fetch();
if (!$k) { flash('Kart bulunamadı', 'hata'); header('Location: araclar.php'); exit; }

// Kalem ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['islem'] ?? '') === 'kalem') {
    $tur = $_POST['tur']; $ref = (int)$_POST['ref'];
    if ($tur === 'hizmet') {
        $h = $pdo->prepare("SELECT * FROM hizmetler WHERE id=?"); $h->execute([$ref]); $h = $h->fetch();
        $pdo->prepare("INSERT INTO kart_kalemleri (kart_id,tur,ref_id,baslik,adet,birim_fiyat,indirim) VALUES (?,?,?,?,?,?,?)")
            ->execute([$id, 'hizmet', $ref, $h['baslik'], 1, $h['fiyat'], $_POST['indirim'] ?? 0]);
    } else {
        $adet = max(1, (int)($_POST['adet'] ?? 1));
        $u = $pdo->prepare("SELECT * FROM urunler WHERE id=?"); $u->execute([$ref]); $u = $u->fetch();
        if ($u['stok'] < $adet) { flash('Yetersiz stok!', 'hata'); }
        else {
            $pdo->prepare("INSERT INTO kart_kalemleri (kart_id,tur,ref_id,baslik,adet,birim_fiyat,indirim) VALUES (?,?,?,?,?,?,?)")
                ->execute([$id, 'urun', $ref, $u['baslik'], $adet, $u['fiyat'], 0]);
            $pdo->prepare("UPDATE urunler SET stok=stok-? WHERE id=?")->execute([$adet, $ref]);
        }
    }
    kart_toplam_guncelle($id);
    header("Location: arac-detay.php?id=$id"); exit;
}
// Kalem sil
if (isset($_GET['sil'])) {
    $pdo->prepare("DELETE FROM kart_kalemleri WHERE id=? AND kart_id=?")->execute([(int)$_GET['sil'], $id]);
    kart_toplam_guncelle($id);
    header("Location: arac-detay.php?id=$id"); exit;
}
// Durum ilerlet
$sira = ['kabul' => 'islemde', 'islemde' => 'bitti', 'bitti' => 'cagrildi', 'cagrildi' => 'teslim'];
if (isset($_GET['durum'])) {
    $yeni = $_GET['durum'];
    if ($yeni === 'ileri') $yeni = $sira[$k['durum']] ?? 'teslim';
    $pdo->prepare("UPDATE arac_kartlari SET durum=? WHERE id=?")->execute([$yeni, $id]);
    if ($yeni === 'cagrildi' && $k['telefon']) {
        $msg = sms_doldur(ayar('sms_sablonu_hazir'), ['ad' => $k['musteri_ad'], 'plaka' => $k['plaka'], 'kod' => $k['teslim_kodu']]);
        sms_gonder($k['telefon'], $msg, 'hazir');
    }
    // Teslim = tahsilat
    if ($yeni === 'teslim') {
        $t = kart_toplam($id);
        $pdo->prepare("UPDATE arac_kartlari SET toplam=?, odenen=?, odeme_yontemi=? WHERE id=?")
            ->execute([$t, $t, $_GET['yontem'] ?? 'nakit', $id]);
        $pdo->prepare("INSERT INTO kasa_hareketler (yon,baslik,tutar,yontem,kart_id,personel,tarih) VALUES ('gelir',?,?,?,?,?,CURDATE())")
            ->execute(["Araç teslim: {$k['plaka']}", $t, $_GET['yontem'] ?? 'nakit', $id, $_SESSION['user']['ad'] ?? '']);
    }
    flash('Durum: ' . durum_etiket($yeni));
    header("Location: arac-detay.php?id=$id"); exit;
}
$kalemler = $pdo->prepare("SELECT * FROM kart_kalemleri WHERE kart_id=?");
$kalemler->execute([$id]); $kalemler = $kalemler->fetchAll();
$hizmetler = $pdo->query("SELECT * FROM hizmetler WHERE aktif=1")->fetchAll();
$urunler = $pdo->query("SELECT * FROM urunler WHERE aktif=1")->fetchAll();
$title = $k['plaka'];
require __DIR__.'/inc/header.php';
$buton = ['kabul' => ['islemde','▶ Aracı İşleme Al'], 'islemde' => ['bitti','✅ İşlemi Bitir'], 'bitti' => ['cagrildi','📞 Müşteriyi Çağır'], 'cagrildi' => ['teslim','💰 Ödemeyi Al, Teslim Et']];
?>
<a href="araclar.php" class="text-sm text-sky-600 font-bold">← Kartlara dön</a>
<h1 class="text-2xl font-extrabold my-2"><?= e($k['plaka']) ?> <span class="text-sm bg-sky-600 text-white px-2 py-1 rounded"><?= durum_etiket($k['durum']) ?></span></h1>
<p class="text-sm text-slate-600 mb-4"><?= e($k['musteri_ad']) ?> · <?= e($k['telefon']) ?> · <?= e(trim($k['marka'].' '.$k['model'])) ?> · Teslim kodu: <b><?= e($k['teslim_kodu']) ?></b></p>
<?php if (isset($buton[$k['durum']])): ?>
  <div class="flex gap-2 mb-4">
    <a href="?id=<?= $id ?>&durum=<?= $buton[$k['durum']][0] ?>&yontem=nakit" class="bg-green-600 text-white px-5 py-3 rounded-xl font-bold"><?= $buton[$k['durum']][1] ?> (Nakit)</a>
    <?php if ($k['durum'] === 'cagrildi'): ?>
      <a href="?id=<?= $id ?>&durum=teslim&yontem=kart" class="bg-slate-800 text-white px-5 py-3 rounded-xl font-bold">💳 Kart ile Teslim</a>
    <?php endif; ?>
    <?php if ($k['durum'] !== 'teslim'): ?>
      <a href="?id=<?= $id ?>&durum=teslim&yontem=veresiye" class="bg-amber-500 text-white px-5 py-3 rounded-xl font-bold">📒 Veresiye Teslim</a>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="bg-green-100 text-green-800 p-3 rounded-xl mb-4 font-bold">✅ Teslim edildi — <?= para($k['odenen']) ?> (<?= e($k['odeme_yontemi']) ?>) <button onclick="window.print()" class="ml-3 bg-white px-3 py-1 rounded text-sm">🖨 Fiş yazdır</button></div>
<?php endif; ?>
<div class="grid md:grid-cols-2 gap-4">
<div class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">Kalemler — Toplam: <?= para($k['toplam']) ?></h2>
<table class="w-full text-sm"><?php foreach ($kalemler as $kl): ?>
<tr class="border-b"><td class="py-1"><?= $kl['tur']==='hizmet'?'✂️':'🧴' ?> <?= e($kl['baslik']) ?> × <?= $kl['adet'] ?></td>
<td class="text-right font-bold"><?= para($kl['birim_fiyat']*$kl['adet']-$kl['indirim']) ?></td>
<td><a href="?id=<?= $id ?>&sil=<?= $kl['id'] ?>" class="text-red-500 text-xs">sil</a></td></tr>
<?php endforeach; ?></table>
<?php if (!$kalemler): ?><p class="text-sm text-slate-500">Henüz hizmet/ürün eklenmedi.</p><?php endif; ?>
</div>
<div class="bg-white rounded-xl shadow p-4">
<h2 class="font-bold mb-2">Hizmet / Ürün ekle</h2>
<form method="post" class="flex gap-2 mb-2">
<input type="hidden" name="islem" value="kalem"><input type="hidden" name="tur" value="hizmet">
<select name="ref" class="border rounded-lg p-2 flex-1"><?php foreach ($hizmetler as $h): ?><option value="<?= $h['id'] ?>"><?= e($h['baslik']) ?> — <?= para($h['fiyat']) ?></option><?php endforeach; ?></select>
<input name="indirim" type="number" step="0.01" value="0" placeholder="İndirim" class="border rounded-lg p-2 w-24">
<button class="bg-sky-600 text-white px-3 rounded-lg font-bold">+ Hizmet</button>
</form>
<form method="post" class="flex gap-2">
<input type="hidden" name="islem" value="kalem"><input type="hidden" name="tur" value="urun">
<select name="ref" class="border rounded-lg p-2 flex-1"><?php foreach ($urunler as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['baslik']) ?> — <?= para($u['fiyat']) ?> (stok:<?= $u['stok'] ?>)</option><?php endforeach; ?></select>
<input name="adet" type="number" value="1" min="1" class="border rounded-lg p-2 w-20">
<button class="bg-emerald-600 text-white px-3 rounded-lg font-bold">+ Ürün</button>
</form>
</div>
</div>
<?php require __DIR__.'/inc/footer.php'; ?>
