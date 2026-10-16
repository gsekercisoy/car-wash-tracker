package com.otoyikama.api.controller;

import com.otoyikama.api.entity.Not;
import com.otoyikama.api.repository.NotRepository;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.List;

@RestController
@RequestMapping("/api/notlar")
public class NotController {

    private final NotRepository repo;

    public NotController(NotRepository repo) {
        this.repo = repo;
    }

    @GetMapping
    public List<Not> liste() {
        return repo.findAll();
    }

    @GetMapping("/{id}")
    public Not getir(@PathVariable Long id) {
        return repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Not bulunamadi"));
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public Not olustur(@Valid @RequestBody Not n) {
        n.setId(null);
        return repo.save(n);
    }

    @PutMapping("/{id}")
    public Not guncelle(@PathVariable Long id, @RequestBody Not g) {
        Not n = repo.findById(id)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Not bulunamadi"));
        n.setBaslik(g.getBaslik());
        n.setIcerik(g.getIcerik());
        return repo.save(n);
    }

    @DeleteMapping("/{id}")
    public void sil(@PathVariable Long id) {
        repo.deleteById(id);
    }
}
