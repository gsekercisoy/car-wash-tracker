package com.otoyikama.api.controller;

import com.otoyikama.api.entity.Urun;
import com.otoyikama.api.repository.UrunRepository;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@RestController
@RequestMapping("/api/urunler")
public class UrunController {

    private final UrunRepository repo;

    public UrunController(UrunRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<Urun> liste() {
        return repo.findAll();
    }

    @GetMapping("/{id}")
    public Urun getir(@PathVariable Long id) {
        return repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Urun bulunamadi"));
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public Urun olustur(@Valid @RequestBody Urun u) {
        u.setId(null);
        return repo.save(u);
    }

    @PutMapping("/{id}")
    public Urun guncelle(@PathVariable Long id, @RequestBody Urun g) {
        Urun u = repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Urun bulunamadi"));
        u.setBaslik(g.getBaslik());
        u.setFiyat(g.getFiyat());
        u.setStok(g.getStok());
        u.setMinStok(g.getMinStok());
        u.setAktif(g.getAktif());
        return repo.save(u);
    }

    @DeleteMapping("/{id}")
    public void sil(@PathVariable Long id) {
        repo.deleteById(id);
    }
}
