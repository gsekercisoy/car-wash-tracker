package com.otoyikama.api.repository;

import com.otoyikama.api.entity.Urun;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import java.util.List;

public interface UrunRepository extends JpaRepository<Urun, Long> {
    @Query(value = "SELECT * FROM urunler WHERE stok <= min_stok", nativeQuery = true)
    List<Urun> kritikStok();
}
