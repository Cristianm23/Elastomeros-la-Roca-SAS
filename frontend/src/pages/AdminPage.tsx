import { useEffect, useState } from 'react'
import type { ReactNode } from 'react'
import { Alert, Button, Card, Input, Select, Textarea } from '../components/ui'
import { adminApi } from '../services/admin'

type Section = 'dashboard' | 'analytics' | 'quotes' | 'contacts' | 'products' | 'categories' | 'services' | 'faqs' | 'testimonials' | 'blog' | 'users' | 'activity'
const sections: Array<[Section, string]> = [['dashboard','Resumen'], ['analytics','Analytics'], ['quotes','Cotizaciones'], ['contacts','Contactos'], ['products','Productos'], ['categories','Categorías'], ['services','Servicios'], ['faqs','FAQ'], ['testimonials','Testimonios'], ['blog','Blog'], ['users','Usuarios'], ['activity','Auditoría']]

export function AdminPage() {
  const [section, setSection] = useState<Section>('dashboard')
  const [data, setData] = useState<any>(null)
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(true)
  useEffect(() => { setLoading(true); setError(''); const load: () => Promise<any> = section === 'dashboard' ? adminApi.dashboard : section === 'analytics' ? adminApi.analytics : section === 'quotes' ? adminApi.quotes : section === 'contacts' ? adminApi.contacts : section === 'users' ? adminApi.users : section === 'activity' ? adminApi.activity : ['faqs','testimonials','blog'].includes(section) ? () => adminApi.content(section) : () => adminApi.catalog(section); load().then(setData).catch((e: Error) => setError(e.message)).finally(() => setLoading(false)) }, [section])
  return <div className="admin-shell"><aside className="admin-sidebar"><div className="admin-brand">LR <span>Administración</span></div><nav aria-label="Administración">{sections.map(([id, label]) => <button className={section === id ? 'is-active' : ''} onClick={() => setSection(id)} key={id}>{label}</button>)}</nav></aside><main className="admin-main"><header className="admin-header"><div><p className="eyebrow">Panel administrativo</p><h1>{sections.find(([id]) => id === section)?.[1]}</h1></div><span className="admin-secure">Acceso protegido</span></header>{loading && <p className="admin-loading">Cargando información…</p>}{error && <Alert title="No se pudo cargar el panel" tone="danger">{error} Verifica tu token y permisos.</Alert>}{!loading && !error && <AdminContent section={section} data={data} onRefresh={() => setSection(section)} />}</main></div>
}

function AdminContent({ section, data, onRefresh }: { section: Section; data: any; onRefresh: () => void }) {
  if (section === 'dashboard') return <div className="admin-cards">{[['Cotizaciones', data.data.quotes], ['Pendientes', data.data.pending_quotes], ['Contactos', data.data.contacts], ['Productos', data.data.products], ['Usuarios', data.data.users]].map(([label, value]) => <Card title={String(label)} key={String(label)}><strong className="admin-number">{String(value)}</strong></Card>)}<Card title="Actividad reciente"><ul className="admin-activity">{data.data.activity.map((item: any) => <li key={item.id}>{item.action} · {item.created_at}</li>)}</ul></Card></div>
  if (section === 'analytics') return <AnalyticsSummary data={data.data} />
  if (section === 'quotes') return <QuoteTable data={data.data?.data || []} onRefresh={onRefresh} />
  if (section === 'contacts') return <SimpleTable rows={data.data?.data || []} columns={['name','company_name','subject','status','created_at']} />
  if (section === 'users') return <SimpleTable rows={data.data?.data || []} columns={['name','email','company_name','roles']} />
  if (section === 'activity') return <SimpleTable rows={data.data?.data || []} columns={['action','auditable_type','ip_address','created_at']} />
  return <ContentManager section={section} rows={data.data?.data || []} onRefresh={onRefresh} />
}

function AnalyticsSummary({ data }: { data: any }) {
  return <div className="admin-cards">
    <Card title="Eventos registrados"><SimpleTable rows={data.totals} columns={['event_name', 'total']} /></Card>
    <Card title="Productos con más interés"><SimpleTable rows={data.products} columns={['product_slug', 'total']} /></Card>
    <Card title="Errores recientes"><SimpleTable rows={data.recent.filter((item: any) => ['frontend_error', 'api_failure'].includes(item.event_name))} columns={['event_name', 'path', 'created_at']} /></Card>
  </div>
}

function QuoteTable({ data, onRefresh }: { data: any[]; onRefresh: () => void }) {
  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('')
  const filtered = data.filter((quote) => `${quote.public_reference} ${quote.company_name} ${quote.email}`.toLowerCase().includes(search.toLowerCase()) && (!status || quote.status === status))
  return <section><div className="admin-toolbar"><Input label="Buscar" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Referencia, empresa o correo" /><Select label="Estado" value={status} onChange={(e) => setStatus(e.target.value)} options={['pending','reviewing','quoted','accepted','rejected','completed','cancelled'].map((value) => ({ value, label: value }))} /></div><SimpleTable rows={filtered} columns={['public_reference','company_name','email','status','created_at']} action={(row) => <Select label={`Estado ${row.public_reference}`} value={row.status} onChange={async (e) => { await adminApi.updateQuote(row.id, { status: e.target.value }); onRefresh() }} options={['pending','reviewing','quoted','accepted','rejected','completed','cancelled'].map((value) => ({ value, label: value }))} />} /></section>
}

function ContentManager({ section, rows, onRefresh }: { section: Section; rows: any[]; onRefresh: () => void }) {
  const [title, setTitle] = useState(''); const [body, setBody] = useState(''); const [categoryId, setCategoryId] = useState('')
  const catalog = ['products', 'categories', 'services'].includes(section)
  const save = async () => {
    const data = catalog
      ? section === 'products' ? { name: title, category_id: Number(categoryId), description: body, is_active: true } : { name: title, description: body, is_active: true }
      : section === 'faqs' ? { question: title, answer: body } : section === 'testimonials' ? { author: title, quote: body } : { title, body, status: 'draft' }
    if (catalog) await adminApi.saveCatalog(section, data); else await adminApi.saveContent(section, data)
    setTitle(''); setBody(''); setCategoryId(''); onRefresh()
  }
  return <section><Card title={`Crear ${section}`}><div className="admin-form"><Input label={catalog ? 'Nombre' : section === 'testimonials' ? 'Autor' : 'Título o pregunta'} value={title} onChange={(e) => setTitle(e.target.value)} />{section === 'products' && <Input label="ID de categoría" type="number" value={categoryId} onChange={(e) => setCategoryId(e.target.value)} />}<Textarea label={catalog && section !== 'products' ? 'Descripción' : 'Contenido'} value={body} onChange={(e) => setBody(e.target.value)} /><Button onClick={save} disabled={!title || !body || (section === 'products' && !categoryId)}>Guardar</Button></div></Card><SimpleTable rows={rows} columns={section === 'faqs' ? ['question','is_active'] : section === 'testimonials' ? ['author','company','quote'] : section === 'blog' ? ['title','status','created_at'] : ['name','slug','is_active']} /></section>
}

function SimpleTable({ rows, columns, action }: { rows: any[]; columns: string[]; action?: (row: any) => ReactNode }) { return <div className="admin-table-wrap"><table className="admin-table"><thead><tr>{columns.map((column) => <th key={column}>{column}</th>)}{action && <th>Acciones</th>}</tr></thead><tbody>{rows.map((row) => <tr key={row.id || row.public_reference}>{columns.map((column) => <td key={column}>{Array.isArray(row[column]) ? row[column].map((item: any) => item.name).join(', ') : typeof row[column] === 'object' ? row[column]?.name || '—' : String(row[column] ?? '—')}</td>)}{action && <td>{action(row)}</td>}</tr>)}</tbody></table></div> }
