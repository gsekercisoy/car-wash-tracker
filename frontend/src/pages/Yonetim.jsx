import { useEffect, useState } from 'react';
import api from '../api';
import { useAuth } from '../AuthContext';

function SmsLog() {
  const [rows, setRows] = useState([]);
  useEffect(() => {
    api.get('/api/sms-log').then((r) => setRows(Array.isArray(r.data) ? r.data : [])).catch(() => {});
  }, []);
  return (
    <div className="card" style={{ overflowX: 'auto' }}>
      <b>📩 SMS geçmişi</b>
      <table className="tbl" style={{ marginTop: 8 }}>
        <thead><tr><th>Tarih</th><th>Telefon</th><th>Mesaj</th><th>Durum</th></tr></thead>
        <tbody>
          {rows.map((s) => (
            <tr key={s.id}>
              <td className="mut">{s.createdAt ?? s.tarih ?? ''}</td>
              <td><b>{s.telefon}</b></td>
              <td>{s.mesaj ?? s.icerik}</td>
              <td><span className="badge">{s.durum ?? s.tip ?? '—'}</span></td>
            </tr>
          ))}
        </tbody>
      </table>
      {!rows.length && <p className="mut">Kayıt yok.</p>}
    </div>
  );
}

function Kullanicilar() {
  const [rows, setRows] = useState([]);
  const [form, setForm] = useState({ ad: '', kullaniciAdi: '', sifre: '', rol: 'personel' });
  const [hata, setHata] = useState('');

  const yukle = async () => {
    try {
      const { data } = await api.get('/api/kullanicilar');
      setRows(Array.isArray(data) ? data : []);
    } catch {
      setHata('Kullanıcılar yüklenemedi (admin yetkisi gerekli olabilir).');
    }
  };

  useEffect(() => {
    yukle();
  }, []);

  const kaydet = async (e) => {
    e.preventDefault();
    try {
      await api.post('/api/kullanicilar', form);
      setForm({ ad: '', kullaniciAdi: '', sifre: '', rol: 'personel' });
      await yukle();
    } catch {
      setHata('Kaydedilemedi.');
    }
  };

  const sil = async (id) => {
    if (!window.confirm('Silinsin mi?')) return;
    try {
      await api.delete(`/api/kullanicilar/${id}`);
      await yukle();
    } catch {
      setHata('Silinemedi.');
    }
  };

  return (
    <div className="grid g2" style={{ gridTemplateColumns: '1fr 2fr' }}>
      <form className="card" onSubmit={kaydet}>
        <b>+ Kullanıcı</b>
        <div className="grid" style={{ marginTop: 8 }}>
          <input placeholder="Ad" value={form.ad} onChange={(e) => setForm((f) => ({ ...f, ad: e.target.value }))} required />
          <input placeholder="Kullanıcı adı" value={form.kullaniciAdi} onChange={(e) => setForm((f) => ({ ...f, kullaniciAdi: e.target.value }))} required />
          <input placeholder="Şifre" type="password" value={form.sifre} onChange={(e) => setForm((f) => ({ ...f, sifre: e.target.value }))} required />
          <select value={form.rol} onChange={(e) => setForm((f) => ({ ...f, rol: e.target.value }))}>
            <option value="personel">personel</option>
            <option value="admin">admin</option>
          </select>
          <button className="btn primary">Kaydet</button>
        </div>
      </form>
      <div className="card">
        {hata && <div className="err">{hata}</div>}
        <table className="tbl">
          <tbody>
            {rows.map((u) => (
              <tr key={u.id}>
                <td><b>{u.ad}</b> <span className="mut">({u.kullaniciAdi} · {u.rol})</span></td>
                <td style={{ textAlign: 'right' }}><button className="btn danger sm" onClick={() => sil(u.id)}>Sil</button></td>
              </tr>
            ))}
          </tbody>
        </table>
        {!rows.length && <p className="mut">Kayıt yok.</p>}
      </div>
    </div>
  );
}

function Ayarlar() {
  const [ayarlar, setAyarlar] = useState({});
  const [hata, setHata] = useState('');
  const [ok, setOk] = useState('');

  useEffect(() => {
    api.get('/api/ayarlar').then((r) => setAyarlar(r.data ?? {})).catch(() => setHata('Ayarlar yüklenemedi.'));
  }, []);

  const kaydet = async (e) => {
    e.preventDefault();
    setOk('');
    try {
      await api.put('/api/ayarlar', ayarlar);
      setOk('Ayarlar kaydedildi.');
    } catch {
      setHata('Kaydedilemedi.');
    }
  };

  return (
    <form className="card" onSubmit={kaydet}>
      <b>⚙️ Ayarlar</b>
      {hata && <div className="err" style={{ marginTop: 8 }}>{hata}</div>}
      {ok && <div className="ok-msg" style={{ marginTop: 8 }}>{ok}</div>}
      <div className="grid" style={{ marginTop: 8 }}>
        {Object.keys(ayarlar).length === 0 && <p className="mut">Ayar yok veya yüklenemedi.</p>}
        {Object.entries(ayarlar).map(([k, v]) => (
          <label key={k} style={{ fontSize: 13 }}>
            <span className="mut">{k}</span>
            <input value={v ?? ''} onChange={(e) => setAyarlar((a) => ({ ...a, [k]: e.target.value }))} />
          </label>
        ))}
        <button className="btn primary">Kaydet</button>
      </div>
    </form>
  );
}

export default function Yonetim() {
  const { isAdmin } = useAuth();
  const [sekme, setSekme] = useState('sms');

  return (
    <>
      <h1>⚙️ Yönetim</h1>
      <div className="tabs">
        <button className={sekme === 'sms' ? 'active' : ''} onClick={() => setSekme('sms')}>📩 SMS Log</button>
        {isAdmin && <button className={sekme === 'kullanici' ? 'active' : ''} onClick={() => setSekme('kullanici')}>👤 Kullanıcılar</button>}
        {isAdmin && <button className={sekme === 'ayar' ? 'active' : ''} onClick={() => setSekme('ayar')}>⚙️ Ayarlar</button>}
      </div>
      {!isAdmin && sekme !== 'sms' && <div className="err">Bu sekmeler admin yetkisi gerektirir.</div>}
      {sekme === 'sms' && <SmsLog />}
      {sekme === 'kullanici' && isAdmin && <Kullanicilar />}
      {sekme === 'ayar' && isAdmin && <Ayarlar />}
    </>
  );
}
