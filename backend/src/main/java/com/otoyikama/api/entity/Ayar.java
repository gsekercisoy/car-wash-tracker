package com.otoyikama.api.entity;

import jakarta.persistence.*;

@Entity
@Table(name = "ayarlar")
public class Ayar {
    @Id
    private String anahtar;
    @Column(columnDefinition = "TEXT")
    private String deger;

    public Ayar() {}
    public Ayar(String anahtar, String deger) {
        this.anahtar = anahtar;
        this.deger = deger;
    }

    public String getAnahtar() { return anahtar; }
    public void setAnahtar(String anahtar) { this.anahtar = anahtar; }
    public String getDeger() { return deger; }
    public void setDeger(String deger) { this.deger = deger; }
}
