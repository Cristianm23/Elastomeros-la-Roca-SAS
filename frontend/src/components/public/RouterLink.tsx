import type { AnchorHTMLAttributes, MouseEventHandler, ReactNode } from 'react'
import { trackEvent } from '../../services/analytics'

export function Link({ href, children, onClick, ...props }: AnchorHTMLAttributes<HTMLAnchorElement> & { href: string; children: ReactNode }) {
  const handleClick: MouseEventHandler<HTMLAnchorElement> = (event) => {
    onClick?.(event)
    if (event.defaultPrevented) return
    if (href.startsWith('https://wa.me/')) trackEvent('whatsapp_click')
    else if (href.startsWith('tel:')) trackEvent('phone_click')
    else if (href === '/cotizacion') trackEvent('cta_click', { properties: { target: 'quote' } })
    else if (href.endsWith('.pdf')) trackEvent('pdf_download')
  }
  return <a href={href} onClick={handleClick} {...props}>{children}</a>
}
