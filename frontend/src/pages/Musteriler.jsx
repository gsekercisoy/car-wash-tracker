import { useEffect, useState } from 'react';
import api from '../api';

const BOS = { adSoyad: '', telefon: '', plaka: '', marka: '', model: '', renk: '' };

export default function Musteriler() {
  const [rows, setRows] = useState([]);
  const [form, setForm] = useState(BOS);
  const [editId, setEditId] = useState(null);
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get('/api/musteriler');
      setRows(Array.isArray(data) ? data : []);
    } catch {
      setHata('Müşteriler yüklenemedi.');
    }
  };

  useEffect(() => {
    yukle();
  }, []);

  const kaydet = async (e) => {
    e.preventDefault();
    try {
      if (editId) await api.put(`/api/musteriler/${editId}`, form);
      else await api.post('/api/musteriler', form);
      setForm(BOS);
      setEditId(null);
      await yukle();
    } catch {
      setHata('Kaydedilemedi.');
    }
  };

  const sil = async (id) => {
    if (!window.confirm('Silinsin mi?')) return;
    try {
      await api.delete(`/api/musteriler/${id}`);
      await yukle();
    } catch {
      setHata('Silinemedi.');
    }
  };

  const set = (k) => (e) => setForm((f) => ({ ...f, [k]: e.target.value }));

  return (
    <>
      <h1>👥 Müşteriler</h1>
      {hata && <div className="err">{hata}</div>}
      <div className="grid g2" style={{ gridTemplateColumns: '1fr 2fr' }}>
        <form className="card" onSubmit={kaydet}>
          <b>{editId ? 'Müşteriyi düzenle' : '+ Yeni müşteri'}</b>
          <div className="grid" style={{ marginTop: 8 }}>
            <input placeholder="Ad Soyad" value={form.adSoyad} onChange={set('adSoyad')} required />
            <input placeholder="Telefon" value={form.telefon} onChange={set('telefon')} />
            <input placeholder="Plaka" value={form.plaka} onChange={set('plaka')} style={{ textTransform: 'uppercase' }} />
            <div className="row">
              <input placeholder="Marka" value={form.marka} onChange={set('marka')} />
              <input placeholder="Model" value={form.model} onChange={set('model')} />
            </div>
            <input placeholder="Renk" value={form.renk} onChange={set('renk')} />
            <div className="row">
              <button className="btn primary">Kaydet</button>
              {editId && <button type="button" className="btn dark" onClick={() => { setEditId(null); setForm(BOS); }}>Vazgeç</button>}
            </div>
          </div>
        </form>
        <div className="card" style={{ overflowX: 'auto' }}>
          <table className="tbl">
            <thead><tr><th>Ad Soyad</th><th>Telefon / Plaka</th><th>Araç</th><th></th></tr></thead>
            <tbody>
              {rows.map((m) => (
                <tr key={m.id}>
                  <td><b>{m.adSoyad}</b></td>
                  <td>{m.telefon}<br /><span className="mut">{m.plaka}</span></td>
                  <td className="mut">{`${m.marka ?? ''} ${m.model ?? ''} ${m.renk ?? ''}`.trim() || '—'}</td>
                  <td className="row">
                    <button className="btn dark sm" onClick={() => { setEditId(m.id); setForm({ adSoyad: m.adSoyad ?? '', telefon: m.telefon ?? '', plaka: m.plaka ?? '', marka: m.marka ?? '', model: m.model ?? '', renk: m.renk ?? '' }); }}>Düzenle</button>
                    <button className="btn danger sm" onClick={() => sil(m.id)}>Sil</button>
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
