import { useEffect, useState } from 'react';
import api, { para } from '../api';

export default function Cari() {
  const [cariler, setCariler] = useState([]);
  const [hareketler, setHareketler] = useState([]);
  const [yeni, setYeni] = useState({ tip: 'musteri', unvan: '', telefon: '' });
  const [hareket, setHareket] = useState({ cariId: '', yon: 'borc', tutar: '', aciklama: '' });
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get('/api/cariler');
      if (Array.isArray(data)) {
        setCariler(data);
        setHareketler([]);
      } else {
        setCariler(data.cariler ?? []);
        setHareketler(data.hareketler ?? []);
      }
    } catch {
      setHata('Cari verisi yüklenemedi.');
    }
  };

  useEffect(() => {
    yukle();
  }, []);

  const cariAc = async (e) => {
    e.preventDefault();
    try {
      await api.post('/api/cariler', { tip: yeni.tip, unvan: yeni.unvan, telefon: yeni.telefon });
      setYeni({ tip: 'musteri', unvan: '', telefon: '' });
      await yukle();
    } catch {
      setHata('Cari açılamadı.');
    }
  };

  const hareketIsle = async (e) => {
    e.preventDefault();
    try {
      await api.post(`/api/cariler/${hareket.cariId}/hareket`, {
        yon: hareket.yon,
        tutar: Number(hareket.tutar),
        aciklama: hareket.aciklama
      });
      setHareket({ cariId: '', yon: 'borc', tutar: '', aciklama: '' });
      await yukle();
    } catch {
      setHata('Hareket işlenemedi.');
    }
  };

  return (
    <>
      <h1>💳 Cari Takip</h1>
      {hata && <div className="err">{hata}</div>}
      <div className="grid g2" style={{ gridTemplateColumns: '1fr 2fr' }}>
        <div className="grid">
          <form className="card" onSubmit={cariAc}>
            <b>+ Cari aç</b>
            <div className="grid" style={{ marginTop: 8 }}>
              <select value={yeni.tip} onChange={(e) => setYeni((f) => ({ ...f, tip: e.target.value }))}>
                <option value="musteri">Müşteri</option>
                <option value="tedarikci">Tedarikçi</option>
                <option value="personel">Personel</option>
              </select>
              <input placeholder="Unvan" value={yeni.unvan} onChange={(e) => setYeni((f) => ({ ...f, unvan: e.target.value }))} required />
              <input placeholder="Telefon" value={yeni.telefon} onChange={(e) => setYeni((f) => ({ ...f, telefon: e.target.value }))} />
              <button className="btn primary">Kaydet</button>
            </div>
          </form>
          <form className="card" onSubmit={hareketIsle}>
            <b>+ Hareket</b>
            <div className="grid" style={{ marginTop: 8 }}>
              <select value={hareket.cariId} onChange={(e) => setHareket((f) => ({ ...f, cariId: e.target.value }))} required>
                <option value="">Cari seçin…</option>
                {cariler.map((c) => (
                  <option key={c.id} value={c.id}>{c.unvan} ({para(c.bakiye)})</option>
                ))}
              </select>
              <select value={hareket.yon} onChange={(e) => setHareket((f) => ({ ...f, yon: e.target.value }))}>
                <option value="borc">Borç (alacak)</option>
                <option value="tahsilat">Tahsilat</option>
              </select>
              <input type="number" step="0.01" placeholder="Tutar" value={hareket.tutar} onChange={(e) => setHareket((f) => ({ ...f, tutar: e.target.value }))} required />
              <input placeholder="Açıklama" value={hareket.aciklama} onChange={(e) => setHareket((f) => ({ ...f, aciklama: e.target.value }))} />
              <button className="btn green">İşle</button>
            </div>
          </form>
        </div>
        <div className="grid">
          <div className="card">
            <b>Cariler</b>
            <table className="tbl" style={{ marginTop: 8 }}>
              <tbody>
                {cariler.map((c) => (
                  <tr key={c.id}>
                    <td>{c.unvan} <span className="mut">({c.tip})</span></td>
                    <td style={{ textAlign: 'right', fontWeight: 800, color: Number(c.bakiye) > 0 ? '#dc2626' : '#16a34a' }}>{para(c.bakiye)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
            {!cariler.length && <p className="mut">Kayıt yok.</p>}
          </div>
          {!!hareketler.length && (
            <div className="card">
              <b>Son hareketler</b>
              <table className="tbl" style={{ marginTop: 8 }}>
                <thead><tr><th>Cari</th><th>Yön</th><th style={{ textAlign: 'right' }}>Tutar</th></tr></thead>
                <tbody>
                  {hareketler.map((h) => (
                    <tr key={h.id}>
                      <td>{h.unvan ?? h.cariUnvan}<br /><span className="mut">{h.aciklama}</span></td>
                      <td>{h.yon}</td>
                      <td style={{ textAlign: 'right', fontWeight: 700 }}>{para(h.tutar)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </>
  );
}
