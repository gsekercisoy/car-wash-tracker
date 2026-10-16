import { useEffect, useState } from 'react';
import api from '../api';

export default function Notlar() {
  const [rows, setRows] = useState([]);
  const [form, setForm] = useState({ baslik: '', icerik: '' });
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get('/api/notlar');
      setRows(Array.isArray(data) ? data : []);
    } catch {
      setHata('Notlar yüklenemedi.');
    }
  };

  useEffect(() => {
    yukle();
  }, []);

  const kaydet = async (e) => {
    e.preventDefault();
    try {
      await api.post('/api/notlar', form);
      setForm({ baslik: '', icerik: '' });
      await yukle();
    } catch {
      setHata('Kaydedilemedi.');
    }
  };

  const sil = async (id) => {
    if (!window.confirm('Silinsin mi?')) return;
    try {
      await api.delete(`/api/notlar/${id}`);
      await yukle();
    } catch {
      setHata('Silinemedi.');
    }
  };

  return (
    <>
      <h1>📝 Notlar</h1>
      {hata && <div className="err">{hata}</div>}
      <form className="card grid g2" onSubmit={kaydet}>
        <input placeholder="Başlık" value={form.baslik} onChange={(e) => setForm((f) => ({ ...f, baslik: e.target.value }))} required />
        <div className="row">
          <input placeholder="İçerik" value={form.icerik} onChange={(e) => setForm((f) => ({ ...f, icerik: e.target.value }))} style={{ flex: 1 }} />
          <button className="btn primary">+ Ekle</button>
        </div>
      </form>
      <div className="grid g3">
        {rows.map((n) => (
          <div className="card" key={n.id}>
            <div className="between">
              <b>{n.baslik}</b>
              <button className="btn danger sm" onClick={() => sil(n.id)}>Sil</button>
            </div>
            <p className="mut">{n.icerik}</p>
            <span className="mut">{n.createdAt ?? n.tarih ?? ''}</span>
          </div>
        ))}
      </div>
      {!rows.length && <p className="mut">Henüz not yok.</p>}
    </>
  );
}
