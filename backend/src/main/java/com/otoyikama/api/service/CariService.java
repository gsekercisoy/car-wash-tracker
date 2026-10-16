package com.otoyikama.api.service;

import com.otoyikama.api.dto.Dtos.CariHareketRequest;
import com.otoyikama.api.entity.Cari;
import com.otoyikama.api.entity.CariHareket;
import com.otoyikama.api.repository.CariHareketRepository;
import com.otoyikama.api.repository.CariRepository;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.server.ResponseStatusException;

import java.math.BigDecimal;

/** PHP cari.php ile ayni: her harekette bakiye = SUM(borc) - SUM(tahsilat). */
@Service
public class CariService {

    private final CariRepository cariler;
    private final CariHareketRepository hareketler;

    public CariService(CariRepository cariler, CariHareketRepository hareketler) {
        this.cariler = cariler;
        this.hareketler = hareketler;
    }

    @Transactional
    public CariHareket hareketEkle(Long cariId, CariHareketRequest r) {
        Cari c = cariler.findById(cariId)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Cari bulunamadi"));
        if (r.tutar == null || r.tutar.compareTo(BigDecimal.ZERO) <= 0) {
            throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Tutar pozitif olmali");
        }
        if (!"borc".equals(r.yon) && !"tahsilat".equals(r.yon)) {
            throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "yon borc veya tahsilat olmali");
        }
        CariHareket h = new CariHareket();
        h.setCariId(cariId);
        h.setYon(r.yon);
        h.setTutar(r.tutar);
        h.setAciklama(r.aciklama != null ? r.aciklama : "");
        h = hareketler.save(h);

        c.setBakiye(hareketler.bakiyeHesapla(cariId));
        cariler.save(c);
        return h;
    }
}
