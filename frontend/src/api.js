import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8080',
  timeout: 15000
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err?.response?.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      if (!window.location.pathname.includes('/login')) window.location.href = '/login';
    }
    return Promise.reject(err);
  }
);

export const para = (n) =>
  new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(Number(n ?? 0));

export const DURUM_ETIKET = {
  kabul: 'Kabul',
  islemde: 'İşlemde',
  bitti: 'Bitti',
  cagrildi: 'Çağrıldı',
  teslim: 'Teslim Edildi'
};

export const durumEtiket = (d) => DURUM_ETIKET[d] ?? d ?? '—';

export default api;
