package com.otoyikama.api.controller;

import com.otoyikama.api.entity.Ayar;
import com.otoyikama.api.repository.AyarRepository;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/ayarlar")
public class AyarController {

    private final AyarRepository repo;

    public AyarController(AyarRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<Ayar> liste() {
        return repo.findAll();
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public Ayar kaydet(@RequestBody Ayar a) {
        return repo.save(a);
    }

    @PutMapping("/{anahtar}")
    public Ayar guncelle(@PathVariable String anahtar, @RequestBody Map<String, String> govde) {
        Ayar a = repo.findById(anahtar)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Ayar bulunamadi"));
        a.setDeger(govde.get("deger"));
        return repo.save(a);
    }
}
