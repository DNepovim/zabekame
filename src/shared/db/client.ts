import { PrismaPg } from '@prisma/adapter-pg'
import { PrismaClient } from '@prisma/client'
import { ensure } from 'narrowland'

const connectionString = ensure.defined(
  process.env.DATABASE_URL,
  'DATABASE_URL env variable is required',
)

const adapter = new PrismaPg({ connectionString })

export const db = new PrismaClient({ adapter })
