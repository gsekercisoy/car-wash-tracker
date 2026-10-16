package com.otoyikama.api.repository;

import com.otoyikama.api.entity.Musteri;
import org.springframework.data.jpa.repository.JpaRepository;
import java.util.List;
import java.util.Optional;

public interface MusteriRepository extends JpaRepository<Musteri, Long> {
    Optional<Musteri> findFirstByPlakaOrderByIdDesc(String plaka);
    List<Musteri> findTop100ByOrderByIdDesc();
}
