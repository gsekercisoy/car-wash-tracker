package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.math.BigDecimal;
import java.time.LocalDateTime;

@Entity
@Table(name = "cari_hareketler")
public class CariHareket {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    @Column(name = "cari_id", nullable = false)
    private Long cariId;
    private String yon;
    private BigDecimal tutar;
    private String aciklama;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public Long getCariId() { return cariId; }
    public void setCariId(Long cariId) { this.cariId = cariId; }
    public String getYon() { return yon; }
    public void setYon(String yon) { this.yon = yon; }
    public BigDecimal getTutar() { return tutar; }
    public void setTutar(BigDecimal tutar) { this.tutar = tutar; }
    public String getAciklama() { return aciklama; }
    public void setAciklama(String aciklama) { this.aciklama = aciklama; }
    public LocalDateTime getCreatedAt() { return createdAt; }
}
