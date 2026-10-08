const apiUrl = import.meta.env.VITE_API_URL || '/api'

async function request(path: string, options: RequestInit = {}) {
  const token = localStorage.getItem('auth_token')
  const response = await fetch(`${apiUrl}/v1/admin${path}`, {
    ...options,
    headers: { Accept: 'application/json', 'Content-Type': 'application/json', ...(token ? { Authorization: `Bearer ${token}` } : {}), ...options.headers },
  })
  if (!response.ok) throw new Error(response.status === 403 ? 'No tienes permisos para esta acción.' : 'No fue posible cargar la información administrativa.')
  return response.status === 204 ? null : response.json()
}

export const adminApi = {
  dashboard: () => request('/dashboard'),
  quotes: (query = '') => request(`/quotes${query}`),
  updateQuote: (id: number, data: object) => request(`/quotes/${id}`, { method: 'PATCH', body: JSON.stringify(data) }),
  contacts: () => request('/contacts'),
  users: () => request('/users'),
  activity: () => request('/activity'),
  analytics: () => request('/analytics'),
  catalog: (type: string) => request(`/${type}`),
  saveCatalog: (type: string, data: object) => request(`/${type}`, { method: 'POST', body: JSON.stringify(data) }),
  content: (type: string) => request(`/content/${type}`),
  saveContent: (type: string, data: object) => request(`/content/${type}`, { method: 'POST', body: JSON.stringify(data) }),
}
