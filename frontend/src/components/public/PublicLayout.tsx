import { useState } from 'react'
import type { ReactNode } from 'react'
import { Link } from './RouterLink'

const currentYear = new Date().getFullYear()

export function PublicHeader() {
  const [open, setOpen] = useState(false)
  return <header className="public-header">
    <Link className="brand" href="/"><span className="brand-mark">LR</span><span>Elastómeros<br />La Roca</span></Link>
    <button className="mobile-menu" aria-label="Abrir menú" aria-expanded={open} onClick={() => setOpen(!open)}>☰</button>
    <nav className={`public-nav ${open ? 'is-open' : ''}`} aria-label="Navegación principal">
      <Link href="/productos" onClick={() => setOpen(false)}>Productos</Link>
      <Link href="/servicios" onClick={() => setOpen(false)}>Servicios</Link>
      <Link href="/nosotros" onClick={() => setOpen(false)}>Nosotros</Link>
      <Link href="/faq" onClick={() => setOpen(false)}>FAQ</Link>
      <Link className="header-action" href="/cotizacion" onClick={() => setOpen(false)}>Solicitar cotización</Link>
    </nav>
  </header>
}

export function PublicFooter() {
  return <footer className="public-footer">
    <div><Link className="brand" href="/"><span className="brand-mark">LR</span><span>Elastómeros<br />La Roca</span></Link><p>Soluciones en caucho y elastómeros para la industria colombiana.</p></div>
    <div className="footer-links"><div><strong>Explorar</strong><Link href="/productos">Productos</Link><Link href="/servicios">Servicios</Link><Link href="/nosotros">Nosotros</Link></div><div><strong>Legal</strong><Link href="/politica-privacidad">Privacidad</Link><Link href="/terminos">Términos</Link></div></div>
    <div className="footer-bottom"><span>© {currentYear} Elastómeros La Roca S.A.S.</span><span>Bogotá D.C., Colombia</span></div>
  </footer>
}

export function PublicLayout({ children }: { children: ReactNode }) { return <><PublicHeader />{children}<PublicFooter /></> }
