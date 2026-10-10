import axios from 'axios'
import type { ApiErrorBody } from '../types/api'

export const api = axios.create({
  baseURL: '/api/v1',
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

export async function ensureCsrfCookie(): Promise<void> {
  await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
}

// Jika token CSRF kedaluwarsa (419), ambil cookie baru lalu ulangi satu kali.
api.interceptors.response.use(undefined, async (error: unknown) => {
  if (
    axios.isAxiosError(error) &&
    error.response?.status === 419 &&
    error.config &&
    !error.config.headers.get('X-CSRF-Retry')
  ) {
    error.config.headers.set('X-CSRF-Retry', '1')
    await ensureCsrfCookie()
    return api.request(error.config)
  }
  throw error
})

export function getErrorMessage(error: unknown): string {
  if (axios.isAxiosError<ApiErrorBody>(error)) {
    if (!error.response) {
      return 'Tidak dapat terhubung ke server. Pastikan backend berjalan.'
    }
    if (error.response.status === 429) {
      return 'Terlalu banyak percobaan. Coba lagi beberapa saat lagi.'
    }
    const message = error.response.data?.message
    if (typeof message === 'string' && message) return message
  }
  return 'Terjadi kesalahan. Silakan coba lagi.'
}