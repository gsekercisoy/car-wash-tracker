package com.otoyikama.api.repository;

import com.otoyikama.api.entity.AracKarti;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import java.util.List;

public interface AracKartiRepository extends JpaRepository<AracKarti, Long> {
    long countByDurumNot(String durum);

    @Query(value = "SELECT * FROM arac_kartlari WHERE "
            + "(:q = '' OR plaka LIKE CONCAT('%', :q, '%') OR musteri_ad LIKE CONCAT('%', :q, '%') OR telefon LIKE CONCAT('%', :q, '%')) "
            + "AND (:durum = '' OR durum = :durum) ORDER BY id DESC LIMIT 100", nativeQuery = true)
    List<AracKarti> ara(@Param("q") String q, @Param("durum") String durum);
}
