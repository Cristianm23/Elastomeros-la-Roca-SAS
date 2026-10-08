export type AnalyticsEventName =
  | 'page_view' | 'product_view' | 'search' | 'quote_started' | 'quote_submitted'
  | 'contact_submitted' | 'login' | 'register' | 'cta_click' | 'whatsapp_click'
  | 'phone_click' | 'pdf_download' | 'frontend_error' | 'api_failure'

type AnalyticsProperties = Record<string, string | number | undefined>

const apiUrl = import.meta.env.VITE_API_URL || '/api'

export function trackEvent(event_name: AnalyticsEventName, data: {
  product_slug?: string
  properties?: AnalyticsProperties
} = {}): void {
  const body = JSON.stringify({
    event_name,
    path: `${window.location.pathname}${window.location.search ? '?present' : ''}`,
    product_slug: data.product_slug,
    properties: data.properties,
  })
  const url = `${apiUrl}/v1/analytics/events`
  if (navigator.sendBeacon) {
    navigator.sendBeacon(url, new Blob([body], { type: 'application/json' }))
    return
  }
  void fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body })
}
