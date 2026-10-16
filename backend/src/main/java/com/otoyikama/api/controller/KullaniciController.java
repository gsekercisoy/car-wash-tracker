package com.otoyikama.api.controller;

import com.otoyikama.api.dto.Dtos.KullaniciRequest;
import com.otoyikama.api.entity.User;
import com.otoyikama.api.repository.UserRepository;
import com.otoyikama.api.security.PhpPasswordEncoder;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.*;

import java.util.List;

/** Sadece admin (kullanicilar.php'deki admin_gerekli() karsiligi). Sifreler listede donulmez. */
@RestController
@RequestMapping("/api/kullanicilar")
@PreAuthorize("hasRole('admin')")
public class KullaniciController {

    private final UserRepository repo;
    private final PhpPasswordEncoder passwordEncoder;

    public KullaniciController(UserRepository repo, PhpPasswordEncoder passwordEncoder) {
        this.repo = repo;
        this.passwordEncoder = passwordEncoder;
    }

    @GetMapping
    public List<User> liste() {
        List<User> hepsi = repo.findAll();
        hepsi.forEach(u -> u.setSifre(null));
        return hepsi;
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public User olustur(@Valid @RequestBody KullaniciRequest r) {
        User u = new User();
        u.setAd(r.ad);
        u.setKullaniciAdi(r.kullaniciAdi);
        u.setSifre(passwordEncoder.encode(r.sifre));
        u.setRol(r.rol != null ? r.rol : "personel");
        u.setAktif(true);
        u = repo.save(u);
        u.setSifre(null);
        return u;
    }
}
