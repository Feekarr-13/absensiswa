import { createContext } from 'react'
import type { LoginPayload } from '../../api/auth'
import type { User } from '../../types/api'

export interface AuthContextValue {
  user: User | null
  isLoading: boolean
  login: (payload: LoginPayload) => Promise<User>
  logout: () => Promise<void>
}

export const AuthContext = createContext<AuthContextValue | null>(null)