package com.otoyikama.api.controller;

import com.otoyikama.api.dto.Dtos.KalemRequest;
import com.otoyikama.api.dto.Dtos.KartCreateRequest;
import com.otoyikama.api.entity.AracKarti;
import com.otoyikama.api.entity.KartKalemi;
import com.otoyikama.api.repository.AracKartiRepository;
import com.otoyikama.api.service.AracKartiService;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/arac-kartlari")
public class AracKartiController {

    private final AracKartiRepository kartlar;
    private final AracKartiService servis;

    public AracKartiController(AracKartiRepository kartlar, AracKartiService servis) {
        this.kartlar = kartlar;
        this.servis = servis;
    }

    @GetMapping
    public List<AracKarti> liste(@RequestParam(defaultValue = "") String q,
                                 @RequestParam(defaultValue = "") String durum) {
        return kartlar.ara(q.trim(), durum.trim());
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public AracKarti olustur(@Valid @RequestBody KartCreateRequest req, Authentication auth) {
        return servis.yeniKart(req, auth != null ? auth.getName() : "");
    }

    @GetMapping("/{id}")
    public Map<String, Object> detay(@PathVariable Long id) {
        AracKarti k = kartlar.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Kart bulunamadi"));
        Map<String, Object> m = new HashMap<>();
        m.put("kart", k);
        m.put("kalemler", servis.kalemListesi(id));
        return m;
    }

    @PostMapping("/{id}/ilerle")
    public AracKarti ilerle(@PathVariable Long id,
                            @RequestParam(defaultValue = "nakit") String yontem,
                            Authentication auth) {
        return servis.ilerle(id, yontem, auth != null ? auth.getName() : "");
    }

    @PostMapping("/{id}/kalemler")
    @ResponseStatus(HttpStatus.CREATED)
    public KartKalemi kalemEkle(@PathVariable Long id, @Valid @RequestBody KalemRequest req) {
        return servis.kalemEkle(id, req);
    }
}
