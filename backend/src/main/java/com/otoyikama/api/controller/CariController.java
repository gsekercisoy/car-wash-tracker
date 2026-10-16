package com.otoyikama.api.controller;

import com.otoyikama.api.dto.Dtos.CariHareketRequest;
import com.otoyikama.api.entity.Cari;
import com.otoyikama.api.entity.CariHareket;
import com.otoyikama.api.repository.CariHareketRepository;
import com.otoyikama.api.repository.CariRepository;
import com.otoyikama.api.service.CariService;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@RestController
@RequestMapping("/api/cariler")
public class CariController {

    private final CariRepository repo;
    private final CariHareketRepository hareketRepo;
    private final CariService servis;

    public CariController(CariRepository repo, CariHareketRepository hareketRepo, CariService servis) {
        this.repo = repo;
        this.hareketRepo = hareketRepo;
        this.servis = servis;
    }

    @GetMapping
    public List<Cari> liste() {
        return repo.findAll();
    }

    @GetMapping("/{id}")
    public Cari getir(@PathVariable Long id) {
        return repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Cari bulunamadi"));
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public Cari olustur(@Valid @RequestBody Cari c) {
        c.setId(null);
        return repo.save(c);
    }

    @PutMapping("/{id}")
    public Cari guncelle(@PathVariable Long id, @RequestBody Cari g) {
        Cari c = repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Cari bulunamadi"));
        c.setTip(g.getTip());
        c.setUnvan(g.getUnvan());
        c.setTelefon(g.getTelefon());
        return repo.save(c);
    }

    @DeleteMapping("/{id}")
    public void sil(@PathVariable Long id) {
        repo.deleteById(id);
    }

    @GetMapping("/{id}/hareketler")
    public List<CariHareket> hareketler(@PathVariable Long id) {
        return hareketRepo.findByCariIdOrderByIdDesc(id);
    }

    @PostMapping("/{id}/hareket")
    @ResponseStatus(HttpStatus.CREATED)
    public CariHareket hareketEkle(@PathVariable Long id, @Valid @RequestBody CariHareketRequest r) {
        return servis.hareketEkle(id, r);
    }
}
