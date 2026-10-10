import type { Role } from '../types/api'

const HOME: Record<Role, string> = {
  student: '/student',
  teacher: '/teacher',
  parent: '/parent',
  admin: '/admin',
}

export const ROLE_LABEL: Record<Role, string> = {
  student: 'Siswa',
  teacher: 'Guru',
  parent: 'Orang Tua',
  admin: 'Administrator',
}

export function homePath(role: Role): string {
  return HOME[role]
}