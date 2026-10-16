package com.otoyikama.api.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "notlar")
public class Not {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    private String baslik;
    @Column(columnDefinition = "TEXT", nullable = false)
    private String icerik;
    @Column(name = "created_at", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getBaslik() { return baslik; }
    public void setBaslik(String baslik) { this.baslik = baslik; }
    public String getIcerik() { return icerik; }
    public void setIcerik(String icerik) { this.icerik = icerik; }
    public LocalDateTime getCreatedAt() { return createdAt; }
}
