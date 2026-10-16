import { useEffect, useState } from 'react';
import api, { para } from '../api';

const bugun = () => new Date().toISOString().slice(0, 10);
const ayBasi = () => new Date().toISOString().slice(0, 8) + '01';

export default function Kasa() {
  const [rows, setRows] = useState([]);
  const [t1, setT1] = useState(ayBasi());
  const [t2, setT2] = useState(bugun());
  const [form, setForm] = useState({ yon: 'gelir', baslik: '', tutar: '', yontem: 'nakit', tarih: bugun() });
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get('/api/kasa', { params: { t1, t2 } });
      setRows(Array.isArray(data) ? data : (data.hareketler ?? []));
    } catch {
      setHata('Kasa yüklenemedi.');
    }
  };

  useEffect(() => {
    yukle();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const kaydet = async (e) => {
    e.preventDefault();
    try {
      await api.post('/api/kasa', { ...form, tutar: Number(form.tutar) });
      setForm({ yon: 'gelir', baslik: '', tutar: '', yontem: 'nakit', tarih: bugun() });
      await yukle();
    } catch {
      setHata('Kaydedilemedi.');
    }
  };

  const gelir = rows.filter((r) => r.yon === 'gelir').reduce((s, r) => s + Number(r.tutar ?? 0), 0);
  const gider = rows.filter((r) => r.yon === 'gider').reduce((s, r) => s + Number(r.tutar ?? 0), 0);

  const set = (k) => (e) => setForm((f) => ({ ...f, [k]: e.target.value }));

  return (
    <>
      <h1>💰 Kasa / Gelir-Gider</h1>
      {hata && <div className="err">{hata}</div>}
      <div className="grid g3">
        <div className="card" style={{ background: '#dcfce7', fontWeight: 800 }}>Gelir<br /><span style={{ fontSize: 24 }}>{para(gelir)}</span></div>
        <div className="card" style={{ background: '#fee2e2', fontWeight: 800 }}>Gider<br /><span style={{ fontSize: 24 }}>{para(gider)}</span></div>
        <div className="card" style={{ fontWeight: 800 }}>Net<br /><span style={{ fontSize: 24 }}>{para(gelir - gider)}</span></div>
      </div>
      <div className="grid g2" style={{ gridTemplateColumns: '1fr 2fr' }}>
        <form className="card" onSubmit={kaydet}>
          <b>+ Gelir / Gider ekle</b>
          <div className="grid" style={{ marginTop: 8 }}>
            <select value={form.yon} onChange={set('yon')}>
              <option value="gelir">Gelir</option>
              <option value="gider">Gider</option>
            </select>
            <input placeholder="Başlık (kira, deterjan…)" value={form.baslik} onChange={set('baslik')} required />
            <input type="number" step="0.01" placeholder="Tutar" value={form.tutar} onChange={set('tutar')} required />
            <div className="row">
              <select value={form.yontem} onChange={set('yontem')}>
                <option value="nakit">Nakit</option>
                <option value="kart">Kart</option>
                <option value="havale">Havale</option>
              </select>
              <input type="date" value={form.tarih} onChange={set('tarih')} />
            </div>
            <button className="btn primary">Kaydet</button>
          </div>
        </form>
        <div className="card">
          <div className="row" style={{ marginBottom: 10 }}>
            <input type="date" style={{ width: 160 }} value={t1} onChange={(e) => setT1(e.target.value)} />
            <input type="date" style={{ width: 160 }} value={t2} onChange={(e) => setT2(e.target.value)} />
            <button className="btn dark" onClick={yukle}>Filtrele</button>
          </div>
          <table className="tbl">
            <thead><tr><th>Tarih</th><th>Başlık</th><th style={{ textAlign: 'right' }}>Tutar</th></tr></thead>
            <tbody>
              {rows.map((r) => (
                <tr key={r.id}>
                  <td className="mut">{r.tarih}</td>
                  <td>{r.yon === 'gelir' ? '🟢' : '🔴'} {r.baslik} <span className="mut">({r.yontem} · {r.personel})</span></td>
                  <td style={{ textAlign: 'right', fontWeight: 800, color: r.yon === 'gelir' ? '#16a34a' : '#dc2626' }}>
                    {r.yon === 'gider' ? '-' : ''}{para(r.tutar)}
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
