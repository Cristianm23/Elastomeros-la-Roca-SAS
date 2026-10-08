import { render, screen, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { ContactPage, ProductDetailPage, QuotePage } from './PublicPages'

vi.mock('../services/analytics', () => ({ trackEvent: vi.fn() }))

describe('public forms and routes', () => {
  it('renders a product route with a quote CTA', () => {
    render(<ProductDetailPage slug="sellos-hidraulicos" />)

    expect(screen.getByRole('heading', { name: /sellos hidráulicos/i })).toBeInTheDocument()
    expect(screen.getAllByRole('link', { name: /solicitar cotización/i })[0]).toHaveAttribute('href', '/cotizacion?producto=sellos-hidraulicos')
  })

  it('submits the contact form and shows success state', async () => {
    const user = userEvent.setup()
    vi.stubGlobal('fetch', vi.fn().mockResolvedValue(new Response('{}', { status: 201 })))
    render(<ContactPage />)

    await user.type(screen.getByLabelText(/Nombre/), 'Ana')
    await user.type(screen.getByLabelText(/Empresa/), 'Empresa SAS')
    await user.type(screen.getByLabelText(/Email/), 'ana@example.com')
    await user.type(screen.getByLabelText(/¿En qué podemos ayudarle?/), 'Necesito información')
    await user.click(screen.getByRole('checkbox'))
    await user.click(screen.getByRole('button', { name: /enviar solicitud/i }))

    await waitFor(() => expect(screen.getByRole('status')).toHaveTextContent('Solicitud recibida'))
  })

  it('starts quote flow with a selected product', () => {
    render(<QuotePage />)

    expect(screen.getByRole('heading', { name: /cuéntenos sobre su necesidad/i })).toBeInTheDocument()
    expect(screen.getByLabelText(/Producto o solución/)).toBeRequired()
  })
})
