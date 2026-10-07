import { useState } from 'react'
import {
  Accordion, Alert, Badge, Breadcrumb, Button, Card, Checkbox, Dropdown, Input,
  Modal, Pagination, Select, Skeleton, Spinner, Tabs, Textarea, Toast, useTheme,
} from '../components/ui'
import './DesignSystemPage.css'

const themeOptions = [
  { value: 'light', label: 'Claro' },
  { value: 'dark', label: 'Oscuro' },
  { value: 'system', label: 'Sistema' },
]

export function DesignSystemPage() {
  const { theme, setTheme } = useTheme()
  const [modalOpen, setModalOpen] = useState(false)
  const [toastVisible, setToastVisible] = useState(true)
  const [page, setPage] = useState(1)

  return (
    <main className="ds-page">
      <header className="ds-page-header">
        <Breadcrumb items={[{ label: 'Inicio', href: '/' }, { label: 'Design System' }]} />
        <div className="ds-page-heading">
          <div><p className="ds-eyebrow">Elastómeros La Roca · interno</p><h1>Design System</h1><p>Componentes, estados y patrones accesibles para construir la plataforma.</p></div>
          <Select label="Tema" value={theme} onChange={(event) => setTheme(event.target.value as typeof theme)} options={themeOptions} />
        </div>
      </header>

      <section className="ds-section" aria-labelledby="actions-title">
        <h2 id="actions-title">Acciones y estados</h2>
        <div className="ds-showcase-row"><Button>Principal</Button><Button variant="secondary">Secundario</Button><Button variant="ghost">Sutil</Button><Button variant="danger">Eliminar</Button><Button loading>Cargando</Button><Button disabled>Deshabilitado</Button></div>
        <div className="ds-showcase-row"><Badge>Neutral</Badge><Badge tone="success">Éxito</Badge><Badge tone="warning">Atención</Badge><Badge tone="danger">Error</Badge><Badge tone="info">Información</Badge><Spinner /><Spinner size="large" /></div>
      </section>

      <section className="ds-section" aria-labelledby="forms-title">
        <h2 id="forms-title">Formularios</h2>
        <div className="ds-form-grid"><Input label="Nombre" placeholder="Tu nombre" required hint="Este campo es obligatorio." /><Input label="Email con error" value="correo-invalido" readOnly error="Escribe un correo válido." onChange={() => undefined} /><Select label="Categoría" options={[{ value: 'industrial', label: 'Industrial' }, { value: 'automotive', label: 'Automotriz' }]} /><Textarea label="Mensaje" placeholder="Cuéntanos tu necesidad" /><Checkbox label="Acepto la política de privacidad" /></div>
      </section>

      <section className="ds-section" aria-labelledby="feedback-title">
        <h2 id="feedback-title">Feedback y contenido</h2>
        <div className="ds-stack"><Alert title="Información" tone="info">Los cambios se guardarán al confirmar.</Alert><Alert title="Guardado" tone="success">La operación se completó correctamente.</Alert><Alert title="Revisa el formulario" tone="danger">Hay campos que necesitan atención.</Alert>{toastVisible && <Toast onClose={() => setToastVisible(false)}>Solicitud guardada correctamente.</Toast>}</div>
      </section>

      <section className="ds-section" aria-labelledby="containers-title">
        <h2 id="containers-title">Contenedores e interacción</h2>
        <div className="ds-card-grid"><Card title="Card de producto"><p>Componente de contenido para productos y servicios.</p><Button onClick={() => setModalOpen(true)}>Abrir modal</Button></Card><Card title="Carga"><Skeleton height="1.2rem" /><Skeleton width="75%" /><Skeleton width="45%" rounded /></Card><Card title="Dropdown"><Dropdown label="Acciones"><a href="#editar">Editar registro</a><a href="#archivar">Archivar registro</a></Dropdown></Card></div>
        <div className="ds-spaced"><Tabs tabs={[{ label: 'Resumen', content: <p>Contenido resumido del registro.</p> }, { label: 'Detalles', content: <p>Información técnica adicional.</p> }]} /><Accordion items={[{ title: '¿Qué incluye?', content: <p>Patrones base para una interfaz consistente.</p> }, { title: '¿Es accesible?', content: <p>Usa HTML semántico, foco visible, labels y ARIA.</p> }]} /></div>
        <Pagination page={page} totalPages={3} onChange={setPage} />
      </section>

      <Modal open={modalOpen} title="Modal de ejemplo" onClose={() => setModalOpen(false)}><p>Presiona Escape o el botón de cierre para continuar.</p><Button onClick={() => setModalOpen(false)}>Cerrar</Button></Modal>
    </main>
  )
}
