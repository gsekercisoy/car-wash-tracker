package com.otoyikama.api.service;

import com.otoyikama.api.dto.Dtos.KalemRequest;
import com.otoyikama.api.dto.Dtos.KartCreateRequest;
import com.otoyikama.api.entity.*;
import com.otoyikama.api.repository.*;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.server.ResponseStatusException;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.List;
import java.util.Map;
import java.util.concurrent.ThreadLocalRandom;

/**
 * PHP is mantigi (araclar.php + arac-detay.php) ile birebir:
 * yeni kartta 4 haneli teslim kodu + kabul SMS'i; cagrildi'da hazir SMS'i;
 * teslimde kasa geliri; urun kaleminde stok dusumu.
 */
@Service
public class AracKartiService {

    private static final Map<String, String> SONRAKI =
            Map.of("kabul", "islemde", "islemde", "bitti", "bitti", "cagrildi", "cagrildi", "teslim");

    private final AracKartiRepository kartlar;
    private final KartKalemiRepository kalemler;
    private final MusteriRepository musteriler;
    private final HizmetRepository hizmetler;
    private final UrunRepository urunler;
    private final KasaRepository kasa;
    private final SmsLogRepository sms;
    private final AyarRepository ayarlar;

    public AracKartiService(AracKartiRepository kartlar, KartKalemiRepository kalemler,
                            MusteriRepository musteriler, HizmetRepository hizmetler,
                            UrunRepository urunler, KasaRepository kasa,
                            SmsLogRepository sms, AyarRepository ayarlar) {
        this.kartlar = kartlar; this.kalemler = kalemler; this.musteriler = musteriler;
        this.hizmetler = hizmetler; this.urunler = urunler; this.kasa = kasa;
        this.sms = sms; this.ayarlar = ayarlar;
    }

    private String ayar(String anahtar, String varsayilan) {
        return ayarlar.findById(anahtar).map(Ayar::getDeger).orElse(varsayilan);
    }

    private String doldur(String sablon, String ad, String plaka, String kod) {
        return sablon.replace("{ad}", ad).replace("{plaka}", plaka).replace("{kod}", kod);
    }

    public void toplamGuncelle(Long kartId) {
        BigDecimal t = kalemler.toplam(kartId);
        AracKarti k = kartlar.findById(kartId)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Kart bulunamadi"));
        k.setToplam(t);
        kartlar.save(k);
    }

    @Transactional
    public AracKarti yeniKart(KartCreateRequest r, String personelAd) {
        if (r.plaka == null || r.plaka.isBlank()) {
            throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Plaka zorunludur");
        }
        String plaka = r.plaka.trim().toUpperCase().replaceAll("\\s+", " ");
        Musteri kayitli = musteriler.findFirstByPlakaOrderByIdDesc(plaka).orElse(null);

        String ad = r.musteriAd != null ? r.musteriAd.trim() : "";
        String tel = r.telefon != null ? r.telefon.trim() : "";
        String marka = r.marka != null ? r.marka : (kayitli != null ? kayitli.getMarka() : "");
        String model = r.model != null ? r.model : (kayitli != null ? kayitli.getModel() : "");
        if (kayitli != null && ad.isEmpty()) {
            ad = kayitli.getAdSoyad();
            if (tel.isEmpty()) tel = kayitli.getTelefon();
        }
        String kod = String.format("%04d", ThreadLocalRandom.current().nextInt(1000, 10000));

        AracKarti k = new AracKarti();
        k.setPlaka(plaka);
        k.setMusteriAd(ad);
        k.setTelefon(tel);
        k.setMarka(marka);
        k.setModel(model);
        k.setRenk(r.renk != null ? r.renk : "");
        k.setPersonel(personelAd != null ? personelAd : "");
        k.setTeslimKodu(kod);
        k.setNotlar(r.notlar);
        k.setDurum("kabul");
        k.setToplam(BigDecimal.ZERO);
        k.setOdenen(BigDecimal.ZERO);
        k.setOdemeYontemi("nakit");
        k = kartlar.save(k);

        if (kayitli == null) {
            Musteri m = new Musteri();
            m.setAdSoyad(ad.isEmpty() ? "Misafir" : ad);
            m.setTelefon(tel);
            m.setPlaka(plaka);
            m.setMarka(marka);
            m.setModel(model);
            m.setRenk(r.renk != null ? r.renk : "");
            musteriler.save(m);
        }

        if (!tel.isEmpty()) {
            String sablon = ayar("sms_sablonu_kabul",
                    "Sayin {ad}, {plaka} plakali araciniz teslim alindi. Teslim kodunuz: {kod}");
            sms.save(new SmsLog(tel, doldur(sablon, ad.isEmpty() ? "Musterimiz" : ad, plaka, kod), "kabul"));
        }
        return k;
    }

    @Transactional
    public KartKalemi kalemEkle(Long kartId, KalemRequest r) {
        kartlar.findById(kartId)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Kart bulunamadi"));
        KartKalemi kl = new KartKalemi();
        kl.setKartId(kartId);
        if ("hizmet".equals(r.tur)) {
            Hizmet h = hizmetler.findById(r.refId)
                    .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Hizmet bulunamadi"));
            kl.setTur("hizmet");
            kl.setRefId(h.getId());
            kl.setBaslik(h.getBaslik());
            kl.setAdet(1);
            kl.setBirimFiyat(h.getFiyat());
            kl.setIndirim(r.indirim != null ? r.indirim : BigDecimal.ZERO);
        } else if ("urun".equals(r.tur)) {
            int adet = (r.adet == null || r.adet < 1) ? 1 : r.adet;
            Urun u = urunler.findById(r.refId)
                    .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Urun bulunamadi"));
            if (u.getStok() == null || u.getStok() < adet) {
                throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Yetersiz stok!");
            }
            kl.setTur("urun");
            kl.setRefId(u.getId());
            kl.setBaslik(u.getBaslik());
            kl.setAdet(adet);
            kl.setBirimFiyat(u.getFiyat());
            kl.setIndirim(BigDecimal.ZERO);
            u.setStok(u.getStok() - adet);
            urunler.save(u);
        } else {
            throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "tur hizmet veya urun olmali");
        }
        kl = kalemler.save(kl);
        toplamGuncelle(kartId);
        return kl;
    }

    @Transactional
    public void kalemSil(Long kalemId) {
        KartKalemi kl = kalemler.findById(kalemId)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Kalem bulunamadi"));
        Long kartId = kl.getKartId();
        kalemler.delete(kl);
        toplamGuncelle(kartId);
    }

    @Transactional
    public AracKarti ilerle(Long kartId, String yontem, String personelAd) {
        AracKarti k = kartlar.findById(kartId)
                .orElseThrow(() -> new ResponseStatusException(HttpStatus.NOT_FOUND, "Kart bulunamadi"));
        String yeni = SONRAKI.getOrDefault(k.getDurum(), "teslim");
        k.setDurum(yeni);
        kartlar.save(k);

        if ("cagrildi".equals(yeni) && k.getTelefon() != null && !k.getTelefon().isEmpty()) {
            String sablon = ayar("sms_sablonu_hazir",
                    "Sayin {ad}, {plaka} plakali araciniz hazir. Teslim kodu: {kod}");
            sms.save(new SmsLog(k.getTelefon(),
                    doldur(sablon, k.getMusteriAd(), k.getPlaka(), k.getTeslimKodu()), "hazir"));
        }
        if ("teslim".equals(yeni)) {
            BigDecimal t = kalemler.toplam(kartId);
            String ym = (yontem == null || yontem.isBlank()) ? "nakit" : yontem;
            k.setToplam(t);
            k.setOdenen(t);
            k.setOdemeYontemi(ym);
            kartlar.save(k);
            KasaHareket kh = new KasaHareket();
            kh.setYon("gelir");
            kh.setBaslik("Arac teslim: " + k.getPlaka());
            kh.setTutar(t);
            kh.setYontem(ym);
            kh.setKartId(kartId);
            kh.setPersonel(personelAd != null ? personelAd : "");
            kh.setTarih(LocalDate.now());
            kasa.save(kh);
        }
        return k;
    }

    public List<KartKalemi> kalemListesi(Long kartId) {
        return kalemler.findByKartId(kartId);
    }
}
