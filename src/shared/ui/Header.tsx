import { Link } from '@tanstack/react-router'

import { ThemeToggle } from './ThemeToggle'

export function Header() {
  return (
    <header className="sticky top-0 z-50 border-b bg-background/80 backdrop-blur-sm">
      <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
        <Link to="/" className="text-sm font-semibold text-foreground no-underline">
          Zabekame
        </Link>

        <nav className="flex items-center gap-6">
          <Link
            to="/"
            className="text-sm text-muted-foreground transition-colors hover:text-foreground [&.active]:font-medium [&.active]:text-foreground"
          >
            Home
          </Link>
          <Link
            to="/about"
            className="text-sm text-muted-foreground transition-colors hover:text-foreground [&.active]:font-medium [&.active]:text-foreground"
          >
            About
          </Link>
        </nav>

        <ThemeToggle />
      </div>
    </header>
  )
}
