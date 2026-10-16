<?php
require_once __DIR__.'/inc/db.php';
giris_gerekli();
$pdo = db();

// Yeni kart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['islem'] ?? '') === 'yeni') {
    $plaka = plaka_temizle($_POST['plaka'] ?? '');
    if (!$plaka) { flash('Plaka zorunludur', 'hata'); }
    else {
        $ad = trim($_POST['musteri_ad'] ?? ''); $tel = trim($_POST['telefon'] ?? '');
        // Kayıtlı müşteri varsa bilgileri doldur
        $m = $pdo->prepare("SELECT * FROM musteriler WHERE plaka=? ORDER BY id DESC LIMIT 1");
        $m->execute([$plaka]); $kayitli = $m->fetch();
        $marka = $_POST['marka'] ?? ($kayitli['marka'] ?? '');
        $model = $_POST['model'] ?? ($kayitli['model'] ?? '');
        if ($kayitli && !$ad) { $ad = $kayitli['ad_soyad']; $tel = $tel ?: $kayitli['telefon']; }
        $kod = teslim_kodu();
        $pdo->prepare("INSERT INTO arac_kartlari (plaka,musteri_ad,telefon,marka,model,renk,personel,teslim_kodu,notlar) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$plaka, $ad, $tel, $marka, $model, $_POST['renk'] ?? '', $_SESSION['user']['ad'] ?? '', $kod, $_POST['notlar'] ?? '']);
        $id = (int)$pdo->lastInsertId();
        // Müşteri yoksa oluştur
        if (!$kayitli) {
            $pdo->prepare("INSERT INTO musteriler (ad_soyad,telefon,plaka,marka,model,renk) VALUES (?,?,?,?,?,?)")
                ->execute([$ad ?: 'Misafir', $tel, $plaka, $marka, $model, $_POST['renk'] ?? '']);
        }
        $msg = sms_doldur(ayar('sms_sablonu_kabul'), ['ad' => $ad ?: 'Müşterimiz', 'plaka' => $plaka, 'kod' => $kod]);
        if ($tel) sms_gonder($tel, $msg, 'kabul');
        flash("Araç kartı açıldı: $plaka (Teslim kodu: $kod)");
        header('Location: arac-detay.php?id=' . $id); exit;
    }
}
$q = trim($_GET['q'] ?? '');
$d = $_GET['durum'] ?? '';
$sql = "SELECT * FROM arac_kartlari WHERE 1=1";
$p = [];
if ($q) { $sql .= " AND (plaka LIKE ? OR musteri_ad LIKE ? OR telefon LIKE ?)"; $p = ["%$q%", "%$q%", "%$q%"]; }
if ($d) { $sql .= " AND durum=?"; $p[] = $d; }
$sql .= " ORDER BY id DESC LIMIT 100";
$s = $pdo->prepare($sql); $s->execute($p);
$kartlar = $s->fetchAll();
$title = 'Araç Kartları';
require __DIR__.'/inc/header.php';
?>
<div class="flex flex-wrap gap-2 items-center justify-between mb-4">
<h1 class="text-2xl font-extrabold">🧾 Araç Kartları</h1>
<form class="flex gap-2" method="get">
  <input name="q" value="<?= e($q) ?>" placeholder="Plaka yazarken bul…" class="border rounded-lg p-2 w-52" onkeyup="plakaCanli(this.value)">
  <select name="durum" class="border rounded-lg p-2"><option value="">Tümü</option>
  <?php foreach (['kabul','islemde','bitti','cagrildi','teslim'] as $x): ?>
    <option value="<?= $x ?>" <?= $d===$x?'selected':'' ?>><?= durum_etiket($x) ?></option>
  <?php endforeach; ?></select>
  <button class="bg-slate-800 text-white px-4 rounded-lg">Ara</button>
</form>
</div>
<div id="canli" class="mb-2 text-sm"></div>
<div class="bg-white rounded-xl shadow p-4 mb-4">
<h2 class="font-bold mb-2">+ Yeni araç kartı (plaka zorunlu, telefon opsiyonel)</h2>
<form method="post" class="grid md:grid-cols-6 gap-2">
<input type="hidden" name="islem" value="yeni">
<input name="plaka" id="plaka" placeholder="34 ABC 123" required class="border rounded-lg p-2 font-bold uppercase md:col-span-1">
<input name="musteri_ad" id="mad" placeholder="Müşteri adı" class="border rounded-lg p-2 md:col-span-1">
<input name="telefon" id="mtel" placeholder="Telefon" class="border rounded-lg p-2 md:col-span-1">
<input name="marka" placeholder="Marka (oto doldurulur)" class="border rounded-lg p-2">
<input name="model" placeholder="Model" class="border rounded-lg p-2">
<input name="renk" placeholder="Renk" class="border rounded-lg p-2">
<button type="button" onclick="plakaOku()" class="bg-violet-600 text-white rounded-lg p-2 font-bold">📷 Plakayı fotoğraftan oku (demo)</button>
<input name="notlar" placeholder="Not (opsiyonel)" class="border rounded-lg p-2 md:col-span-4">
<button class="bg-sky-600 text-white rounded-lg p-2 font-bold md:col-span-1">Kartı Aç</button>
</form>
</div>
<div class="bg-white rounded-xl shadow overflow-x-auto">
<table class="w-full text-sm">
<tr class="bg-slate-800 text-white text-left"><th class="p-2">Plaka</th><th>Müşteri</th><th>Durum</th><th>Toplam</th><th>Personel</th><th>Tarih</th><th></th></tr>
<?php foreach ($kartlar as $k): ?>
<tr class="border-b hover:bg-slate-50">
<td class="p-2 font-extrabold"><?= e($k['plaka']) ?><br><span class="text-xs font-normal text-slate-500">Kod: <?= e($k['teslim_kodu']) ?></span></td>
<td class="p-2"><?= e($k['musteri_ad']) ?><br><span class="text-xs text-slate-500"><?= e($k['telefon']) ?></span></td>
<td class="p-2"><span class="text-xs font-bold px-2 py-1 rounded bg-sky-100"><?= durum_etiket($k['durum']) ?></span></td>
<td class="p-2 font-bold"><?= para($k['toplam']) ?></td>
<td class="p-2"><?= e($k['personel']) ?></td>
<td class="p-2 text-xs"><?= e($k['created_at']) ?></td>
<td class="p-2"><a href="arac-detay.php?id=<?= $k['id'] ?>" class="bg-slate-800 text-white px-3 py-1 rounded-lg text-xs font-bold">Aç →</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<script>
function plakaOku(){
  // Demo: dosya yüklemeden plaka okuma simülasyonu — gerçek projede Plate Recognizer API bağlanır.
  const v = document.getElementById('plaka');
  if(!v.value){ v.value = '34 ABC ' + Math.floor(100+Math.random()*900); }
  alert('📷 (Demo) Plaka okundu: ' + v.value + '\nGerçek entegrasyon için Plate Recognizer / OpenALPR API anahtarı ayarlardan girilir.');
  plakaSorgula(v.value);
}
function plakaSorgula(plaka){
  fetch('<?= BASE_URL ?>/api/plaka-sorgula.php?plaka=' + encodeURIComponent(plaka))
    .then(r=>r.json()).then(j=>{
      if(j.bulundu){ document.getElementById('mad').value = j.ad_soyad; document.getElementById('mtel').value = j.telefon; }
    });
}
function plakaCanli(v){
  if(v.length < 2) return;
  fetch('<?= BASE_URL ?>/api/plaka-sorgula.php?plaka=' + encodeURIComponent(v))
    .then(r=>r.json()).then(j=>{
      document.getElementById('canli').innerHTML = j.bulundu ? ('✅ Kayıtlı: <b>'+j.plaka+'</b> — '+j.ad_soyad) : '';
    });
}
document.getElementById('plaka').addEventListener('blur', e=>plakaSorgula(e.target.value));
</script>
<?php require __DIR__.'/inc/footer.php'; ?>
