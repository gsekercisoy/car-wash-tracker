<?php
// Merkezi ayarlar - XAMPP için
session_start();
date_default_timezone_set('Europe/Istanbul');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'oto_yikama');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP varsayılan: boş
define('BASE_URL', '/oto-yikama');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci");
        $pdo->exec("USE `" . DB_NAME . "`");
    }
    return $pdo;
}

function ayar(string $k, string $varsayilan = ''): string {
    try {
        $s = db()->prepare("SELECT deger FROM ayarlar WHERE anahtar=?");
        $s->execute([$k]);
        $r = $s->fetchColumn();
        return $r !== false ? (string)$r : $varsayilan;
    } catch (Throwable $e) { return $varsayilan; }
}

function ayar_kaydet(string $k, string $v): void {
    db()->prepare("INSERT INTO ayarlar (anahtar,deger) VALUES (?,?) ON DUPLICATE KEY UPDATE deger=VALUES(deger)")
       ->execute([$k, $v]);
}

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function para($v): string { return number_format((float)$v, 2, ',', '.') . ' ₺'; }
function plaka_temizle(string $p): string {
    $p = mb_strtoupper(trim($p), 'UTF-8');
    return preg_replace('/\s+/', ' ', $p);
}
function teslim_kodu(): string { return str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT); }
function flash(string $msg, string $tip = 'ok'): void { $_SESSION['flash'] = ['msg' => $msg, 'tip' => $tip]; }
function flash_goster(): string {
    if (empty($_SESSION['flash'])) return '';
    $f = $_SESSION['flash']; unset($_SESSION['flash']);
    $renk = $f['tip'] === 'hata' ? 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca' : 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0';
    return '<div style="' . $renk . ';padding:10px 14px;border-radius:10px;margin-bottom:14px;font-weight:600">' . e($f['msg']) . '</div>';
}
function sms_gonder(string $telefon, string $mesaj, string $tip = 'bilgi'): void {
    // Gerçek SMS entegrasyonu yok: log tablosuna düşer, panelde "SMS Log" gibi görünür.
    try { db()->prepare("INSERT INTO sms_log (telefon,mesaj,tip) VALUES (?,?,?)")->execute([$telefon, $mesaj, $tip]); } catch (Throwable $e) {}
}
function sms_doldur(string $sablon, array $d): string {
    foreach ($d as $k => $v) $sablon = str_replace('{' . $k . '}', $v, $sablon);
    return $sablon;
}
function kart_toplam(int $kart_id): float {
    $s = db()->prepare("SELECT COALESCE(SUM((birim_fiyat*adet)-indirim),0) FROM kart_kalemleri WHERE kart_id=?");
    $s->execute([$kart_id]);
    return (float)$s->fetchColumn();
}
function kart_toplam_guncelle(int $kart_id): void {
    $t = kart_toplam($kart_id);
    db()->prepare("UPDATE arac_kartlari SET toplam=? WHERE id=?")->execute([$t, $kart_id]);
}
function giris_gerekli(): void {
    if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . '/login.php'); exit; }
}
function admin_gerekli(): void {
    giris_gerekli();
    if (($_SESSION['user']['rol'] ?? '') !== 'admin') { http_response_code(403); exit('Yetkisiz erişim'); }
}
function durum_etiket(string $d): string {
    return ['kabul'=>'Kabul','islemde'=>'İşlemde','bitti'=>'Bitti','cagrildi'=>'Çağrıldı','teslim'=>'Teslim'][$d] ?? $d;
}
