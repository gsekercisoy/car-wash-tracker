import { useEffect, useState } from 'react';
import api, { para } from '../api';

function Crud({ baslik, endpoint, alanlar, bos, goster }) {
  const [rows, setRows] = useState([]);
  const [form, setForm] = useState(bos);
  const [editId, setEditId] = useState(null);
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get(endpoint);
      setRows(Array.isArray(data) ? data : []);
    } catch {
      setHata(`${baslik} yüklenemedi.`);
    }
  };

  useEffect(() => {
    yukle();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [endpoint]);

  const kaydet = async (e) => {
    e.preventDefault();
    try {
      if (editId) await api.put(`${endpoint}/${editId}`, form);
      else await api.post(endpoint, form);
      setForm(bos);
      setEditId(null);
      await yukle();
    } catch {
      setHata('Kaydedilemedi.');
    }
  };

  const sil = async (id) => {
    if (!window.confirm('Silinsin mi?')) return;
    try {
      await api.delete(`${endpoint}/${id}`);
      await yukle();
    } catch {
      setHata('Silinemedi.');
    }
  };

  return (
    <div className="grid g2" style={{ gridTemplateColumns: '1fr 2fr' }}>
      <form className="card" onSubmit={kaydet}>
        <b>{editId ? 'Düzenle' : '+ Yeni'}</b>
        <div className="grid" style={{ marginTop: 8 }}>
          {alanlar.map((a) => (
            <input
              key={a.k}
              type={a.tip ?? 'text'}
              step={a.tip === 'number' ? '0.01' : undefined}
              placeholder={a.label}
              value={form[a.k] ?? ''}
              onChange={(e) => setForm((f) => ({ ...f, [a.k]: e.target.value }))}
              required={!!a.zorunlu}
            />
          ))}
          <div className="row">
            <button className="btn primary">Kaydet</button>
            {editId && <button type="button" className="btn dark" onClick={() => { setEditId(null); setForm(bos); }}>Vazgeç</button>}
          </div>
        </div>
      </form>
      <div className="card" style={{ overflowX: 'auto' }}>
        {hata && <div className="err">{hata}</div>}
        <table className="tbl">
          <tbody>
            {rows.map((r) => (
              <tr key={r.id}>
                <td>{goster(r)}</td>
                <td className="row" style={{ justifyContent: 'flex-end' }}>
                  <button className="btn dark sm" onClick={() => { setEditId(r.id); setForm(Object.fromEntries(alanlar.map((a) => [a.k, r[a.k] ?? '']))); }}>Düzenle</button>
                  <button className="btn danger sm" onClick={() => sil(r.id)}>Sil</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        {!rows.length && <p className="mut">Kayıt yok.</p>}
      </div>
    </div>
  );
}

export default function Tanimlar() {
  const [sekme, setSekme] = useState('hizmet');

  return (
    <>
      <h1>🧰 Hizmet & Ürün Tanımları</h1>
      <div className="tabs">
        <button className={sekme === 'hizmet' ? 'active' : ''} onClick={() => setSekme('hizmet')}>✂️ Hizmetler</button>
        <button className={sekme === 'urun' ? 'active' : ''} onClick={() => setSekme('urun')}>🧴 Ürünler</button>
      </div>
      {sekme === 'hizmet' ? (
        <Crud
          baslik="Hizmetler"
          endpoint="/api/hizmetler"
          bos={{ baslik: '', fiyat: '', sureDakika: '' }}
          alanlar={[
            { k: 'baslik', label: 'Başlık', zorunlu: true },
            { k: 'fiyat', label: 'Fiyat ₺', tip: 'number', zorunlu: true },
            { k: 'sureDakika', label: 'Süre (dk)', tip: 'number' }
          ]}
          goster={(r) => (<><b>{r.baslik}</b> — {para(r.birimFiyat ?? r.fiyat)} <span className="mut">{r.sureDakika ? `· ${r.sureDakika} dk` : ''}</span></>)}
        />
      ) : (
        <Crud
          baslik="Ürünler"
          endpoint="/api/urunler"
          bos={{ baslik: '', fiyat: '', stok: '', minStok: '' }}
          alanlar={[
            { k: 'baslik', label: 'Başlık', zorunlu: true },
            { k: 'fiyat', label: 'Fiyat ₺', tip: 'number', zorunlu: true },
            { k: 'stok', label: 'Stok', tip: 'number' },
            { k: 'minStok', label: 'Min. stok', tip: 'number' }
          ]}
          goster={(r) => (<><b>{r.baslik}</b> — {para(r.birimFiyat ?? r.fiyat)} <span className="mut">· stok: {r.stok}{Number(r.stok) <= Number(r.minStok ?? 0) ? ' ⚠️' : ''}</span></>)}
        />
      )}
    </>
  );
}
