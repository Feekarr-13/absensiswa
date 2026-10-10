import type { ComponentProps } from 'react'

export function AppCard({ className = '', ...rest }: ComponentProps<'div'>) {
  return (
    <div
      className={`rounded-2xl border border-line bg-white p-4 shadow-sm ${className}`}
      {...rest}
    />
  )
}