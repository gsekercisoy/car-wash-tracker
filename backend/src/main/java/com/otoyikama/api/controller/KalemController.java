package com.otoyikama.api.controller;

import com.otoyikama.api.service.AracKartiService;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

@RestController
@RequestMapping("/api/kalemler")
public class KalemController {

    private final AracKartiService servis;

    public KalemController(AracKartiService servis) {
        this.servis = servis;
    }

    @DeleteMapping("/{id}")
    public Map<String, String> sil(@PathVariable Long id) {
        servis.kalemSil(id);
        return Map.of("sonuc", "silindi");
    }
}
