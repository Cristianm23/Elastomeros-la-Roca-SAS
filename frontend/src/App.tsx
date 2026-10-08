import './App.css'
import { useEffect } from 'react'
import { DesignSystemPage } from './pages/DesignSystemPage'
import { AdminPage } from './pages/AdminPage'
import { PublicLayout } from './components/public/PublicLayout'
import { ConnectivityBanner } from './components/public/ConnectivityBanner'
import {
  AboutPage, CategoriesPage, CategoryDetailPage, ContactPage, FaqPage, HomePage,
  LegalPage, ProductDetailPage, ProductsPage, QuotePage, ServiceDetailPage, ServicesPage,
} from './pages/PublicPages'
import { trackEvent } from './services/analytics'

function PublicRouter() {
  const path = window.location.pathname.replace(/\/+$/, '') || '/'
  if (path === '/') return <HomePage />
  if (path === '/productos') return <ProductsPage />
  if (path.startsWith('/productos/')) return <ProductDetailPage slug={path.split('/')[2]} />
  if (path === '/categorias') return <CategoriesPage />
  if (path.startsWith('/categorias/')) return <CategoryDetailPage slug={path.split('/')[2]} />
  if (path === '/servicios') return <ServicesPage />
  if (path.startsWith('/servicios/')) return <ServiceDetailPage slug={path.split('/')[2]} />
  if (path === '/nosotros') return <AboutPage />
  if (path === '/faq') return <FaqPage />
  if (path === '/contacto') return <ContactPage />
  if (path === '/cotizacion') return <QuotePage />
  if (path === '/politica-privacidad') return <LegalPage />
  if (path === '/terminos') return <LegalPage terms />
  return <HomePage />
}

function App() {
  useEffect(() => {
    trackEvent('page_view')
  }, [])
  if (window.location.pathname === '/admin') return <AdminPage />
  if (window.location.pathname === '/design-system') return <DesignSystemPage />
  return <PublicLayout><ConnectivityBanner /><PublicRouter /></PublicLayout>
}

export default App
