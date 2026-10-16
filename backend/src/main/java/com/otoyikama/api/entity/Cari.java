package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.math.BigDecimal;
import java.time.LocalDateTime;

@Entity
@Table(name = "cariler")
public class Cari {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String tip;
    private String unvan;
    private String telefon;
    private BigDecimal bakiye;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getTip() { return tip; }
    public void setTip(String tip) { this.tip = tip; }
    public String getUnvan() { return unvan; }
    public void setUnvan(String unvan) { this.unvan = unvan; }
    public String getTelefon() { return telefon; }
    public void setTelefon(String telefon) { this.telefon = telefon; }
    public BigDecimal getBakiye() { return bakiye; }
    public void setBakiye(BigDecimal bakiye) { this.bakiye = bakiye; }
    public LocalDateTime getCreatedAt() { return createdAt; }
}
