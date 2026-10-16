package com.otoyikama.api.controller;

import com.otoyikama.api.dto.Dtos.KasaRequest;
import com.otoyikama.api.entity.KasaHareket;
import com.otoyikama.api.repository.KasaRepository;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.time.LocalDate;
import java.util.List;

@RestController
@RequestMapping("/api/kasa")
public class KasaController {

    private final KasaRepository repo;

    public KasaController(KasaRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<KasaHareket> liste(@RequestParam(required = false) LocalDate t1,
                                   @RequestParam(required = false) LocalDate t2) {
        LocalDate now = LocalDate.now();
        return repo.findTop200ByTarihBetweenOrderByTarihDescIdDesc(
                t1 != null ? t1 : now.withDayOfMonth(1),
                t2 != null ? t2 : now);
    }

    @GetMapping("/{id}")
    public KasaHareket getir(@PathVariable Long id) {
        return repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Kayit bulunamadi"));
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public KasaHareket olustur(@Valid @RequestBody KasaRequest r) {
        KasaHareket k = new KasaHareket();
        k.setYon(r.yon);
        k.setBaslik(r.baslik);
        k.setTutar(r.tutar);
        k.setYontem(r.yontem != null ? r.yontem : "nakit");
        k.setPersonel(r.personel != null ? r.personel : "");
        k.setTarih(r.tarih != null ? r.tarih : LocalDate.now());
        return repo.save(k);
    }

    @PutMapping("/{id}")
    public KasaHareket guncelle(@PathVariable Long id, @RequestBody KasaRequest r) {
        KasaHareket k = repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Kayit bulunamadi"));
        k.setYon(r.yon);
        k.setBaslik(r.baslik);
        k.setTutar(r.tutar);
        if (r.yontem != null) k.setYontem(r.yontem);
        if (r.tarih != null) k.setTarih(r.tarih);
        return repo.save(k);
    }

    @DeleteMapping("/{id}")
    public void sil(@PathVariable Long id) {
        repo.deleteById(id);
    }
}
