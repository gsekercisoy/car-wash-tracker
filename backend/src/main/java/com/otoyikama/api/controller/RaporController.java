package com.otoyikama.api.controller;

import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.web.bind.annotation.*;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.HashMap;
import java.util.Map;

/** raporlar.php ile ayni hesaplar. */
@RestController
@RequestMapping("/api/rapor")
public class RaporController {

    private final JdbcTemplate jdbc;

    public RaporController(JdbcTemplate jdbc) {
        this.jdbc = jdbc;
    }

    @GetMapping("/ozet")
    public Map<String, Object> ozet(
            @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate t1,
            @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate t2) {
        LocalDate now = LocalDate.now();
        if (t1 == null) t1 = now.withDayOfMonth(1);
        if (t2 == null) t2 = now;

        BigDecimal ciro = jdbc.queryForObject(
                "SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gelir' AND tarih BETWEEN ? AND ?",
                BigDecimal.class, t1, t2);
        BigDecimal gider = jdbc.queryForObject(
                "SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gider' AND tarih BETWEEN ? AND ?",
                BigDecimal.class, t1, t2);
        Integer arac = jdbc.queryForObject(
                "SELECT COUNT(*) FROM arac_kartlari WHERE DATE(created_at) BETWEEN ? AND ?",
                Integer.class, t1, t2);

        Map<String, Object> m = new HashMap<>();
        m.put("ciro", ciro);
        m.put("gider", gider);
        m.put("net", ciro.subtract(gider));
        m.put("aracSayisi", arac);
        m.put("personelPerformans", jdbc.queryForList(
                "SELECT personel, COUNT(*) AS adet, COALESCE(SUM(toplam),0) AS tutar "
                        + "FROM arac_kartlari WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY personel",
                t1, t2));
        m.put("topKalemler", jdbc.queryForList(
                "SELECT k.baslik, COUNT(*) AS adet, SUM((k.birim_fiyat*k.adet)-k.indirim) AS tutar "
                        + "FROM kart_kalemleri k JOIN arac_kartlari a ON a.id=k.kart_id "
                        + "WHERE DATE(a.created_at) BETWEEN ? AND ? "
                        + "GROUP BY k.baslik ORDER BY tutar DESC LIMIT 15",
                t1, t2));
        return m;
    }
}
