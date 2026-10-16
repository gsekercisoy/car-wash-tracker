package com.otoyikama.api.controller;

import com.otoyikama.api.repository.AracKartiRepository;
import com.otoyikama.api.repository.RandevuRepository;
import com.otoyikama.api.repository.UrunRepository;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.web.bind.annotation.*;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.HashMap;
import java.util.Map;

/** dashboard.php ile ayni ozet. */
@RestController
@RequestMapping("/api/dashboard")
public class DashboardController {

    private final AracKartiRepository kartlar;
    private final RandevuRepository randevular;
    private final UrunRepository urunler;
    private final JdbcTemplate jdbc;

    public DashboardController(AracKartiRepository kartlar, RandevuRepository randevular,
                               UrunRepository urunler, JdbcTemplate jdbc) {
        this.kartlar = kartlar; this.randevular = randevular;
        this.urunler = urunler; this.jdbc = jdbc;
    }

    @GetMapping("/ozet")
    public Map<String, Object> ozet() {
        LocalDate bugun = LocalDate.now();
        BigDecimal ciro = jdbc.queryForObject(
                "SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gelir' AND tarih=?",
                BigDecimal.class, bugun);
        BigDecimal gider = jdbc.queryForObject(
                "SELECT COALESCE(SUM(tutar),0) FROM kasa_hareketler WHERE yon='gider' AND tarih=?",
                BigDecimal.class, bugun);
        Map<String, Object> m = new HashMap<>();
        m.put("aktifArac", kartlar.countByDurumNot("teslim"));
        m.put("bugunkuCiro", ciro);
        m.put("bugunkuGider", gider);
        m.put("bugunkuRandevu", randevular.countByTarihAndDurum(bugun, "bekliyor"));
        m.put("kritikStok", urunler.kritikStok());
        return m;
    }
}
