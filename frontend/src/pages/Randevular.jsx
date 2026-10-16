import { useEffect, useState } from 'react';
import api from '../api';

const BOS = { adSoyad: '', telefon: '', plaka: '', tarih: new Date().toISOString().slice(0, 10), saat: '', notlar: '' };

export default function Randevular() {
  const [rows, setRows] = useState([]);
  const [form, setForm] = useState(BOS);
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get('/api/randevular');
      setRows(Array.isArray(data) ? data : []);
    } catch {
      setHata('Randevular yüklenemedi.');
    }
  };

  useEffect(() => {
    yukle();
  }, []);

  const kaydet = async (e) => {
    e.preventDefault();
    try {
      await api.post('/api/randevular', form);
      setForm(BOS);
      await yukle();
    } catch {
      setHata('Kaydedilemedi.');
    }
  };

  const durumDegis = async (id, durum) => {
    try {
      await api.put(`/api/randevular/${id}`, { durum });
      await yukle();
    } catch {
      setHata('Durum güncellenemedi.');
    }
  };

  const sil = async (id) => {
    if (!window.confirm('Silinsin mi?')) return;
    try {
      await api.delete(`/api/randevular/${id}`);
      await yukle();
    } catch {
      setHata('Silinemedi.');
    }
  };

  const set = (k) => (e) => setForm((f) => ({ ...f, [k]: e.target.value }));

  return (
    <>
      <h1>📅 Randevular</h1>
      {hata && <div className="err">{hata}</div>}
      <div className="grid g2" style={{ gridTemplateColumns: '1fr 2fr' }}>
        <form className="card" onSubmit={kaydet}>
          <b>+ Manuel randevu</b>
          <div className="grid" style={{ marginTop: 8 }}>
            <input placeholder="Ad Soyad" value={form.adSoyad} onChange={set('adSoyad')} required />
            <input placeholder="Telefon" value={form.telefon} onChange={set('telefon')} required />
            <input placeholder="Plaka" value={form.plaka} onChange={set('plaka')} />
            <div className="row">
              <input type="date" value={form.tarih} onChange={set('tarih')} required />
              <input type="time" value={form.saat} onChange={set('saat')} required />
            </div>
            <input placeholder="Not" value={form.notlar} onChange={set('notlar')} />
            <button className="btn primary">Kaydet</button>
          </div>
        </form>
        <div className="card" style={{ overflowX: 'auto' }}>
          <table className="tbl">
            <thead><tr><th>Tarih</th><th>Müşteri</th><th>Durum</th><th></th></tr></thead>
            <tbody>
              {rows.map((r) => (
                <tr key={r.id}>
                  <td><b>{r.tarih} {(r.saat ?? '').slice(0, 5)}</b></td>
                  <td>{r.adSoyad}<br /><span className="mut">{r.telefon} · {r.plaka}</span></td>
                  <td><span className="badge">{r.durum}</span></td>
                  <td className="row">
                    <select style={{ width: 130 }} value={r.durum ?? 'bekliyor'} onChange={(e) => durumDegis(r.id, e.target.value)}>
                      <option value="bekliyor">bekliyor</option>
                      <option value="tamamlandi">tamamlandi</option>
                      <option value="iptal">iptal</option>
                    </select>
                    <button className="btn danger sm" onClick={() => sil(r.id)}>Sil</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
          {!rows.length && <p className="mut">Kayıt yok.</p>}
        </div>
      </div>
    </>
  );
}
