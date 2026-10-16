<?php
require_once __DIR__ . '/db.php';
$renk = ayar('renk', '#0ea5e9');
$site = ayar('isletme_adi', 'Parla Oto Yıkama');
$user = $_SESSION['user'] ?? null;
?><!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? $site) ?> — <?= e($site) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<style>:root{--renk:<?= e($renk) ?>}</style>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-slate-100">
<div class="flex min-h-screen">
<aside class="w-60 shrink-0 text-white p-4 hidden md:flex flex-col gap-1" style="background:#0f172a">
  <a href="<?= BASE_URL ?>/dashboard.php" class="text-xl font-extrabold px-2 py-3">🚗 <?= e($site) ?></a>
  <div class="text-xs uppercase tracking-wider text-slate-400 px-2 mt-2">Operasyon</div>
  <?php
  $menu = [
    'dashboard.php'=>'📊 Panel','araclar.php'=>'🧾 Araç Kartları','musteriler.php'=>'👥 Müşteriler',
    'randevular.php'=>'📅 Randevular','hizmetler.php'=>'✂️ Hizmetler','urunler.php'=>'🧴 Ürün & Stok',
    'cari.php'=>'💳 Cari Takip','kasa.php'=>'💰 Kasa / Gider','raporlar.php'=>'📈 Raporlar',
    'notlar.php'=>'📝 Notlar','sms.php'=>'💬 SMS Log',
  ];
  $cur = basename($_SERVER['PHP_SELF']);
  foreach ($menu as $f => $t): $a = $cur === $f ? 'background:var(--renk)' : '';
  ?>
    <a href="<?= BASE_URL ?>/<?= $f ?>" class="px-3 py-2 rounded-lg hover:bg-slate-700" style="<?= $a ?>"><?= $t ?></a>
  <?php endforeach; ?>
  <?php if (($user['rol'] ?? '') === 'admin'): ?>
    <div class="text-xs uppercase tracking-wider text-slate-400 px-2 mt-3">Yönetim</div>
    <a href="<?= BASE_URL ?>/kullanicilar.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">👤 Kullanıcılar</a>
    <a href="<?= BASE_URL ?>/ayarlar.php" class="px-3 py-2 rounded-lg hover:bg-slate-700">⚙️ Ayarlar</a>
  <?php endif; ?>
  <div class="mt-auto text-sm text-slate-300 px-2 pt-4 border-t border-slate-700">
    <?= e($user['ad'] ?? '') ?> (<?= e($user['rol'] ?? '') ?>)<br>
    <a href="<?= BASE_URL ?>/logout.php" class="text-red-300 hover:underline">Çıkış yap</a>
    · <a href="<?= BASE_URL ?>/index.php" class="text-slate-300 hover:underline">Site</a>
  </div>
</aside>
<main class="flex-1 p-4 md:p-8 max-w-6xl w-full">
<?= flash_goster() ?>
