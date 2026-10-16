package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "musteriler")
public class Musteri {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    @Column(name = "ad_soyad", nullable = false)
    private String adSoyad;
    private String telefon;
    private String plaka;
    private String marka;
    private String model;
    private String renk;
    @Column(columnDefinition = "TEXT")
    private String notlar;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getAdSoyad() { return adSoyad; }
    public void setAdSoyad(String adSoyad) { this.adSoyad = adSoyad; }
    public String getTelefon() { return telefon; }
    public void setTelefon(String telefon) { this.telefon = telefon; }
    public String getPlaka() { return plaka; }
    public void setPlaka(String plaka) { this.plaka = plaka; }
    public String getMarka() { return marka; }
    public void setMarka(String marka) { this.marka = marka; }
    public String getModel() { return model; }
    public void setModel(String model) { this.model = model; }
    public String getRenk() { return renk; }
    public void setRenk(String renk) { this.renk = renk; }
    public String getNotlar() { return notlar; }
    public void setNotlar(String notlar) { this.notlar = notlar; }
    public LocalDateTime getCreatedAt() { return createdAt; }
}
