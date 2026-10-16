import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import api from './api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem('user') ?? 'null');
    } catch {
      return null;
    }
  });
  const [token, setToken] = useState(() => localStorage.getItem('token'));

  const login = useCallback(async (kullaniciAdi, sifre) => {
    const { data } = await api.post('/api/auth/login', { kullaniciAdi, sifre });
    // beklenen: { token, id, ad, kullaniciAdi, rol }
    localStorage.setItem('token', data.token);
    const u = { id: data.id, ad: data.ad, kullaniciAdi: data.kullaniciAdi, rol: data.rol };
    localStorage.setItem('user', JSON.stringify(u));
    setToken(data.token);
    setUser(u);
    return u;
  }, []);

  const logout = useCallback(() => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setToken(null);
    setUser(null);
  }, []);

  useEffect(() => {
    const t = localStorage.getItem('token');
    if (t && !token) setToken(t);
  }, [token]);

  const value = useMemo(
    () => ({ user, token, isAdmin: user?.rol === 'admin', login, logout }),
    [user, token, login, logout]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export const useAuth = () => useContext(AuthContext);
