import { Navigate, Route, Routes } from 'react-router-dom'
import { DashboardPlaceholderPage } from '../pages/DashboardPlaceholderPage'
import { LoginPage } from '../pages/LoginPage'
import { WelcomePage } from '../pages/WelcomePage'
import type { Role } from '../types/api'
import { GuestRoute, ProtectedRoute } from './ProtectedRoute'

const ROLE_ROUTES: { path: string; role: Role }[] = [
  { path: '/student', role: 'student' },
  { path: '/teacher', role: 'teacher' },
  { path: '/parent', role: 'parent' },
  { path: '/admin', role: 'admin' },
]

export function AppRouter() {
  return (
    <Routes>
      <Route path="/" element={<GuestRoute><WelcomePage /></GuestRoute>} />
      <Route path="/login" element={<GuestRoute><LoginPage /></GuestRoute>} />
      {ROLE_ROUTES.map(({ path, role }) => (
        <Route
          key={path}
          path={path}
          element={
            <ProtectedRoute roles={[role]}>
              <DashboardPlaceholderPage />
            </ProtectedRoute>
          }
        />
      ))}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}