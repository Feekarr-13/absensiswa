import { Check, Clock, FileText, Hourglass, Thermometer, X } from 'lucide-react'
import type { LucideIcon } from 'lucide-react'
import type { AttendanceStatus } from '../../types/api'
import { AppBadge } from './AppBadge'
import type { BadgeTone } from './AppBadge'

const CONFIG: Record<AttendanceStatus, { label: string; tone: BadgeTone; icon: LucideIcon }> = {
  present: { label: 'Hadir', tone: 'success', icon: Check },
  late: { label: 'Terlambat', tone: 'warning', icon: Clock },
  sick: { label: 'Sakit', tone: 'warning', icon: Thermometer },
  permitted: { label: 'Izin', tone: 'info', icon: FileText },
  absent: { label: 'Alpa', tone: 'danger', icon: X },
  pending: { label: 'Menunggu', tone: 'neutral', icon: Hourglass },
}

// Warna bukan satu-satunya penanda: selalu ada ikon dan teks.
export function AttendanceStatusBadge({ status }: { status: AttendanceStatus }) {
  const { label, tone, icon: Icon } = CONFIG[status]
  return (
    <AppBadge tone={tone}>
      <Icon className="size-3.5" aria-hidden="true" />
      {label}
    </AppBadge>
  )
}