import { createFileRoute, redirect } from '@tanstack/react-router'

import { LogInForm } from '@/pages/login'
import { fetchSession } from '@/shared/auth'

export const Route = createFileRoute('/login')({
  loader: async () => {
    const session = await fetchSession()
    if (session?.user) {
      // eslint-disable-next-line @typescript-eslint/only-throw-error
      throw redirect({ to: '/' })
    }
  },
  component: function LoginComponent() {
    return (
      <main className="flex min-h-[calc(100vh-8rem)] items-center justify-center px-4">
        <LogInForm />
      </main>
    )
  },
})
