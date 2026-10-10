import { LoaderCircle } from 'lucide-react'

export function LoadingState({ text = 'Memuat...' }: { text?: string }) {
  return (
    <div role="status" className="flex flex-col items-center justify-center gap-3 py-16 text-muted">
      <LoaderCircle className="size-8 animate-spin text-primary" aria-hidden="true" />
      <span className="text-sm">{text}</span>
    </div>
  )
}