package com.otoyikama.api.controller;

import com.otoyikama.api.dto.Dtos.LoginRequest;
import com.otoyikama.api.dto.Dtos.LoginResponse;
import com.otoyikama.api.entity.User;
import com.otoyikama.api.repository.UserRepository;
import com.otoyikama.api.security.JwtUtil;
import com.otoyikama.api.security.PhpPasswordEncoder;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

@RestController
@RequestMapping("/api/auth")
public class AuthController {

    private final UserRepository users;
    private final PhpPasswordEncoder passwordEncoder;
    private final JwtUtil jwtUtil;

    public AuthController(UserRepository users, PhpPasswordEncoder passwordEncoder, JwtUtil jwtUtil) {
        this.users = users;
        this.passwordEncoder = passwordEncoder;
        this.jwtUtil = jwtUtil;
    }

    @PostMapping("/login")
    public LoginResponse login(@Valid @RequestBody LoginRequest req) {
        User u = users.findByKullaniciAdi(req.getKullaniciAdi())
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.UNAUTHORIZED, "Kullanici adi veya sifre hatali"));
        if (u.getAktif() == null || !u.getAktif()) {
            throw new ResponseStatusException(HttpStatus.UNAUTHORIZED, "Kullanici adi veya sifre hatali");
        }
        if (!passwordEncoder.matches(req.getSifre(), u.getSifre())) {
            throw new ResponseStatusException(HttpStatus.UNAUTHORIZED, "Kullanici adi veya sifre hatali");
        }
        return new LoginResponse(jwtUtil.generate(u.getKullaniciAdi(), u.getRol()),
                u.getId(), u.getAd(), u.getKullaniciAdi(), u.getRol());
    }
}
