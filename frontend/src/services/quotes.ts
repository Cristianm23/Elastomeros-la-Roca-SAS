type QuotePayload = {
  company_name: string
  contact_name: string
  email: string
  phone: string
  city: string
  required_date: string
  observations: string
  product: string
  product_slug?: string
  quantity: string
  attachment?: File
  website: string
  consent: boolean
}
import { reportFrontendError } from './observability'

const apiUrl = import.meta.env.VITE_API_URL || '/api'

export async function submitQuoteRequest(payload: QuotePayload): Promise<void> {
  const body = new FormData()
  body.append('company_name', payload.company_name)
  body.append('contact_name', payload.contact_name)
  body.append('email', payload.email)
  body.append('phone', payload.phone)
  body.append('city', payload.city)
  body.append('required_date', payload.required_date)
  body.append('observations', payload.observations)
  body.append('website', payload.website)
  body.append('consent', payload.consent ? '1' : '0')
  if (payload.product_slug) body.append('product_slug', payload.product_slug)
  body.append('items[0][description]', payload.product)
  body.append('items[0][quantity]', payload.quantity)
  if (payload.attachment) body.append('attachment', payload.attachment)

  const response = await fetch(`${apiUrl}/v1/quotes`, {
    method: 'POST',
    headers: { Accept: 'application/json' },
    body,
  })

  if (!response.ok) {
    reportFrontendError(new Error(`Quote API ${response.status}`), 'api_failure')
    const data = await response.json().catch(() => null) as { message?: string } | null
    throw new Error(data?.message || 'No fue posible enviar la solicitud de cotización.')
  }
}
