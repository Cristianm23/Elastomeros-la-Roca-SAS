import './App.css'

const capabilities = [
  {
    title: 'Productos técnicos',
    description: 'Componentes en elastómeros para aplicaciones industriales exigentes.',
  },
  {
    title: 'Acompañamiento experto',
    description: 'Orientación para seleccionar materiales y soluciones según cada aplicación.',
  },
  {
    title: 'Respuesta empresarial',
    description: 'Un canal claro para solicitudes de información y cotizaciones.',
  },
]

function App() {
  return (
    <main>
      <header className="site-header">
        <a className="brand" href="/" aria-label="Elastómeros La Roca, inicio">
          <span className="brand-mark" aria-hidden="true">LR</span>
          <span>Elastómeros<br />La Roca</span>
        </a>
        <nav aria-label="Navegación principal">
          <a href="#empresa">Empresa</a>
          <a href="#soluciones">Soluciones</a>
          <a href="#contacto">Contacto</a>
        </nav>
        <a className="header-action" href="#contacto">Solicitar cotización</a>
      </header>

      <section className="hero" aria-labelledby="hero-title">
        <div className="hero-copy">
          <p className="eyebrow">Ingeniería en elastómeros · Bogotá D.C.</p>
          <h1 id="hero-title">Materiales que sostienen la industria.</h1>
          <p className="hero-lead">
            Diseñamos y suministramos soluciones en caucho y elastómeros para operaciones
            que necesitan confiabilidad, precisión y continuidad.
          </p>
          <div className="hero-actions">
            <a className="button button-primary" href="#soluciones">Conocer soluciones</a>
            <a className="button button-secondary" href="#contacto">Hablar con un asesor <span aria-hidden="true">↗</span></a>
          </div>
        </div>
        <div className="hero-panel" aria-label="Resumen de la propuesta de valor">
          <span className="panel-label">Desde Colombia</span>
          <strong>Soluciones<br />que perduran.</strong>
          <span className="panel-line" aria-hidden="true" />
          <span className="panel-note">Calidad · criterio técnico · cercanía</span>
        </div>
      </section>

      <section className="intro" id="empresa" aria-labelledby="intro-title">
        <p className="eyebrow">Una base sólida para su operación</p>
        <h2 id="intro-title">Experiencia técnica con una mirada práctica.</h2>
        <p>
          Trabajamos junto a empresas que requieren componentes confiables y atención
          especializada, convirtiendo necesidades de operación en soluciones concretas.
        </p>
      </section>

      <section className="capabilities" id="soluciones" aria-label="Capacidades">
        {capabilities.map((capability, index) => (
          <article className="capability" key={capability.title}>
            <span className="capability-number">0{index + 1}</span>
            <h3>{capability.title}</h3>
            <p>{capability.description}</p>
          </article>
        ))}
      </section>

      <section className="contact-strip" id="contacto" aria-labelledby="contact-title">
        <div>
          <p className="eyebrow">Empecemos una conversación</p>
          <h2 id="contact-title">¿Tiene un reto técnico?</h2>
        </div>
        <a className="button button-light" href="mailto:comercial@elastomeroslaroca.com">
          comercial@elastomeroslaroca.com <span aria-hidden="true">↗</span>
        </a>
      </section>

      <footer>
        <span>© Elastómeros La Roca S.A.S.</span>
        <span>Bogotá D.C., Colombia</span>
      </footer>
    </main>
  )
}

export default App
