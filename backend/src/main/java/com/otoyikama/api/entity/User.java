package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "users")
public class User {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String ad;
    @Column(name = "kullanici_adi", unique = true, nullable = false)
    private String kullaniciAdi;
    private String sifre;
    private String rol;
    @Column(columnDefinition = "TEXT")
    private String yetkiler;
    private Boolean aktif;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getAd() { return ad; }
    public void setAd(String ad) { this.ad = ad; }
    public String getKullaniciAdi() { return kullaniciAdi; }
    public void setKullaniciAdi(String kullaniciAdi) { this.kullaniciAdi = kullaniciAdi; }
    public String getSifre() { return sifre; }
    public void setSifre(String sifre) { this.sifre = sifre; }
    public String getRol() { return rol; }
    public void setRol(String rol) { this.rol = rol; }
    public String getYetkiler() { return yetkiler; }
    public void setYetkiler(String yetkiler) { this.yetkiler = yetkiler; }
    public Boolean getAktif() { return aktif; }
    public void setAktif(Boolean aktif) { this.aktif = aktif; }
    public LocalDateTime getCreatedAt() { return createdAt; }
}
