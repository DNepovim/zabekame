import { createFileRoute, redirect } from '@tanstack/react-router'

import { HomePage } from '@/pages/home'
import { fetchSession } from '@/shared/auth'

export const Route = createFileRoute('/')({
  loader: async () => {
    const session = await fetchSession()
    if (!session?.user) {
      // eslint-disable-next-line @typescript-eslint/only-throw-error
      throw redirect({ to: '/login' })
    }
    return session.user
  },
  component: function IndexComponent() {
    const user = Route.useLoaderData()
    return <HomePage user={user} />
  },
})
