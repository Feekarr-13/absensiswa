import { TriangleAlert } from 'lucide-react'
import { AppButton } from './AppButton'

interface Props {
  message?: string
  onRetry?: () => void
}

export function ErrorState({ message = 'Gagal memuat data.', onRetry }: Props) {
  return (
    <div role="alert" className="flex flex-col items-center gap-3 py-12 text-center">
      <TriangleAlert className="size-10 text-danger" aria-hidden="true" />
      <p className="max-w-xs text-sm text-muted">{message}</p>
      {onRetry && (
        <AppButton variant="secondary" onClick={onRetry}>
          Coba lagi
        </AppButton>
      )}
    </div>
  )
}