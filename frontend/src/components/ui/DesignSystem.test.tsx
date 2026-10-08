import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { Button, Input, Modal, Tabs } from './DesignSystem'

describe('design system components', () => {
  it('renders an accessible input and loading button', () => {
    render(<><Input label="Correo" required /><Button loading>Enviar</Button></>)

    expect(screen.getByLabelText(/Correo/)).toBeRequired()
    expect(screen.getByRole('button', { name: /enviar/i })).toBeDisabled()
    expect(screen.getByRole('status', { name: 'Cargando' })).toBeInTheDocument()
  })

  it('supports modal escape and tab keyboard navigation', async () => {
    const user = userEvent.setup()
    const onClose = vi.fn()
    render(<><Modal open title="Ayuda" onClose={onClose}>Contenido</Modal><Tabs tabs={[{ label: 'Uno', content: 'Primero' }, { label: 'Dos', content: 'Segundo' }]} /></>)

    await user.keyboard('{Escape}')
    expect(onClose).toHaveBeenCalledOnce()
    await user.click(screen.getByRole('tab', { name: 'Dos' }))
    expect(screen.getByRole('tabpanel')).toHaveTextContent('Segundo')
  })
})
