package com.otoyikama.api.repository;

import com.otoyikama.api.entity.Randevu;
import org.springframework.data.jpa.repository.JpaRepository;
import java.time.LocalDate;

public interface RandevuRepository extends JpaRepository<Randevu, Long> {
    long countByTarihAndDurum(LocalDate tarih, String durum);
}
