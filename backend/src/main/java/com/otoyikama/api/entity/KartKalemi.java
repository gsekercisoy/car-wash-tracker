package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.math.BigDecimal;

@Entity
@Table(name = "kart_kalemleri")
public class KartKalemi {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    @Column(name = "kart_id", nullable = false)
    private Long kartId;
    private String tur;
    @Column(name = "ref_id")
    private Long refId;
    private String baslik;
    private Integer adet;
    @Column(name = "birim_fiyat")
    private BigDecimal birimFiyat;
    private BigDecimal indirim;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public Long getKartId() { return kartId; }
    public void setKartId(Long kartId) { this.kartId = kartId; }
    public String getTur() { return tur; }
    public void setTur(String tur) { this.tur = tur; }
    public Long getRefId() { return refId; }
    public void setRefId(Long refId) { this.refId = refId; }
    public String getBaslik() { return baslik; }
    public void setBaslik(String baslik) { this.baslik = baslik; }
    public Integer getAdet() { return adet; }
    public void setAdet(Integer adet) { this.adet = adet; }
    public BigDecimal getBirimFiyat() { return birimFiyat; }
    public void setBirimFiyat(BigDecimal birimFiyat) { this.birimFiyat = birimFiyat; }
    public BigDecimal getIndirim() { return indirim; }
    public void setIndirim(BigDecimal indirim) { this.indirim = indirim; }
}
