<?php
require_once __DIR__.'/inc/db.php'; giris_gerekli();
$rows = db()->query("SELECT * FROM sms_log ORDER BY id DESC LIMIT 150")->fetchAll();
$title='SMS Log'; require __DIR__.'/inc/header.php';
?>
<h1 class="text-2xl font-extrabold mb-1">💬 SMS / WhatsApp Geçmişi</h1>
<p class="text-sm text-slate-500 mb-4">Demo mod: mesajlar gerçekten gönderilmez, buraya düşer. Gerçek NetGSM/Twilio anahtarı Ayarlar'a girilince <code>sms_gonder()</code> fonksiyonuna bağlanır.</p>
<div class="bg-white rounded-xl shadow p-4 space-y-2">
<?php foreach($rows as $r): ?><div class="border rounded-lg p-2 text-sm">📩 <b><?= e($r['telefon']) ?></b> <span class="text-xs bg-slate-100 px-2 rounded"><?= e($r['tip']) ?></span> <span class="text-xs text-slate-400"><?= e($r['created_at']) ?></span><br><?= e($r['mesaj']) ?></div><?php endforeach; ?>
<?php if(!$rows): ?><p class="text-sm text-slate-500">Henüz mesaj yok.</p><?php endif; ?>
</div>
<?php require __DIR__.'/inc/footer.php'; ?>
