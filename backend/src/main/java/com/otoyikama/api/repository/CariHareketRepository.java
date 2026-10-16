package com.otoyikama.api.repository;

import com.otoyikama.api.entity.CariHareket;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import java.math.BigDecimal;
import java.util.List;

public interface CariHareketRepository extends JpaRepository<CariHareket, Long> {
    List<CariHareket> findTop100ByOrderByIdDesc();
    List<CariHareket> findByCariIdOrderByIdDesc(Long cariId);

    // PHP: bakiye = SUM(borc) - SUM(tahsilat)
    @Query("SELECT COALESCE(SUM(CASE WHEN h.yon = 'borc' THEN h.tutar ELSE -h.tutar END), 0) "
            + "FROM CariHareket h WHERE h.cariId = :cariId")
    BigDecimal bakiyeHesapla(@Param("cariId") Long cariId);
}
