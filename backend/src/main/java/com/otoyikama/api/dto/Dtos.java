package com.otoyikama.api.dto;

import jakarta.validation.constraints.NotBlank;
import java.math.BigDecimal;
import java.time.LocalDate;
import java.time.LocalTime;

/** Tum istek/cevap DTO'lari (JSON camelCase). */
public class Dtos {

    public static class LoginRequest {
        @NotBlank public String kullaniciAdi;
        @NotBlank public String sifre;
        public String getKullaniciAdi() { return kullaniciAdi; }
        public void setKullaniciAdi(String v) { this.kullaniciAdi = v; }
        public String getSifre() { return sifre; }
        public void setSifre(String v) { this.sifre = v; }
    }

    public static class LoginResponse {
        public String token;
        public Long id;
        public String ad;
        public String kullaniciAdi;
        public String rol;
        public LoginResponse(String token, Long id, String ad, String kullaniciAdi, String rol) {
            this.token = token; this.id = id; this.ad = ad;
            this.kullaniciAdi = kullaniciAdi; this.rol = rol;
        }
    }

    public static class KartCreateRequest {
        @NotBlank public String plaka;
        public String musteriAd;
        public String telefon;
        public String marka;
        public String model;
        public String renk;
        public String notlar;
    }

    public static class KalemRequest {
        @NotBlank public String tur; // hizmet | urun
        public Long refId;
        public Integer adet = 1;
        public BigDecimal indirim = BigDecimal.ZERO;
    }

    public static class CariHareketRequest {
        @NotBlank public String yon; // borc | tahsilat
        public BigDecimal tutar;
        public String aciklama;
    }

    public static class KasaRequest {
        @NotBlank public String yon; // gelir | gider
        @NotBlank public String baslik;
        public BigDecimal tutar;
        public String yontem = "nakit";
        public String personel;
        public LocalDate tarih;
    }

    public static class RandevuRequest {
        @NotBlank public String adSoyad;
        @NotBlank public String telefon;
        public String plaka;
        public LocalDate tarih;
        public LocalTime saat;
        public String durum = "bekliyor";
        public String notlar;
    }

    public static class KullaniciRequest {
        @NotBlank public String ad;
        @NotBlank public String kullaniciAdi;
        @NotBlank public String sifre;
        public String rol = "personel";
    }
}
