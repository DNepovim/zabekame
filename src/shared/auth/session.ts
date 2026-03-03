import { createServerFn } from '@tanstack/react-start'
import { getRequest } from '@tanstack/react-start/server'

export const fetchSession = createServerFn().handler(async () => {
  const { auth } = await import('./server')
  const request = getRequest()
  return auth.api.getSession({ headers: request.headers })
})

export type SessionUser = NonNullable<Awaited<ReturnType<typeof fetchSession>>>['user']
