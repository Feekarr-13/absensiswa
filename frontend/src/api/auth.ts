import axios from 'axios'
import { api, ensureCsrfCookie } from './client'
import type { ApiSuccess, User } from '../types/api'

export interface LoginPayload {
  login: string
  password: string
}

export async function loginRequest(payload: LoginPayload): Promise<User> {
  await ensureCsrfCookie()
  const { data } = await api.post<ApiSuccess<User>>('/auth/login', payload)
  return data.data
}

// Mengembalikan null jika belum login (401), bukan melempar error.
export async function fetchMe(): Promise<User | null> {
  try {
    const { data } = await api.get<ApiSuccess<User>>('/auth/me')
    return data.data
  } catch (error) {
    if (axios.isAxiosError(error) && error.response?.status === 401) return null
    throw error
  }
}

export async function logoutRequest(): Promise<void> {
  await api.post('/auth/logout')
}