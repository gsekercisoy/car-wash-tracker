package com.otoyikama.api.controller;

import com.otoyikama.api.entity.Hizmet;
import com.otoyikama.api.repository.HizmetRepository;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@RestController
@RequestMapping("/api/hizmetler")
public class HizmetController {

    private final HizmetRepository repo;

    public HizmetController(HizmetRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<Hizmet> liste() {
        return repo.findAll();
    }

    @GetMapping("/{id}")
    public Hizmet getir(@PathVariable Long id) {
        return repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Hizmet bulunamadi"));
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public Hizmet olustur(@Valid @RequestBody Hizmet h) {
        h.setId(null);
        return repo.save(h);
    }

    @PutMapping("/{id}")
    public Hizmet guncelle(@PathVariable Long id, @RequestBody Hizmet g) {
        Hizmet h = repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Hizmet bulunamadi"));
        h.setBaslik(g.getBaslik());
        h.setFiyat(g.getFiyat());
        h.setAktif(g.getAktif());
        return repo.save(h);
    }

    @DeleteMapping("/{id}")
    public void sil(@PathVariable Long id) {
        repo.deleteById(id);
    }
}
