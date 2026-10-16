package com.otoyikama.api.controller;

import com.otoyikama.api.entity.Musteri;
import com.otoyikama.api.repository.MusteriRepository;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@RestController
@RequestMapping("/api/musteriler")
public class MusteriController {

    private final MusteriRepository repo;

    public MusteriController(MusteriRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<Musteri> liste() {
        return repo.findTop100ByOrderByIdDesc();
    }

    @GetMapping("/{id}")
    public Musteri getir(@PathVariable Long id) {
        return repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Musteri bulunamadi"));
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public Musteri olustur(@Valid @RequestBody Musteri m) {
        m.setId(null);
        return repo.save(m);
    }

    @PutMapping("/{id}")
    public Musteri guncelle(@PathVariable Long id, @RequestBody Musteri g) {
        Musteri m = repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Musteri bulunamadi"));
        m.setAdSoyad(g.getAdSoyad());
        m.setTelefon(g.getTelefon());
        m.setPlaka(g.getPlaka());
        m.setMarka(g.getMarka());
        m.setModel(g.getModel());
        m.setRenk(g.getRenk());
        m.setNotlar(g.getNotlar());
        return repo.save(m);
    }

    @DeleteMapping("/{id}")
    public void sil(@PathVariable Long id) {
        repo.deleteById(id);
    }
}
