import { GraduationCap } from 'lucide-react'
import { Link, useNavigate } from 'react-router-dom'
import { AppButton } from '../components/ui/AppButton'

export function WelcomePage() {
  const navigate = useNavigate()

  return (
    <main className="mx-auto flex min-h-screen max-w-md flex-col items-center justify-between px-6 py-12">
      <div className="flex w-full flex-1 flex-col items-center justify-center text-center">
        <div className="flex size-24 items-center justify-center rounded-3xl bg-primary shadow-lg shadow-primary/30">
          <GraduationCap className="size-12 text-white" aria-hidden="true" />
        </div>
        <h1 className="mt-6 text-4xl font-bold">
          Absen<span className="text-primary">Siswa</span>
        </h1>
        <p className="mt-3 text-sm text-muted">Mudah, Cepat, Akurat</p>
        <p className="text-sm text-muted">Untuk Kehadiran Siswa</p>

        {/* Placeholder ilustrasi: ganti dengan aset ilustrasi siswa bila sudah tersedia. */}
        <div
          aria-hidden="true"
          className="mt-10 flex h-48 w-full items-center justify-center rounded-3xl bg-gradient-to-b from-primary/5 to-primary/15"
        >
          <GraduationCap className="size-20 text-primary/40" />
        </div>
      </div>

      <div className="w-full">
        <AppButton fullWidth onClick={() => navigate('/login')}>
          Mulai
        </AppButton>
        <p className="mt-4 text-center text-sm text-muted">
          Sudah punya akun?{' '}
          <Link to="/login" className="font-semibold text-primary">
            Masuk
          </Link>
        </p>
      </div>
    </main>
  )
}