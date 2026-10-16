import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function Login() {
  const { login } = useAuth();
  const nav = useNavigate();
  const [kullaniciAdi, setK] = useState('');
  const [sifre, setS] = useState('');
  const [hata, setHata] = useState('');
  const [busy, setBusy] = useState(false);

  const gonder = async (e) => {
    e.preventDefault();
    setHata('');
    setBusy(true);
    try {
      await login(kullaniciAdi.trim(), sifre);
      nav('/', { replace: true });
    } catch (err) {
      setHata(err?.response?.data?.mesaj || err?.response?.data?.message || 'Giriş başarısız. Bilgileri kontrol edin.');
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="login-wrap">
      <form className="login-card" onSubmit={gonder}>
        <h1>🚗 Oto Yıkama</h1>
        <p className="mut" style={{ textAlign: 'center' }}>Devam etmek için giriş yapın</p>
        {hata && <div className="err">{hata}</div>}
        <div className="grid" style={{ marginTop: 8 }}>
          <input placeholder="Kullanıcı adı" value={kullaniciAdi} onChange={(e) => setK(e.target.value)} autoComplete="username" />
          <input placeholder="Şifre" type="password" value={sifre} onChange={(e) => setS(e.target.value)} autoComplete="current-password" />
          <button className="btn primary" disabled={busy || !kullaniciAdi || !sifre}>
            {busy ? 'Giriş yapılıyor…' : 'Giriş Yap'}
          </button>
        </div>
      </form>
    </div>
  );
}
