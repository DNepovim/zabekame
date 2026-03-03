# Zabekame — Agent Guidelines

## Project Overview

**Zabekame** is a full-stack web application built with:

- **Framework**: [TanStack Start](https://tanstack.com/start) (React 19, SSR-capable)
- **Routing**: [TanStack Router](https://tanstack.com/router) (file-based)
- **Styling**: [Tailwind CSS v4](https://tailwindcss.com/)
- **Forms**: [React Hook Form](https://react-hook-form.com/) + [Zod](https://zod.dev/)
- **Icons**: [Lucide React](https://lucide.dev/)
- **Architecture**: [Feature-Sliced Design (FSD)](https://feature-sliced.design/docs/get-started/overview)
- **Testing**: [Vitest](https://vitest.dev/) + [Testing Library](https://testing-library.com/)
- **Database**: [Prisma](https://www.prisma.io/docs) + [PostgreSQL](https://www.postgresql.org/)
- **Auth**: [Better Auth](https://www.better-auth.com/docs/introduction)
- **Linting**: ESLint + Prettier
- **Architecture linting**: [Steiger](https://github.com/feature-sliced/steiger)

---

## Coding Rules

### TypeScript

- Write **strict TypeScript** — `strict: true` is enforced via `tsconfig.json`.
- The **`any` type is forbidden** — use `unknown`, proper generics, or explicit types instead.
- The **`as` keyword is forbidden** — use type guards, discriminated unions, or `satisfies` instead.
- Avoid type assertions; let TypeScript infer or explicitly annotate types.

### Type Narrowing — use `narrowland`

Use the [`narrowland`](https://www.npmjs.com/package/narrowland) library for all runtime type narrowing. Never write manual `typeof`/`instanceof` checks or use `as` casts to work around type uncertainty.

**Pick the right tool for the job:**

| Situation | Use |
|---|---|
| Conditional check, no throw | `is.*` type guards |
| Expect valid — fail fast, narrow in place | `assert.*` |
| Validate and return narrowed value inline | `ensure.*` |
| Check a business-logic condition | `invariant` |
| Throw a typed error inline (e.g. in `??`) | `raiseError` |

**`is.*` — type guards (return `boolean`, never throw)**

```ts
import { is } from 'narrowland'

if (is.string(value)) {
  value.toUpperCase() // value: string
}

// Great for filtering arrays with full type inference
const strings = mixed.filter(is.string) // string[]

// Narrow a property on objects in a filter
const hasEmail = is.propertyOf('email', is.defined)
const contactable = users.filter(hasEmail) // email is required & defined
```

**`assert.*` — narrow in place, throw on failure**

```ts
import { assert } from 'narrowland'

function process(input: unknown) {
  assert.string(input) // throws if not a string; input is string below
  return input.toUpperCase()
}
```

**`ensure.*` — validate and return narrowed value**

```ts
import { ensure } from 'narrowland'

// Inline assignment — replaces `as` casts
const name = ensure.string(raw.name)
const config = ensure.defined(maybeConfig, 'Config is required')

// Reusable validator from any predicate
const ensureStatus = ensure.fromPredicate(
  is.oneOf(['pending', 'done'] as const),
  'Invalid status',
)
```

**`invariant` — generic condition check**

```ts
import { invariant } from 'narrowland'

invariant(user.age >= 18, 'User must be an adult')
```

**`raiseError` — inline error (use with `??`)**

```ts
import { raiseError } from 'narrowland'

const id = maybeId ?? raiseError('ID must be defined')
```

> **Deprecated:** `is.falsy` / `assert.falsy` have incorrect narrowing — do not use them.

### Functional & Immutable Style

- Write **clean functional code** — prefer pure functions with no side effects.
- **Prefer immutable data** — use `const`, `readonly`, `ReadonlyArray`, and spread operators instead of mutation.
- **Avoid `let`** — use `const` for all variable declarations. If you think you need `let`, find a functional alternative.
- **Avoid `for` loops** — use array methods (`map`, `filter`, `reduce`, `flatMap`, `find`, `every`, `some`) instead.

### Comments

- **Do not write comments** — code should be self-explanatory through clear naming and small, focused functions.
- If you feel a comment is necessary, rewrite the code to make it obvious instead.

### React

- Prefer function components with hooks.
- Keep components small and focused — extract logic into custom hooks or utility functions.
- Avoid prop drilling — use context, state managers, or co-location instead.

### Architecture — Feature-Sliced Design

Follow the [FSD architecture](https://feature-sliced.design/docs/get-started/overview) strictly:

```
src/
  app/        # App-wide setup: providers, global styles, root layout
  pages/      # Route-level page components (assembled from widgets/features)
  widgets/    # Self-contained UI blocks composed from features/entities
  features/   # User interactions and business actions
  entities/   # Business domain objects (models, API, UI representations)
  shared/     # Reusable utilities, UI kit, configs, API clients
```

**Key FSD rules:**
- Each layer can only import from layers **below** it (e.g. `features` → `entities` → `shared`).
- Segments within a slice: `ui/`, `model/`, `api/`, `lib/`, `config/`.
- Public API via `index.ts` — never import from internal paths of another slice.
- Run `pnpm arch:check` to validate FSD compliance with Steiger.

---

## After Every Task

Run linting and formatting and fix **all** reported problems before considering a task complete:

```bash
pnpm lint:fix
pnpm format:write
pnpm arch:check
```

If any errors remain after auto-fix, resolve them manually before finishing.
