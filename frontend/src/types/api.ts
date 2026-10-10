export type Role = 'admin' | 'teacher' | 'student' | 'parent'

export type AttendanceStatus =
  | 'present'
  | 'late'
  | 'sick'
  | 'permitted'
  | 'absent'
  | 'pending'

export interface ApiSuccess<T> {
  success: true
  message: string
  data: T
}

export interface ApiErrorBody {
  success: false
  message: string
  errors: Record<string, string[]>
}

export interface User {
  id: number
  name: string
  email: string
  username: string | null
  role: Role
  phone: string | null
  avatar_url: string | null
  student?: {
    id: number
    student_number: string
    classroom: string | null
  } | null
}