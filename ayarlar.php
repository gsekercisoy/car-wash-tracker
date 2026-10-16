<?php
require_once __DIR__.'/inc/db.php'; admin_gerekli();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    foreach (['isletme_adi','renk','whatsapp','sms_sablonu_kabul','sms_sablonu_hazir'] as $k)
        if (isset($_POST[$k])) ayar_kaydet($k, trim($_POST[$k]));
    flash('Ayarlar kaydedildi'); header('Location: ayarlar.php'); exit;
}
$title='Ayarlar'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-4">⚙️ Ayarlar</h1>
<form method="post" class="bg-white rounded-xl shadow p-4 max-w-xl space-y-3">
<label class="block text-sm font-bold">İşletme adı<input name="isletme_adi" value="<?= e(ayar('isletme_adi')) ?>" class="border rounded-lg p-2 w-full font-normal"></label>
<label class="block text-sm font-bold">Kurumsal renk <input type="color" name="renk" value="<?= e(ayar('renk','#0ea5e9')) ?>"></label>
<label class="block text-sm font-bold">WhatsApp hattı<input name="whatsapp" value="<?= e(ayar('whatsapp')) ?>" class="border rounded-lg p-2 w-full font-normal"></label>
<label class="block text-sm font-bold">SMS — kabul şablonu <span class="font-normal">(değişkenler: {ad} {plaka} {kod})</span><textarea name="sms_sablonu_kabul" class="border rounded-lg p-2 w-full font-normal" rows="2"><?= e(ayar('sms_sablonu_kabul')) ?></textarea></label>
<label class="block text-sm font-bold">SMS — hazır şablonu<textarea name="sms_sablonu_hazir" class="border rounded-lg p-2 w-full font-normal" rows="2"><?= e(ayar('sms_sablonu_hazir')) ?></textarea></label>
<button class="bg-sky-600 text-white px-5 py-2 rounded-lg font-bold">Kaydet</button></form>
<?php require __DIR__.'/inc/footer.php'; ?>
