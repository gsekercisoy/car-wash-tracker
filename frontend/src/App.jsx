import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import { AuthProvider, useAuth } from './AuthContext';
import Layout from './Layout';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import Araclar from './pages/Araclar';
import AracDetay from './pages/AracDetay';
import Musteriler from './pages/Musteriler';
import Tanimlar from './pages/Tanimlar';
import Randevular from './pages/Randevular';
import Cari from './pages/Cari';
import Kasa from './pages/Kasa';
import Raporlar from './pages/Raporlar';
import Notlar from './pages/Notlar';
import Yonetim from './pages/Yonetim';

function Korumali({ children }) {
  const { token } = useAuth();
  if (!token) return <Navigate to="/login" replace />;
  return children;
}

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<Login />} />
          <Route
            path="/"
            element={
              <Korumali>
                <Layout />
              </Korumali>
            }
          >
            <Route index element={<Dashboard />} />
            <Route path="araclar" element={<Araclar />} />
            <Route path="araclar/:id" element={<AracDetay />} />
            <Route path="musteriler" element={<Musteriler />} />
            <Route path="tanimlar" element={<Tanimlar />} />
            <Route path="randevular" element={<Randevular />} />
            <Route path="cari" element={<Cari />} />
            <Route path="kasa" element={<Kasa />} />
            <Route path="raporlar" element={<Raporlar />} />
            <Route path="notlar" element={<Notlar />} />
            <Route path="yonetim" element={<Yonetim />} />
          </Route>
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}
