import { useMemo, useState } from 'react'
import type { FormEvent } from 'react'
import { categories, products, services } from '../data/catalog'
import type { Product } from '../data/catalog'
import heroImage from '../assets/hero.png'
import { Alert, Button, Card, Checkbox, Input, Select, Textarea } from '../components/ui'
import { Link } from '../components/public/RouterLink'
import { Seo } from '../components/public/Seo'
import { submitContactMessage } from '../services/contact'
import { submitQuoteRequest } from '../services/quotes'

const quoteMinimumDate = new Date().toISOString().split('T')[0]

function PageIntro({ eyebrow, title, description }: { eyebrow: string; title: string; description: string }) {
  return <section className="page-intro"><p className="eyebrow">{eyebrow}</p><h1>{title}</h1><p>{description}</p></section>
}

function Cta() { return <section className="public-cta"><div><p className="eyebrow">Hablemos de su aplicación</p><h2>Una solución precisa empieza con una buena conversación.</h2></div><Link className="button button-light" href="/cotizacion">Solicitar cotización ↗</Link></section> }

export function HomePage() {
  return <><Seo title="Soluciones en elastómeros" description="Elastómeros La Roca suministra soluciones técnicas en caucho y elastómeros para la industria colombiana." /><main className="public-main">
    <section className="home-hero"><div><p className="eyebrow">Ingeniería en elastómeros · Bogotá D.C.</p><h1>Materiales que sostienen la industria.</h1><p className="hero-lead">Diseñamos y suministramos soluciones en caucho y elastómeros para operaciones que necesitan confiabilidad, precisión y continuidad.</p><div className="hero-actions"><Link className="button button-primary" href="/productos">Explorar productos</Link><Link className="button button-secondary" href="/contacto">Hablar con un asesor ↗</Link></div></div><div className="hero-art"><img src={heroImage} alt="" loading="eager" /><div><span>Desde Colombia</span><strong>Soluciones<br />que perduran.</strong></div></div></section>
    <section className="home-section"><div className="section-heading"><div><p className="eyebrow">Soluciones para cada reto</p><h2>Lo técnico también puede ser sencillo.</h2></div><Link href="/categorias">Ver categorías ↗</Link></div><div className="category-grid">{categories.map((category) => <Link className="category-tile" href={`/categorias/${category.slug}`} key={category.slug}><span>0{categories.indexOf(category) + 1}</span><h3>{category.name}</h3><p>{category.description}</p><b>↗</b></Link>)}</div></section>
    <section className="value-section"><div><p className="eyebrow">Nuestra propuesta de valor</p><h2>Más que un componente: criterio para su operación.</h2></div><div className="value-list">{['Respuesta cercana y clara', 'Materiales seleccionados para cada aplicación', 'Compromiso con la continuidad de su proceso'].map((value, index) => <div key={value}><span>0{index + 1}</span><p>{value}</p></div>)}</div></section>
    <section className="home-section"><div className="section-heading"><div><p className="eyebrow">Selección técnica</p><h2>Productos destacados</h2></div><Link href="/productos">Ver catálogo ↗</Link></div><div className="product-grid">{products.slice(0, 3).map((product) => <ProductCard product={product} key={product.slug} />)}</div></section>
    <section className="service-band"><p className="eyebrow">Acompañamiento especializado</p><h2>La solución correcta va más allá del producto.</h2><div className="service-grid">{services.map((service, index) => <Link href={`/servicios/${service.slug}`} key={service.slug}><span>0{index + 1}</span><h3>{service.name}</h3><p>{service.description}</p></Link>)}</div></section>
    <section className="process-section home-section"><p className="eyebrow">Nuestro proceso</p><h2>Del reto a la solución.</h2><div className="process-grid">{['Escuchamos', 'Entendemos', 'Proponemos', 'Acompañamos'].map((step, index) => <div key={step}><span>0{index + 1}</span><h3>{step}</h3><p>{['Conocemos su operación y el problema que necesita resolver.', 'Revisamos condiciones, materiales y exigencias del proceso.', 'Presentamos una alternativa clara y técnicamente viable.', 'Seguimos presentes para construir relaciones duraderas.'][index]}</p></div>)}</div></section>
    <Cta />
  </main></>
}

function ProductCard({ product }: { product: Product }) { return <Link className="product-card" href={`/productos/${product.slug}`}><div className="product-art" style={{ background: product.accent }}><span>{product.category}</span><b>LR</b></div><div><p className="eyebrow">{product.category}</p><h3>{product.name}</h3><p>{product.description}</p><strong>Ver detalle ↗</strong></div></Link> }

export function ProductsPage() {
  const [search, setSearch] = useState(''); const [category, setCategory] = useState('')
  const filtered = useMemo(() => products.filter((product) => (!category || product.categorySlug === category) && `${product.name} ${product.description}`.toLowerCase().includes(search.toLowerCase())), [category, search])
  return <><Seo title="Productos" description="Catálogo de productos técnicos en caucho y elastómeros de Elastómeros La Roca." /><main className="public-main"><PageIntro eyebrow="Catálogo técnico" title="Productos para que su operación siga avanzando." description="Explore nuestra selección de componentes elastoméricos. Si no encuentra exactamente lo que necesita, podemos desarrollar una alternativa a medida." /><section className="catalog-section"><div className="catalog-filters"><Input label="Buscar producto" placeholder="Ej. sello, manguera..." value={search} onChange={(event) => setSearch(event.target.value)} /><Select label="Filtrar por categoría" value={category} onChange={(event) => setCategory(event.target.value)} options={categories.map((item) => ({ value: item.slug, label: item.name }))} /></div><p className="results-count">{filtered.length} productos encontrados</p><div className="product-grid">{filtered.map((product) => <ProductCard product={product} key={product.slug} />)}</div>{filtered.length === 0 && <Alert title="No encontramos resultados">Prueba otra búsqueda o solicita una solución personalizada.</Alert>}</section><Cta /></main></>
}

export function ProductDetailPage({ slug }: { slug: string }) {
  const product = products.find((item) => item.slug === slug)
  if (!product) return <NotFound />
  return <><Seo title={product.name} description={product.description} /><main className="public-main"><div className="detail-page"><p className="eyebrow"><Link href="/productos">Productos</Link> / {product.category}</p><div className="product-detail"><div className="product-detail-art" style={{ background: product.accent }}><span>{product.category}</span><b>LR</b></div><div><h1>{product.name}</h1><p className="detail-lead">{product.description}</p><Link className="button button-primary" href={`/cotizacion?producto=${product.slug}`}>Solicitar cotización ↗</Link><div className="spec-table"><h2>Especificaciones</h2>{product.specifications.map(([name, value]) => <div key={name}><span>{name}</span><strong>{value}</strong></div>)}</div></div></div></div><Cta /></main></>
}

export function CategoriesPage() { return <><Seo title="Categorías" description="Encuentre soluciones por categoría de producto." /><main className="public-main"><PageIntro eyebrow="Explore por aplicación" title="Categorías para encontrar el punto de partida." description="Organizamos nuestras soluciones para que pueda llegar más rápido a la respuesta que busca." /><section className="category-list">{categories.map((category, index) => <Link href={`/categorias/${category.slug}`} key={category.slug}><span>0{index + 1}</span><div><h2>{category.name}</h2><p>{category.description}</p></div><b>↗</b></Link>)}</section><Cta /></main></> }
export function CategoryDetailPage({ slug }: { slug: string }) { const category = categories.find((item) => item.slug === slug); const items = products.filter((item) => item.categorySlug === slug); if (!category) return <NotFound />; return <><Seo title={category.name} description={category.description} /><main className="public-main"><PageIntro eyebrow="Categoría de productos" title={category.name} description={category.description} /><section className="catalog-section"><div className="product-grid">{items.map((product) => <ProductCard product={product} key={product.slug} />)}</div></section><Cta /></main></> }
export function ServicesPage() { return <><Seo title="Servicios" description="Asesoría, fabricación a medida y mantenimiento de soluciones elastoméricas." /><main className="public-main"><PageIntro eyebrow="Servicios" title="Acompañamiento técnico para cada etapa." description="Ponemos nuestro conocimiento al servicio de sus objetivos de operación." /><section className="service-list">{services.map((service, index) => <Link href={`/servicios/${service.slug}`} key={service.slug}><span>0{index + 1}</span><div><h2>{service.name}</h2><p>{service.description}</p></div><b>↗</b></Link>)}</section><Cta /></main></> }
export function ServiceDetailPage({ slug }: { slug: string }) { const service = services.find((item) => item.slug === slug); if (!service) return <NotFound />; return <><Seo title={service.name} description={service.description} /><main className="public-main"><div className="detail-page service-detail"><p className="eyebrow"><Link href="/servicios">Servicios</Link> / Servicio</p><h1>{service.name}</h1><p className="detail-lead">{service.description}</p><div className="service-detail-grid"><Card title="¿Cómo podemos ayudar?"><p>Cuéntenos sobre su aplicación, cantidades y condiciones de trabajo. Nuestro equipo revisará la información y le propondrá el siguiente paso.</p><Link className="button button-primary" href="/contacto">Hablar con un asesor</Link></Card><Card title="Una respuesta a su medida"><p>Trabajamos con información técnica, muestras y planos para encontrar una solución que se integre a su proceso.</p></Card></div></div><Cta /></main></> }
export function AboutPage() { return <><Seo title="Nosotros" description="Conozca a Elastómeros La Roca, empresa colombiana de soluciones industriales." /><main className="public-main"><PageIntro eyebrow="Elastómeros La Roca" title="Una empresa cercana para industrias exigentes." description="Nacimos para hacer más simple el acceso a soluciones confiables en caucho y elastómeros, con conocimiento técnico y atención humana." /><section className="about-grid"><div><h2>Conocemos el valor de que una pieza funcione.</h2></div><div><p>Trabajamos con empresas que no pueden detenerse. Por eso escuchamos antes de recomendar, cuidamos cada detalle y respondemos con claridad.</p><p>Desde Bogotá acompañamos operaciones en Colombia con una mirada práctica, flexible y comprometida.</p></div></section><Cta /></main></> }
export function FaqPage() { const faqs = ['¿Qué información necesitan para cotizar?', '¿Fabrican productos a medida?', '¿Atienden empresas fuera de Bogotá?', '¿Cómo elegir el material correcto?']; return <><Seo title="Preguntas frecuentes" description="Respuestas a preguntas frecuentes sobre productos y servicios." /><main className="public-main"><PageIntro eyebrow="Preguntas frecuentes" title="Respuestas claras para avanzar." description="Si no encuentra aquí lo que busca, nuestro equipo está listo para conversar." /><section className="faq-list">{faqs.map((question, index) => <details key={question}><summary>{question}</summary><p>{['Una referencia, cantidad, aplicación y condiciones de trabajo nos ayudan a responder mejor.', 'Sí. Podemos trabajar desde una muestra, plano o descripción de la necesidad.', 'Atendemos solicitudes de empresas en todo Colombia y coordinamos la logística.', 'Consideramos temperatura, presión, fluido, desgaste y geometría antes de recomendar.'][index]}</p></details>)}</section><Cta /></main></> }
export function ContactPage() {
  const [sent, setSent] = useState(false)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const submit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    setLoading(true)
    setError('')
    const form = new FormData(event.currentTarget)
    try {
      await submitContactMessage({
        name: String(form.get('name') || ''),
        company_name: String(form.get('company_name') || ''),
        email: String(form.get('email') || ''),
        phone: String(form.get('phone') || ''),
        subject: String(form.get('subject') || 'Solicitud de información'),
        message: String(form.get('message') || ''),
        consent: form.get('consent') === 'on',
      })
      setSent(true)
      event.currentTarget.reset()
    } catch (submissionError) {
      setError(submissionError instanceof Error ? submissionError.message : 'No fue posible enviar la solicitud.')
    } finally {
      setLoading(false)
    }

  }
  return <><Seo title="Contacto" description="Contacte a Elastómeros La Roca para solicitar información o una cotización." /><main className="public-main"><PageIntro eyebrow="Contacto" title="Cuéntenos qué necesita." description="Comparta los detalles de su aplicación y un asesor le responderá." /><section className="contact-grid"><form onSubmit={submit} className="contact-form">{sent && <Alert title="Solicitud recibida" tone="success">Gracias. Nos pondremos en contacto pronto.</Alert>}{error && <Alert title="No pudimos enviar su solicitud" tone="danger">{error}</Alert>}<Input label="Nombre" name="name" required /><Input label="Empresa" name="company_name" required /><Input label="Email" name="email" type="email" required /><Input label="Teléfono" name="phone" type="tel" /><Input label="Asunto" name="subject" required defaultValue="Solicitud de información" /><Textarea label="¿En qué podemos ayudarle?" name="message" required /><Checkbox label="Acepto la política de privacidad y el tratamiento de mis datos." name="consent" required /><input className="honeypot" name="website" tabIndex={-1} autoComplete="off" aria-hidden="true" /><Button type="submit" loading={loading}>Enviar solicitud ↗</Button></form><div className="contact-info"><h2>Hablemos.</h2><p>comercial@elastomeroslaroca.com</p><p>Bogotá D.C., Colombia</p><p>Respondemos sus solicitudes en horario laboral.</p><Link className="button button-primary" href="/cotizacion">Ir a solicitar cotización ↗</Link></div></section></main></>
}

export function QuotePage() {
  const initialProduct = new URLSearchParams(window.location.search).get('producto') || ''
  const [sent, setSent] = useState(false)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const submit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault()
    const formElement = event.currentTarget
    setLoading(true)
    setError('')
    const form = new FormData(formElement)
    try {
      await submitQuoteRequest({
        company_name: String(form.get('company_name') || ''),
        contact_name: String(form.get('contact_name') || ''),
        email: String(form.get('email') || ''),
        phone: String(form.get('phone') || ''),
        city: String(form.get('city') || ''),
        required_date: String(form.get('required_date') || ''),
        observations: String(form.get('observations') || ''),
        product: products.find((product) => product.slug === form.get('product'))?.name || String(form.get('product') || ''),
        quantity: String(form.get('quantity') || ''),
        attachment: form.get('attachment') instanceof File ? form.get('attachment') as File : undefined,
        website: String(form.get('website') || ''),
        consent: form.get('consent') === 'on',
      })
      setSent(true)
      formElement.reset()
    } catch (submissionError) {
      setError(submissionError instanceof Error ? submissionError.message : 'No fue posible enviar la solicitud.')
    } finally {
      setLoading(false)
    }
  }
  return <><Seo title="Solicitar cotización" description="Solicite una cotización de soluciones en caucho y elastómeros." /><main className="public-main"><PageIntro eyebrow="Cotización" title="Cuéntenos sobre su necesidad." description="Comparta los datos de su aplicación y prepararemos una respuesta técnica para su empresa." /><section className="contact-grid"><form onSubmit={submit} className="contact-form">{sent && <Alert title="Solicitud recibida" tone="success">Su solicitud fue registrada. Recibirá confirmación por correo.</Alert>}{error && <Alert title="No pudimos enviar su solicitud" tone="danger">{error}</Alert>}<Input label="Nombre de contacto" name="contact_name" required /><Input label="Empresa" name="company_name" required /><Input label="Email" name="email" type="email" required /><Input label="Teléfono" name="phone" type="tel" /><Input label="Ciudad" name="city" required /><Select label="Producto o solución" name="product" defaultValue={initialProduct} required options={products.map((product) => ({ value: product.slug, label: product.name }))} /><Input label="Cantidad" name="quantity" type="number" min="1" required /><Input label="Fecha requerida" name="required_date" type="date" min={quoteMinimumDate} required /><Input label="Archivo técnico (opcional)" name="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.webp" hint="Máximo 10 MB. PDF, Office o imagen." /><Textarea label="Observaciones" name="observations" /><Checkbox label="Acepto la política de privacidad y el tratamiento de mis datos." name="consent" required /><input className="honeypot" name="website" tabIndex={-1} autoComplete="off" aria-hidden="true" /><Button type="submit" loading={loading}>Enviar solicitud ↗</Button></form><div className="contact-info"><h2>Una respuesta precisa.</h2><p>Revisaremos producto, cantidad, ciudad y fecha requerida para responder con el siguiente paso adecuado.</p><p>Si tiene planos, fichas técnicas o imágenes de referencia, puede adjuntarlos.</p></div></section></main></>
}
export function LegalPage({ terms = false }: { terms?: boolean }) { return <><Seo title={terms ? 'Términos y condiciones' : 'Política de privacidad'} description="Información legal de Elastómeros La Roca S.A.S." /><main className="public-main"><PageIntro eyebrow="Información legal" title={terms ? 'Términos y condiciones' : 'Política de privacidad'} description="Última actualización: octubre de 2026." /><article className="legal-copy"><h2>{terms ? 'Uso del sitio' : 'Tratamiento de datos'}</h2><p>Esta página presenta información general de Elastómeros La Roca S.A.S. y sus soluciones industriales. El contenido está sujeto a disponibilidad y validación técnica.</p><h2>{terms ? 'Solicitudes y cotizaciones' : 'Información que recopilamos'}</h2><p>Cuando nos contacta podemos solicitar datos de identificación y de su empresa para responder, preparar cotizaciones y dar seguimiento a su solicitud.</p><h2>Contacto</h2><p>Para ejercer sus derechos o resolver dudas, escriba a comercial@elastomeroslaroca.com.</p></article></main></> }
function NotFound() { return <main className="public-main"><PageIntro eyebrow="404" title="No encontramos esta página." description="Regresa al inicio o explora nuestro catálogo." /><Link className="button button-primary" href="/">Volver al inicio</Link></main> }
