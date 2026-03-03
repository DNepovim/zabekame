import { PrismaPg } from '@prisma/adapter-pg'
import { PrismaClient } from '@prisma/client'
import { betterAuth } from 'better-auth'
import { prismaAdapter } from 'better-auth/adapters/prisma'
import { config } from 'dotenv'
import { ensure } from 'narrowland'

config()

const connectionString = ensure.defined(
  process.env.DATABASE_URL,
  'DATABASE_URL env variable is required',
)

const db = new PrismaClient({ adapter: new PrismaPg({ connectionString }) })

const auth = betterAuth({
  database: prismaAdapter(db, { provider: 'postgresql' }),
  emailAndPassword: { enabled: true },
})

const { error } = await auth.api.signUpEmail({
  body: {
    name: 'Admin',
    email: 'admin@example.com',
    password: 'password123',
  },
})

if (error) {
  console.error('Seed failed:', error.message)
  process.exit(1)
}

console.log('Seeded user: admin@example.com / password123')
await db.$disconnect()
