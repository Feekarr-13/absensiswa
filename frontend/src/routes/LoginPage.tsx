import { useState } from 'react'
import { zodResolver } from '@hookform/resolvers/zod'
import { Eye, EyeOff, GraduationCap, Lock, User } from 'lucide-react'
import { useForm } from 'react-hook-form'
import { useLocation, useNavigate } from 'react-router-dom'
import { z } from 'zod'
import { getErrorMessage } from '../api/client'
import { AppButton } from '../components/ui/AppButton'
import { AppInput } from '../components/ui/AppInput'
import { useAuth } from '../features/auth/useAuth'
import { homePath } from '../utils/roles'

const schema = z.object({
  login: z.string().trim().min(1, 'Email atau username wajib diisi.'),
  password: z.string().min(1, 'Password wajib diisi.'),
})

type FormValues = z.infer<typeof schema>

export function LoginPage() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()
  const from = (location.state as { from?: string } | null)?.from

  const [showPassword, setShowPassword] = useState(false)
  const [serverError, setServerError] = useState<string | null>(null)

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<FormValues>({ resolver: zodResolver(schema) })

  const onSubmit = handleSubmit(async (values) => {
    setServerError(null)
    try {
      const user = await login(values)
      navigate(from ?? homePath(user.role), { replace: true })
    } catch (error) {
      setServerError(getErrorMessage(error))
    }
  })

  return (
    <main className="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6 py-12">
      <div className="flex flex-col items-center text-center">
        <div className="flex size-16 items-center justify-center rounded-2xl bg-primary">
          <GraduationCap className="size-9 text-white" aria-hidden="true" />
        </div>
        <h1 className="mt-4 text-3xl font-bold">
          Absen<span className="text-primary">Siswa</span>
        </h1>
        <p className="mt-2 text-sm text-muted">Masuk ke akun Anda</p>
      </div>

      <form onSubmit={onSubmit} noValidate className="mt-8 space-y-4">
        {serverError && (
          <div role="alert" className="rounded-xl border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
            {serverError}
          </div>
        )}

        <AppInput
          label="Email atau username"
          autoComplete="username"
          placeholder="nama@sekolah.id"
          leftIcon={<User className="size-5" aria-hidden="true" />}
          error={errors.login?.message}
          {...register('login')}
        />

        <AppInput
          label="Password"
          type={showPassword ? 'text' : 'password'}
          autoComplete="current-password"
          placeholder="Masukkan password"
          leftIcon={<Lock className="size-5" aria-hidden="true" />}
          rightSlot={
            <button
              type="button"
              onClick={() => setShowPassword((v) => !v)}
              aria-label={showPassword ? 'Sembunyikan password' : 'Tampilkan password'}
              className="flex size-9 items-center justify-center rounded-lg text-muted hover:text-ink"
            >
              {showPassword ? <EyeOff className="size-5" /> : <Eye className="size-5" />}
            </button>
          }
          error={errors.password?.message}
          {...register('password')}
        />

        <AppButton type="submit" fullWidth loading={isSubmitting}>
          Masuk
        </AppButton>
      </form>
    </main>
  )
}