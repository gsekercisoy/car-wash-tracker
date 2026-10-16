package com.otoyikama.api.controller;

import com.otoyikama.api.dto.Dtos.RandevuRequest;
import com.otoyikama.api.entity.Randevu;
import com.otoyikama.api.repository.RandevuRepository;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@RestController
@RequestMapping("/api/randevular")
public class RandevuController {

    private final RandevuRepository repo;

    public RandevuController(RandevuRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<Randevu> liste() {
        return repo.findAll();
    }

    @GetMapping("/{id}")
    public Randevu getir(@PathVariable Long id) {
        return repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Randevu bulunamadi"));
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public Randevu olustur(@Valid @RequestBody RandevuRequest r) {
        Randevu e = new Randevu();
        kopyala(e, r);
        if (e.getDurum() == null) e.setDurum("bekliyor");
        return repo.save(e);
    }

    @PutMapping("/{id}")
    public Randevu guncelle(@PathVariable Long id, @RequestBody RandevuRequest r) {
        Randevu e = repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Randevu bulunamadi"));
        kopyala(e, r);
        return repo.save(e);
    }

    @DeleteMapping("/{id}")
    public void sil(@PathVariable Long id) {
        repo.deleteById(id);
    }

    private void kopyala(Randevu e, RandevuRequest r) {
        e.setAdSoyad(r.adSoyad);
        e.setTelefon(r.telefon);
        e.setPlaka(r.plaka);
        e.setTarih(r.tarih);
        e.setSaat(r.saat);
        if (r.durum != null) e.setDurum(r.durum);
        e.setNotlar(r.notlar);
    }
}
