import { render, screen } from '@testing-library/react'
import App from './App'

vi.mock('./services/analytics', () => ({ trackEvent: vi.fn() }))

describe('application routes', () => {
  afterEach(() => {
    window.history.replaceState({}, '', '/')
  })

  it('renders the public product route', () => {
    window.history.replaceState({}, '', '/productos')
    render(<App />)

    expect(screen.getByRole('heading', { name: /productos para que su operación/i })).toBeInTheDocument()
  })

  it('renders the design system route', () => {
    window.history.replaceState({}, '', '/design-system')
    render(<App />)

    expect(screen.getByRole('heading', { name: /design system/i })).toBeInTheDocument()
  })
})
