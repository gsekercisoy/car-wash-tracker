package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "sms_log")
public class SmsLog {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String telefon;
    @Column(columnDefinition = "TEXT", nullable = false)
    private String mesaj;
    private String tip;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    public SmsLog() {}
    public SmsLog(String telefon, String mesaj, String tip) {
        this.telefon = telefon;
        this.mesaj = mesaj;
        this.tip = tip;
    }

    public Long getId() { return id; }
    public String getTelefon() { return telefon; }
    public void setTelefon(String telefon) { this.telefon = telefon; }
    public String getMesaj() { return mesaj; }
    public void setMesaj(String mesaj) { this.mesaj = mesaj; }
    public String getTip() { return tip; }
    public void setTip(String tip) { this.tip = tip; }
    public LocalDateTime getCreatedAt() { return createdAt; }
}
