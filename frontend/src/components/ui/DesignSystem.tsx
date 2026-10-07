import {
  forwardRef,
  useId,
  useEffect,
  useState,
  type ButtonHTMLAttributes,
  type InputHTMLAttributes,
  type ReactNode,
  type SelectHTMLAttributes,
  type TextareaHTMLAttributes,
} from 'react'

type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: 'primary' | 'secondary' | 'ghost' | 'danger'
  loading?: boolean
}

export function Button({ variant = 'primary', loading = false, children, disabled, ...props }: ButtonProps) {
  return (
    <button className={`ds-button ds-button--${variant}`} disabled={disabled || loading} {...props}>
      {loading && <Spinner size="small" label="Cargando" />}
      {children}
    </button>
  )
}

type FieldProps = {
  label: string
  error?: string
  hint?: string
  required?: boolean
}

export const Input = forwardRef<HTMLInputElement, InputHTMLAttributes<HTMLInputElement> & FieldProps>(
  ({ label, error, hint, required, id, ...props }, ref) => {
    const generatedId = useId()
    const inputId = id ?? generatedId
    return (
      <div className="ds-field">
        <label htmlFor={inputId}>{label}{required && <span aria-hidden="true"> *</span>}</label>
        <input ref={ref} id={inputId} aria-invalid={Boolean(error)} aria-describedby={error ? `${inputId}-error` : undefined} required={required} {...props} />
        {hint && !error && <small className="ds-hint">{hint}</small>}
        {error && <small className="ds-error" id={`${inputId}-error`} role="alert">{error}</small>}
      </div>
    )
  },
)
Input.displayName = 'Input'

export function Select({ label, error, options, required, id, ...props }: SelectHTMLAttributes<HTMLSelectElement> & FieldProps & { options: Array<{ value: string; label: string }> }) {
  const generatedId = useId()
  const selectId = id ?? generatedId
  return (
    <div className="ds-field">
      <label htmlFor={selectId}>{label}{required && <span aria-hidden="true"> *</span>}</label>
      <select id={selectId} aria-invalid={Boolean(error)} required={required} {...props}>
        <option value="">Selecciona una opción</option>
        {options.map((option) => <option key={option.value} value={option.value}>{option.label}</option>)}
      </select>
      {error && <small className="ds-error" role="alert">{error}</small>}
    </div>
  )
}

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaHTMLAttributes<HTMLTextAreaElement> & FieldProps>(
  ({ label, error, required, id, ...props }, ref) => {
    const generatedId = useId()
    const textareaId = id ?? generatedId
    return (
      <div className="ds-field">
        <label htmlFor={textareaId}>{label}{required && <span aria-hidden="true"> *</span>}</label>
        <textarea ref={ref} id={textareaId} aria-invalid={Boolean(error)} required={required} {...props} />
        {error && <small className="ds-error" role="alert">{error}</small>}
      </div>
    )
  },
)
Textarea.displayName = 'Textarea'

export function Checkbox({ label, ...props }: InputHTMLAttributes<HTMLInputElement> & { label: string }) {
  return <label className="ds-checkbox"><input type="checkbox" {...props} /><span>{label}</span></label>
}

export function Card({ children, title, className = '' }: { children: ReactNode; title?: string; className?: string }) {
  return <section className={`ds-card ${className}`}>{title && <h3>{title}</h3>}{children}</section>
}

export function Badge({ children, tone = 'neutral' }: { children: ReactNode; tone?: 'neutral' | 'success' | 'warning' | 'danger' | 'info' }) {
  return <span className={`ds-badge ds-badge--${tone}`}>{children}</span>
}

export function Alert({ children, title, tone = 'info' }: { children: ReactNode; title?: string; tone?: 'info' | 'success' | 'warning' | 'danger' }) {
  return <div className={`ds-alert ds-alert--${tone}`} role={tone === 'danger' ? 'alert' : 'status'}><strong>{title}</strong><span>{children}</span></div>
}

export function Toast({ children, onClose, tone = 'success' }: { children: ReactNode; onClose?: () => void; tone?: 'success' | 'danger' | 'info' }) {
  return <div className={`ds-toast ds-toast--${tone}`} role="status"><span>{children}</span>{onClose && <button className="ds-icon-button" onClick={onClose} aria-label="Cerrar notificación">×</button>}</div>
}

export function Spinner({ size = 'medium', label = 'Cargando' }: { size?: 'small' | 'medium' | 'large'; label?: string }) {
  return <span className={`ds-spinner ds-spinner--${size}`} role="status" aria-label={label} />
}

export function Skeleton({ width = '100%', height = '1rem', rounded = false }: { width?: string; height?: string; rounded?: boolean }) {
  return <span className={`ds-skeleton ${rounded ? 'ds-skeleton--rounded' : ''}`} style={{ width, height }} aria-hidden="true" />
}

export function Pagination({ page, totalPages, onChange }: { page: number; totalPages: number; onChange: (page: number) => void }) {
  return (
    <nav className="ds-pagination" aria-label="Paginación">
      <Button variant="ghost" disabled={page === 1} onClick={() => onChange(page - 1)}>Anterior</Button>
      <span aria-live="polite">Página {page} de {totalPages}</span>
      <Button variant="ghost" disabled={page === totalPages} onClick={() => onChange(page + 1)}>Siguiente</Button>
    </nav>
  )
}

export function Breadcrumb({ items }: { items: Array<{ label: string; href?: string }> }) {
  return <nav aria-label="Migas de pan"><ol className="ds-breadcrumb">{items.map((item, index) => <li key={item.label}>{item.href && index < items.length - 1 ? <a href={item.href}>{item.label}</a> : <span aria-current={index === items.length - 1 ? 'page' : undefined}>{item.label}</span>}{index < items.length - 1 && <span aria-hidden="true">/</span>}</li>)}</ol></nav>
}

export function Tabs({ tabs }: { tabs: Array<{ label: string; content: ReactNode }> }) {
  const [active, setActive] = useState(0)
  const tabId = useId()
  const selectTab = (index: number) => setActive((index + tabs.length) % tabs.length)
  return (
    <div className="ds-tabs">
      <div role="tablist" aria-label="Secciones" className="ds-tab-list">
        {tabs.map((tab, index) => <button key={tab.label} role="tab" aria-selected={active === index} aria-controls={`${tabId}-${index}`} tabIndex={active === index ? 0 : -1} onClick={() => setActive(index)} onKeyDown={(event) => { if (event.key === 'ArrowRight') selectTab(index + 1); if (event.key === 'ArrowLeft') selectTab(index - 1); }}>{tab.label}</button>)}
      </div>
      <div role="tabpanel" id={`${tabId}-${active}`} tabIndex={0}>{tabs[active].content}</div>
    </div>
  )
}

export function Accordion({ items }: { items: Array<{ title: string; content: ReactNode }> }) {
  return <div className="ds-accordion">{items.map((item) => <details key={item.title}><summary>{item.title}</summary><div>{item.content}</div></details>)}</div>
}

export function Modal({ open, title, children, onClose }: { open: boolean; title: string; children: ReactNode; onClose: () => void }) {
  useEffect(() => {
    if (!open) return
    const closeOnEscape = (event: KeyboardEvent) => { if (event.key === 'Escape') onClose() }
    document.addEventListener('keydown', closeOnEscape)
    return () => document.removeEventListener('keydown', closeOnEscape)
  }, [onClose, open])

  if (!open) return null
  return <div className="ds-modal-backdrop" role="presentation" onMouseDown={onClose}><section className="ds-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" onMouseDown={(event) => event.stopPropagation()}><div className="ds-modal-header"><h2 id="modal-title">{title}</h2><button className="ds-icon-button" onClick={onClose} aria-label="Cerrar ventana">×</button></div>{children}</section></div>
}

export function Dropdown({ label, children }: { label: string; children: ReactNode }) {
  return <details className="ds-dropdown"><summary>{label}</summary><div className="ds-dropdown-menu">{children}</div></details>
}
