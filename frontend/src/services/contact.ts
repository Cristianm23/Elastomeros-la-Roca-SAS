type ContactPayload = {
  name: string
  company_name: string
  email: string
  phone: string
  subject: string
  message: string
  consent: boolean
}

const apiUrl = import.meta.env.VITE_API_URL || '/api'

export async function submitContactMessage(payload: ContactPayload): Promise<void> {
  const response = await fetch(`${apiUrl}/v1/contact`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify(payload),
  })

  if (!response.ok) {
    const data = await response.json().catch(() => null) as { message?: string } | null
    throw new Error(data?.message || 'No fue posible enviar la solicitud.')
  }
}
