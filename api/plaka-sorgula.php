<?php
require_once __DIR__.'/inc/db.php';
header('Content-Type: application/json; charset=utf-8');
$plaka = plaka_temizle($_GET['plaka'] ?? '');
if (mb_strlen($plaka) < 2) { echo json_encode(['bulundu'=>false]); exit; }
try {
    $s = db()->prepare("SELECT ad_soyad,telefon,plaka,marka,model,renk FROM musteriler WHERE plaka LIKE ? OR telefon LIKE ? OR ad_soyad LIKE ? ORDER BY id DESC LIMIT 5");
    $s->execute(["%$plaka%","%$plaka%","%$plaka%"]);
    $rows = $s->fetchAll();
    if ($rows) echo json_encode(['bulundu'=>true] + $rows[0] + ['oneriler'=>$rows]);
    else echo json_encode(['bulundu'=>false]);
} catch (Throwable $e) { echo json_encode(['bulundu'=>false,'kurulum'=>true]); }
