package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.math.BigDecimal;
import java.time.LocalDateTime;

@Entity
@Table(name = "arac_kartlari")
public class AracKarti {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String plaka;
    @Column(name = "musteri_id")
    private Long musteriId;
    @Column(name = "musteri_ad")
    private String musteriAd;
    private String telefon;
    private String marka;
    private String model;
    private String renk;
    private String durum;
    private String personel;
    @Column(name = "teslim_kodu", nullable = false)
    private String teslimKodu;
    private BigDecimal toplam;
    private BigDecimal odenen;
    @Column(name = "odeme_yontemi")
    private String odemeYontemi;
    @Column(columnDefinition = "TEXT")
    private String notlar;
    private String foto;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;
    @Column(name = "updated_at", insertable = false, updatable = false)
    private LocalDateTime updatedAt;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getPlaka() { return plaka; }
    public void setPlaka(String plaka) { this.plaka = plaka; }
    public Long getMusteriId() { return musteriId; }
    public void setMusteriId(Long musteriId) { this.musteriId = musteriId; }
    public String getMusteriAd() { return musteriAd; }
    public void setMusteriAd(String musteriAd) { this.musteriAd = musteriAd; }
    public String getTelefon() { return telefon; }
    public void setTelefon(String telefon) { this.telefon = telefon; }
    public String getMarka() { return marka; }
    public void setMarka(String marka) { this.marka = marka; }
    public String getModel() { return model; }
    public void setModel(String model) { this.model = model; }
    public String getRenk() { return renk; }
    public void setRenk(String renk) { this.renk = renk; }
    public String getDurum() { return durum; }
    public void setDurum(String durum) { this.durum = durum; }
    public String getPersonel() { return personel; }
    public void setPersonel(String personel) { this.personel = personel; }
    public String getTeslimKodu() { return teslimKodu; }
    public void setTeslimKodu(String teslimKodu) { this.teslimKodu = teslimKodu; }
    public BigDecimal getToplam() { return toplam; }
    public void setToplam(BigDecimal toplam) { this.toplam = toplam; }
    public BigDecimal getOdenen() { return odenen; }
    public void setOdenen(BigDecimal odenen) { this.odenen = odenen; }
    public String getOdemeYontemi() { return odemeYontemi; }
    public void setOdemeYontemi(String odemeYontemi) { this.odemeYontemi = odemeYontemi; }
    public String getNotlar() { return notlar; }
    public void setNotlar(String notlar) { this.notlar = notlar; }
    public String getFoto() { return foto; }
    public void setFoto(String foto) { this.foto = foto; }
    public LocalDateTime getCreatedAt() { return createdAt; }
    public LocalDateTime getUpdatedAt() { return updatedAt; }
}
