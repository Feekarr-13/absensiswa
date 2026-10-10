import type { ComponentProps, ReactNode } from 'react'
import { useId } from 'react'

interface Props extends ComponentProps<'input'> {
  label: string
  error?: string
  leftIcon?: ReactNode
  rightSlot?: ReactNode
}

export function AppInput({ label, error, leftIcon, rightSlot, className = '', ...rest }: Props) {
  const id = useId()
  const errorId = `${id}-error`

  return (
    <div className="w-full">
      <label htmlFor={id} className="mb-1.5 block text-sm font-medium text-ink">
        {label}
      </label>
      <div
        className={`flex min-h-12 items-center gap-2 rounded-xl border bg-white px-3 focus-within:ring-2 focus-within:ring-primary/30 ${
          error ? 'border-danger' : 'border-line focus-within:border-primary'
        }`}
      >
        {leftIcon && <span className="text-muted">{leftIcon}</span>}
        <input
          id={id}
          aria-invalid={error ? true : undefined}
          aria-describedby={error ? errorId : undefined}
          className={`h-full min-h-11 w-full bg-transparent text-base text-ink outline-none placeholder:text-muted/70 ${className}`}
          {...rest}
        />
        {rightSlot}
      </div>
      {error && (
        <p id={errorId} className="mt-1 text-sm text-danger">
          {error}
        </p>
      )}
    </div>
  )
}