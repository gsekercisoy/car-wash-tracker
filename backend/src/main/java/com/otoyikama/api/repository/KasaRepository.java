package com.otoyikama.api.repository;

import com.otoyikama.api.entity.KasaHareket;
import org.springframework.data.jpa.repository.JpaRepository;
import java.time.LocalDate;
import java.util.List;

public interface KasaRepository extends JpaRepository<KasaHareket, Long> {
    List<KasaHareket> findTop200ByTarihBetweenOrderByTarihDescIdDesc(LocalDate t1, LocalDate t2);
}
