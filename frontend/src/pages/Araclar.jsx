import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api, { para, durumEtiket } from '../api';

const DURUMLAR = ['', 'kabul', 'islemde', 'bitti', 'cagrildi', 'teslim'];

export default function Araclar() {
  const [kartlar, setKartlar] = useState([]);
  const [q, setQ] = useState('');
  const [durum, setDurum] = useState('');
  const [hata, setHata] = useState('');
  const [form, setForm] = useState({ plaka: '', musteriAd: '', telefon: '', marka: '', model: '', renk: '', notlar: '' });

  const yukle = async () => {
    setHata('');
    try {
      const { data } = await api.get('/api/arac-kartlari', { params: { q, durum } });
      setKartlar(Array.isArray(data) ? data : []);
    } catch {
      setHata('Kartlar yüklenemedi.');
    }
  };

  useEffect(() => {
    const t = setTimeout(yukle, q ? 350 : 0);
    return () => clearTimeout(t);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [q, durum]);

  const ac = async (e) => {
    e.preventDefault();
    if (!form.plaka.trim()) return setHata('Plaka zorunludur.');
    try {
      const { data } = await api.post('/api/arac-kartlari', form);
      setForm({ plaka: '', musteriAd: '', telefon: '', marka: '', model: '', renk: '', notlar: '' });
      await yukle();
      if (data?.id) window.location.hash = '';
      return data;
    } catch {
      setHata('Kart açılamadı.');
    }
  };

  const set = (k) => (e) => setForm((f) => ({ ...f, [k]: e.target.value }));

  return (
    <>
      <div className="between">
        <h1>🧾 Araç Kartları</h1>
        <div className="row">
          <input style={{ width: 210 }} placeholder="Plaka / ad / telefon…" value={q} onChange={(e) => setQ(e.target.value)} />
          <select style={{ width: 150 }} value={durum} onChange={(e) => setDurum(e.target.value)}>
            {DURUMLAR.map((d) => (
              <option key={d} value={d}>{d ? durumEtiket(d) : 'Tümü'}</option>
            ))}
          </select>
          <button className="btn dark" onClick={yukle}>Ara</button>
        </div>
      </div>
      {hata && <div className="err">{hata}</div>}

      <div className="card">
        <b>+ Yeni araç kartı (plaka zorunlu)</b>
        <form onSubmit={ac} className="grid g3" style={{ marginTop: 8 }}>
          <input placeholder="34 ABC 123" value={form.plaka} onChange={set('plaka')} style={{ textTransform: 'uppercase', fontWeight: 800 }} />
          <input placeholder="Müşteri adı" value={form.musteriAd} onChange={set('musteriAd')} />
          <input placeholder="Telefon" value={form.telefon} onChange={set('telefon')} />
          <input placeholder="Marka" value={form.marka} onChange={set('marka')} />
          <input placeholder="Model" value={form.model} onChange={set('model')} />
          <input placeholder="Renk" value={form.renk} onChange={set('renk')} />
          <input placeholder="Not (opsiyonel)" value={form.notlar} onChange={set('notlar')} style={{ gridColumn: '1 / -1' }} />
          <button className="btn primary" style={{ gridColumn: '1 / -1' }}>Kartı Aç</button>
        </form>
      </div>

      <div className="card" style={{ overflowX: 'auto' }}>
        <table className="tbl">
          <thead><tr><th>Plaka</th><th>Müşteri</th><th>Durum</th><th>Toplam</th><th></th></tr></thead>
          <tbody>
            {kartlar.map((k) => (
              <tr key={k.id}>
                <td><b>{k.plaka}</b><br /><span className="mut">Kod: {k.teslimKodu}</span></td>
                <td>{k.musteriAd}<br /><span className="mut">{k.telefon}</span></td>
                <td><span className={`badge ${k.durum}`}>{durumEtiket(k.durum)}</span></td>
                <td><b>{para(k.toplam)}</b></td>
                <td><Link className="btn dark sm" to={`/araclar/${k.id}`}>Aç →</Link></td>
              </tr>
            ))}
          </tbody>
        </table>
        {!kartlar.length && <p className="mut">Kayıt yok.</p>}
      </div>
    </>
  );
}
