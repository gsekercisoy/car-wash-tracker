package com.otoyikama.api.repository;

import com.otoyikama.api.entity.KartKalemi;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import java.math.BigDecimal;
import java.util.List;

public interface KartKalemiRepository extends JpaRepository<KartKalemi, Long> {
    List<KartKalemi> findByKartId(Long kartId);

    @Query("SELECT COALESCE(SUM((k.birimFiyat * k.adet) - k.indirim), 0) FROM KartKalemi k WHERE k.kartId = :kartId")
    BigDecimal toplam(@Param("kartId") Long kartId);
}
