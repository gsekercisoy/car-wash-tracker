package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.math.BigDecimal;
import java.time.LocalDate;
import java.time.LocalDateTime;

@Entity
@Table(name = "kasa_hareketler")
public class KasaHareket {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String yon;
    private String baslik;
    private BigDecimal tutar;
    private String yontem;
    @Column(name = "kart_id")
    private Long kartId;
    private String personel;
    private LocalDate tarih;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getYon() { return yon; }
    public void setYon(String yon) { this.yon = yon; }
    public String getBaslik() { return baslik; }
    public void setBaslik(String baslik) { this.baslik = baslik; }
    public BigDecimal getTutar() { return tutar; }
    public void setTutar(BigDecimal tutar) { this.tutar = tutar; }
    public String getYontem() { return yontem; }
    public void setYontem(String yontem) { this.yontem = yontem; }
    public Long getKartId() { return kartId; }
    public void setKartId(Long kartId) { this.kartId = kartId; }
    public String getPersonel() { return personel; }
    public void setPersonel(String personel) { this.personel = personel; }
    public LocalDate getTarih() { return tarih; }
    public void setTarih(LocalDate tarih) { this.tarih = tarih; }
    public LocalDateTime getCreatedAt() { return createdAt; }
}
