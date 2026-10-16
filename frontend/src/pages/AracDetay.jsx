import { useCallback, useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import api, { para, durumEtiket } from '../api';

const BUTON = {
  kabul: ['islemde', '▶ Aracı İşleme Al'],
  islemde: ['bitti', '✅ İşlemi Bitir'],
  bitti: ['cagrildi', '📞 Müşteriyi Çağır'],
  cagrildi: ['teslim', '💰 Ödemeyi Al, Teslim Et']
};

export default function AracDetay() {
  const { id } = useParams();
  const [kart, setKart] = useState(null);
  const [hizmetler, setHizmetler] = useState([]);
  const [urunler, setUrunler] = useState([]);
  const [hata, setHata] = useState('');
  const [kalem, setKalem] = useState({ tur: 'hizmet', refId: '', adet: 1, indirim: 0 });

  const yukle = useCallback(async () => {
    try {
      const [{ data: k }, h, u] = await Promise.all([
        api.get(`/api/arac-kartlari/${id}`),
        api.get('/api/hizmetler').catch(() => ({ data: [] })),
        api.get('/api/urunler').catch(() => ({ data: [] }))
      ]);
      setKart(k);
      setHizmetler(Array.isArray(h.data) ? h.data : []);
      setUrunler(Array.isArray(u.data) ? u.data : []);
    } catch {
      setHata('Kart yüklenemedi.');
    }
  }, [id]);

  useEffect(() => {
    yukle();
  }, [yukle]);

  const ilerle = async (yontem) => {
    setHata('');
    try {
      const { data } = await api.post(`/api/arac-kartlari/${id}/ilerle`, null, { params: { yontem } });
      setKart(data);
    } catch {
      setHata('Durum ilerletilemedi.');
    }
  };

  const kalemEkle = async (e) => {
    e.preventDefault();
    if (!kalem.refId) return setHata('Hizmet/ürün seçin.');
    try {
      await api.post(`/api/arac-kartlari/${id}/kalemler`, {
        tur: kalem.tur,
        refId: Number(kalem.refId),
        adet: Number(kalem.adet) || 1,
        indirim: Number(kalem.indirim) || 0
      });
      setKalem({ tur: 'hizmet', refId: '', adet: 1, indirim: 0 });
      await yukle();
    } catch {
      setHata('Kalem eklenemedi (stok yetersiz olabilir).');
    }
  };

  const kalemSil = async (kid) => {
    try {
      await api.delete(`/api/kalemler/${kid}`);
      await yukle();
    } catch {
      setHata('Kalem silinemedi.');
    }
  };

  if (!kart && !hata) return <p className="mut">Yükleniyor…</p>;
  if (!kart) return <div className="err">{hata}</div>;

  const kalemler = kart.kalemler ?? kart.items ?? [];
  const btn = BUTON[kart.durum];

  return (
    <>
      <Link className="link" to="/araclar">← Kartlara dön</Link>
      <h1 style={{ marginTop: 6 }}>
        {kart.plaka} <span className={`badge ${kart.durum}`}>{durumEtiket(kart.durum)}</span>
      </h1>
      <p className="mut">
        {kart.musteriAd} · {kart.telefon} · {`${kart.marka ?? ''} ${kart.model ?? ''}`.trim()} · Teslim kodu:{' '}
        <span className="kod">{kart.teslimKodu}</span>
      </p>
      {hata && <div className="err">{hata}</div>}

      {btn ? (
        <div className="row no-print" style={{ marginBottom: 12 }}>
          <button className="btn green" onClick={() => ilerle('nakit')}>{btn[1]} (Nakit)</button>
          {kart.durum === 'cagrildi' && <button className="btn dark" onClick={() => ilerle('kart')}>💳 Kart ile Teslim</button>}
          <button className="btn amber" onClick={() => ilerle('veresiye')}>📒 Veresiye Teslim</button>
        </div>
      ) : (
        <div className="ok-msg">
          ✅ Teslim edildi — {para(kart.odenen ?? kart.toplam)} ({kart.odemeYontemi ?? 'nakit'})
          <button className="btn dark sm" style={{ marginLeft: 10 }} onClick={() => window.print()}>🖨 Fiş yazdır</button>
        </div>
      )}

      <div className="grid g2">
        <div className="card">
          <b>Kalemler — Toplam: {para(kart.toplam)}</b>
          <table className="tbl" style={{ marginTop: 8 }}>
            <tbody>
              {kalemler.map((kl) => (
                <tr key={kl.id}>
                  <td>{kl.tur === 'hizmet' ? '✂️' : '🧴'} {kl.baslik} × {kl.adet}</td>
                  <td style={{ textAlign: 'right', fontWeight: 700 }}>{para((kl.birimFiyat ?? kl.birimFiyat) * kl.adet - (kl.indirim ?? 0))}</td>
                  <td><button className="btn danger sm" onClick={() => kalemSil(kl.id)}>sil</button></td>
                </tr>
              ))}
            </tbody>
          </table>
          {!kalemler.length && <p className="mut">Henüz hizmet/ürün eklenmedi.</p>}
        </div>
        <div className="card no-print">
          <b>Hizmet / Ürün ekle</b>
          <form onSubmit={kalemEkle} className="grid" style={{ marginTop: 8 }}>
            <select value={kalem.tur} onChange={(e) => setKalem((k) => ({ ...k, tur: e.target.value, refId: '' }))}>
              <option value="hizmet">Hizmet</option>
              <option value="urun">Ürün</option>
            </select>
            <select value={kalem.refId} onChange={(e) => setKalem((k) => ({ ...k, refId: e.target.value }))}>
              <option value="">Seçin…</option>
              {(kalem.tur === 'hizmet' ? hizmetler : urunler).map((x) => (
                <option key={x.id} value={x.id}>
                  {x.baslik ?? x.ad} — {para(x.birimFiyat ?? x.fiyat)}{kalem.tur === 'urun' ? ` (stok:${x.stok})` : ''}
                </option>
              ))}
            </select>
            <div className="row">
              {kalem.tur === 'urun'
                ? <input type="number" min="1" value={kalem.adet} onChange={(e) => setKalem((k) => ({ ...k, adet: e.target.value }))} placeholder="Adet" />
                : <input type="number" step="0.01" min="0" value={kalem.indirim} onChange={(e) => setKalem((k) => ({ ...k, indirim: e.target.value }))} placeholder="İndirim ₺" />}
              <button className="btn primary">+ Ekle</button>
            </div>
          </form>
        </div>
      </div>
    </>
  );
}
