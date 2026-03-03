import { useNavigate } from '@tanstack/react-router'

import type { SessionUser } from '@/shared/auth'
import { authClient } from '@/shared/auth-client'
import { Button } from '@/shared/ui/button'

type Props = {
  user: SessionUser
}

export function HomePage({ user }: Props) {
  const navigate = useNavigate()

  const handleSignOut = async () => {
    await authClient.signOut()
    await navigate({ to: '/login' })
  }

  return (
    <main className="mx-auto max-w-5xl px-4 py-16">
      <div className="flex items-center justify-between">
        <p className="text-foreground">
          Welcome, <span className="font-semibold">{user.name || user.email}</span>
        </p>
        <Button variant="outline" onClick={() => void handleSignOut()}>
          Sign out
        </Button>
      </div>
    </main>
  )
}
