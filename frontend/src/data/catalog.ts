export type Product = {
  slug: string
  name: string
  category: string
  categorySlug: string
  description: string
  specifications: Array<[string, string]>
  accent: string
}

export const categories = [
  { slug: 'sellos-y-juntas', name: 'Sellos y juntas', description: 'Protección confiable para uniones, ejes y sistemas hidráulicos.' },
  { slug: 'mangueras-industriales', name: 'Mangueras industriales', description: 'Conducción segura para fluidos, aire y procesos exigentes.' },
  { slug: 'perfiles-y-recubrimientos', name: 'Perfiles y recubrimientos', description: 'Diseños a medida para aislamiento, protección y continuidad.' },
]

export const products: Product[] = [
  { slug: 'sellos-hidraulicos', name: 'Sellos hidráulicos', category: 'Sellos y juntas', categorySlug: 'sellos-y-juntas', description: 'Elementos de estanqueidad para cilindros y sistemas hidráulicos con alta exigencia de presión.', specifications: [['Material', 'NBR / PU'], ['Aplicación', 'Sistemas hidráulicos'], ['Temperatura', '-30 °C a 110 °C']], accent: '#2d6aa3' },
  { slug: 'juntas-industriales', name: 'Juntas industriales', category: 'Sellos y juntas', categorySlug: 'sellos-y-juntas', description: 'Juntas fabricadas para mantener la integridad de equipos y líneas de proceso.', specifications: [['Material', 'EPDM / NBR'], ['Formato', 'Corte y moldeado'], ['Aplicación', 'Flanges y equipos']], accent: '#4d78a6' },
  { slug: 'manguera-alta-presion', name: 'Manguera de alta presión', category: 'Mangueras industriales', categorySlug: 'mangueras-industriales', description: 'Manguera flexible para conducción confiable en operaciones industriales.', specifications: [['Refuerzo', 'Textil / acero'], ['Presión', 'Según configuración'], ['Aplicación', 'Aceite y aire']], accent: '#245789' },
  { slug: 'perfil-de-silicona', name: 'Perfil de silicona', category: 'Perfiles y recubrimientos', categorySlug: 'perfiles-y-recubrimientos', description: 'Perfiles de silicona para sellar, aislar y proteger componentes.', specifications: [['Material', 'Silicona'], ['Dureza', 'Según diseño'], ['Formato', 'Extrusión']], accent: '#6f9bc9' },
  { slug: 'recubrimiento-rodillos', name: 'Recubrimiento de rodillos', category: 'Perfiles y recubrimientos', categorySlug: 'perfiles-y-recubrimientos', description: 'Recubrimientos que mejoran el desempeño y prolongan la vida útil de rodillos.', specifications: [['Material', 'Caucho técnico'], ['Acabado', 'Según aplicación'], ['Servicio', 'Diagnóstico y fabricación']], accent: '#5d87b4' },
  { slug: 'amortiguador-industrial', name: 'Amortiguador industrial', category: 'Perfiles y recubrimientos', categorySlug: 'perfiles-y-recubrimientos', description: 'Soluciones de absorción de impacto para reducir vibración y desgaste.', specifications: [['Material', 'Elastómero técnico'], ['Dureza', 'Personalizable'], ['Aplicación', 'Maquinaria industrial']], accent: '#35689b' },
]

export const services = [
  { slug: 'asesoria-tecnica', name: 'Asesoría técnica', description: 'Analizamos su aplicación y recomendamos el material, la geometría y la solución más conveniente.' },
  { slug: 'fabricacion-a-medida', name: 'Fabricación a medida', description: 'Convertimos planos, muestras o necesidades de operación en componentes fabricados para su proceso.' },
  { slug: 'mantenimiento-y-reparacion', name: 'Mantenimiento y reparación', description: 'Ayudamos a recuperar el desempeño de componentes elastoméricos y reducir paradas no programadas.' },
]
