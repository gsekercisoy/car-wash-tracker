import { NavLink, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from './AuthContext';

const MENU = [
  { to: '/', label: '📊 Panel', end: true },
  { to: '/araclar', label: '🧾 Araç Kartları' },
  { to: '/musteriler', label: '👥 Müşteriler' },
  { to: '/tanimlar', label: '🧰 Tanımlar' },
  { to: '/randevular', label: '📅 Randevular' },
  { to: '/cari', label: '💳 Cari' },
  { to: '/kasa', label: '💰 Kasa' },
  { to: '/raporlar', label: '📈 Raporlar' },
  { to: '/notlar', label: '📝 Notlar' },
  { to: '/yonetim', label: '⚙️ Yönetim' }
];

export default function Layout() {
  const { user, logout } = useAuth();
  const nav = useNavigate();

  const cikis = () => {
    logout();
    nav('/login');
  };

  return (
    <div className="shell">
      <aside className="sidebar">
        <div className="brand">🚗 Oto Yıkama</div>
        <div className="who">
          <b>{user?.ad ?? 'Kullanıcı'}</b>
          <span>{user?.kullaniciAdi} · {user?.rol}</span>
        </div>
        <nav>
          {MENU.map((m) => (
            <NavLink key={m.to} to={m.to} end={m.end} className={({ isActive }) => (isActive ? 'active' : '')}>
              {m.label}
            </NavLink>
          ))}
        </nav>
        <button className="btn danger ghost" onClick={cikis}>
          ⎋ Çıkış Yap
        </button>
      </aside>
      <main className="content">
        <Outlet />
      </main>
    </div>
  );
}
