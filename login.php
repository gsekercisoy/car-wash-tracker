<?php
require_once __DIR__ . '/inc/db.php';
if (!empty($_SESSION['user'])) { header('Location: ' . BASE_URL . '/dashboard.php'); exit; }
$hata = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $k = trim($_POST['k'] ?? ''); $s = $_POST['s'] ?? '';
    try {
        $st = db()->prepare("SELECT * FROM users WHERE kullanici_adi=? AND aktif=1");
        $st->execute([$k]);
        $u = $st->fetch();
        if ($u && password_verify($s, $u['sifre'])) {
            unset($u['sifre']);
            $_SESSION['user'] = $u;
            header('Location: ' . BASE_URL . '/dashboard.php'); exit;
        }
        $hata = 'Kullanıcı adı veya şifre hatalı.';
    } catch (Throwable $e) { $hata = 'Önce kurulum.php dosyasını çalıştırın. (' . $e->getMessage() . ')'; }
}
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Giriş — <?= e(ayar('isletme_adi')) ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
<form method="post" class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-2xl">
<h1 class="text-2xl font-extrabold mb-1">🚗 <?= e(ayar('isletme_adi')) ?></h1>
<p class="text-sm text-slate-500 mb-5">Personel girişi (demo: admin / admin123)</p>
<?php if ($hata): ?><div class="bg-red-100 text-red-700 p-2 rounded mb-3 text-sm"><?= e($hata) ?></div><?php endif; ?>
<label class="text-sm font-bold">Kullanıcı adı</label>
<input name="k" class="w-full border rounded-lg p-2 mb-3" required>
<label class="text-sm font-bold">Şifre</label>
<input name="s" type="password" class="w-full border rounded-lg p-2 mb-4" required>
<button class="w-full bg-sky-600 text-white font-bold py-2 rounded-lg">Giriş Yap</button>
<p class="text-xs text-center mt-3"><a class="text-sky-600" href="<?= BASE_URL ?>/kurulum.php">İlk kurulum</a> · <a class="text-sky-600" href="<?= BASE_URL ?>/index.php">Siteye dön</a></p>
</form></body></html>
