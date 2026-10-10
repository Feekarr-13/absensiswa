import { LogOut } from 'lucide-react'
import { useNavigate } from 'react-router-dom'
import { AppButton } from '../components/ui/AppButton'
import { AppCard } from '../components/ui/AppCard'
import { useAuth } from '../features/auth/useAuth'
import { ROLE_LABEL } from '../utils/roles'

export function DashboardPlaceholderPage() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()

  if (!user) return null

  return (
    <main className="mx-auto max-w-md px-6 py-12">
      <AppCard className="space-y-2">
        <p className="text-sm text-muted">Berhasil masuk sebagai {ROLE_LABEL[user.role]}</p>
        <h1 className="text-2xl font-bold">{user.name}</h1>
        {user.student && (
          <p className="text-sm text-muted">
            Kelas {user.student.classroom} · NIS {user.student.student_number}
          </p>
        )}
        <p className="pt-2 text-xs text-muted">Halaman sementara. Dashboard asli dibuat di bagian berikutnya.</p>
      </AppCard>

      <AppButton
        variant="secondary"
        fullWidth
        className="mt-4"
        onClick={async () => {
          await logout()
          navigate('/login', { replace: true })
        }}
      >
        <LogOut className="size-5" aria-hidden="true" />
        Keluar
      </AppButton>
    </main>
  )
}