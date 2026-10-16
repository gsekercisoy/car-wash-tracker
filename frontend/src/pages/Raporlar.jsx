import { useEffect, useState } from 'react';
import api, { para } from '../api';

export default function Raporlar() {
  const ayBasi = new Date().toISOString().slice(0, 8) + '01';
  const bugun = new Date().toISOString().slice(0, 10);
  const [t1, setT1] = useState(ayBasi);
  const [t2, setT2] = useState(bugun);
  const [rapor, setRapor] = useState(null);
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get('/api/rapor/ozet', { params: { t1, t2 } });
      setRapor(data);
    } catch {
      setHata('Rapor yüklenemedi.');
    }
  };

  useEffect(() => {
    yukle();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <>
      <h1>📈 Raporlar</h1>
      <div className="card row">
        <input type="date" style={{ width: 170 }} value={t1} onChange={(e) => setT1(e.target.value)} />
        <input type="date" style={{ width: 170 }} value={t2} onChange={(e) => setT2(e.target.value)} />
        <button className="btn dark" onClick={yukle}>Filtrele</button>
        <button className="btn green" onClick={() => window.print()}>🖨 PDF / Yazdır</button>
      </div>
      {hata && <div className="err">{hata}</div>}
      {!rapor ? <p className="mut">Yükleniyor…</p> : (
        <>
          <div className="grid g4">
            <div className="card stat"><div className="lbl">Ciro</div><div className="val green">{para(rapor.ciro)}</div></div>
            <div className="card stat"><div className="lbl">Gider</div><div className="val red">{para(rapor.gider)}</div></div>
            <div className="card stat"><div className="lbl">Net</div><div className="val">{para(Number(rapor.ciro ?? 0) - Number(rapor.gider ?? 0))}</div></div>
            <div className="card stat"><div className="lbl">Araç sayısı</div><div className="val">{rapor.aracSayisi ?? 0}</div></div>
          </div>
          <div className="grid g2">
            <div className="card">
              <b>👷 Personel performansı</b>
              <table className="tbl" style={{ marginTop: 8 }}>
                <tbody>
                  {(rapor.personelPerformans ?? []).map((p, i) => (
                    <tr key={i}>
                      <td>{p.personel || p.ad || '—'}</td>
                      <td>{p.adet ?? p.aracSayisi ?? 0} araç</td>
                      <td style={{ textAlign: 'right', fontWeight: 700 }}>{para(p.tutar ?? p.ciro)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {!(rapor.personelPerformans ?? []).length && <p className="mut">Veri yok.</p>}
            </div>
            <div className="card">
              <b>🔥 En çok satan hizmet/ürün</b>
              <table className="tbl" style={{ marginTop: 8 }}>
                <tbody>
                  {(rapor.topKalemler ?? []).map((h, i) => (
                    <tr key={i}>
                      <td>{h.baslik}</td>
                      <td>{h.adet}</td>
                      <td style={{ textAlign: 'right', fontWeight: 700 }}>{para(h.tutar)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {!(rapor.topKalemler ?? []).length && <p className="mut">Veri yok.</p>}
            </div>
          </div>
        </>
      )}
    </>
  );
}
