export function Footer() {
  const year = new Date().getFullYear()

  return (
    <footer className="border-t py-6">
      <div className="mx-auto flex max-w-5xl items-center justify-between px-4 text-sm text-muted-foreground">
        <p>&copy; {year} Your name here.</p>
        <p>Built with TanStack Start</p>
      </div>
    </footer>
  )
}
