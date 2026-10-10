import type { ReactNode } from 'react'
import { Navigate, useLocation } from 'react-router-dom'
import { LoadingState } from '../components/ui/LoadingState'
import { useAuth } from '../features/auth/useAuth'
import type { Role } from '../types/api'
import { homePath } from '../utils/roles'

export function ProtectedRoute({ roles, children }: { roles: Role[]; children: ReactNode }) {
  const { user, isLoading } = useAuth()
  const location = useLocation()

  if (isLoading) return <LoadingState text="Memeriksa sesi..." />
  if (!user) return <Navigate to="/login" replace state={{ from: location.pathname }} />
  if (!roles.includes(user.role)) return <Navigate to={homePath(user.role)} replace />

  return <>{children}</>
}

export function GuestRoute({ children }: { children: ReactNode }) {
  const { user, isLoading } = useAuth()

  if (isLoading) return <LoadingState text="Memeriksa sesi..." />
  if (user) return <Navigate to={homePath(user.role)} replace />

  return <>{children}</>
}