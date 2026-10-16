package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.math.BigDecimal;

@Entity
@Table(name = "urunler")
public class Urun {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String baslik;
    private BigDecimal fiyat;
    private Integer stok;
    @Column(name = "min_stok")
    private Integer minStok;
    private Boolean aktif;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getBaslik() { return baslik; }
    public void setBaslik(String baslik) { this.baslik = baslik; }
    public BigDecimal getFiyat() { return fiyat; }
    public void setFiyat(BigDecimal fiyat) { this.fiyat = fiyat; }
    public Integer getStok() { return stok; }
    public void setStok(Integer stok) { this.stok = stok; }
    public Integer getMinStok() { return minStok; }
    public void setMinStok(Integer minStok) { this.minStok = minStok; }
    public Boolean getAktif() { return aktif; }
    public void setAktif(Boolean aktif) { this.aktif = aktif; }
}
