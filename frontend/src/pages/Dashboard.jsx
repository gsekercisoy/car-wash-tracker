import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api, { para, durumEtiket } from '../api';

export default function Dashboard() {
  const [ozet, setOzet] = useState(null);
  const [hata, setHata] = useState('');

  useEffect(() => {
    api
      .get('/api/dashboard/ozet')
      .then((r) => setOzet(r.data))
      .catch(() => setHata('Özet yüklenemedi. Backend çalışıyor mu (http://localhost:8080)?'));
  }, []);

  if (hata) return <div className="err">{hata}</div>;
  if (!ozet) return <p className="mut">Yükleniyor…</p>;

  return (
    <>
      <h1>📊 Günlük Panel</h1>
      <div className="grid g4">
        <div className="card stat"><div className="lbl">Aktif araç</div><div className="val">{ozet.aktifArac ?? 0}</div></div>
        <div className="card stat"><div className="lbl">Bugünkü ciro</div><div className="val green">{para(ozet.bugunCiro)}</div></div>
        <div className="card stat"><div className="lbl">Bugünkü gider</div><div className="val red">{para(ozet.bugunGider)}</div></div>
        <div className="card stat"><div className="lbl">Bugünkü randevu</div><div className="val">{ozet.bugunRandevu ?? 0}</div></div>
      </div>
      <div className="grid g2" style={{ gridTemplateColumns: '2fr 1fr' }}>
        <div className="card">
          <div className="between" style={{ marginBottom: 10 }}>
            <b>🔧 Sahadaki araçlar</b>
            <Link className="link" to="/araclar">+ Yeni kart</Link>
          </div>
          {!(ozet.sonKartlar ?? []).length && <p className="mut">Aktif araç yok. Yeni kart açın.</p>}
          {(ozet.sonKartlar ?? []).map((k) => (
            <Link key={k.id} to={`/araclar/${k.id}`} className="link" style={{ display: 'flex', justifyContent: 'space-between', border: '1px solid #e2e8f0', borderRadius: 9, padding: '8px 10px', marginBottom: 6, color: '#0f172a' }}>
              <b>{k.plaka}</b>
              <span className="mut">{k.musteriAd}</span>
              <span className={`badge ${k.durum}`}>{durumEtiket(k.durum)}</span>
              <b>{para(k.toplam)}</b>
            </Link>
          ))}
        </div>
        <div className="card">
          <b>⚠️ Kritik stok</b>
          {!(ozet.kritikStok ?? []).length && <p className="mut">Sorun yok.</p>}
          {(ozet.kritikStok ?? []).map((u) => (
            <div key={u.id} style={{ border: '1px solid #e2e8f0', borderRadius: 9, padding: 8, marginTop: 6, fontSize: 13 }}>
              🧴 {u.baslik ?? u.ad} — <b style={{ color: '#dc2626' }}>{u.stok} adet</b>
            </div>
          ))}
          <Link className="link" to="/randevular" style={{ display: 'block', marginTop: 10, fontSize: 13 }}>
            Bugünkü randevuları gör →
          </Link>
        </div>
      </div>
    </>
  );
}
