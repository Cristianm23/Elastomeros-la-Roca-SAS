import { expect, test } from '@playwright/test'

test('cliente navega por productos y comienza una cotización', async ({ page }) => {
  await page.route('**/api/v1/analytics/events', async (route) => {
    await route.fulfill({ status: 202, contentType: 'application/json', body: '{"data":{"recorded":true}}' })
  })
  await page.goto('/')
  await expect(page.getByRole('heading', { name: /materiales que sostienen la industria/i })).toBeVisible()
  await page.getByRole('link', { name: /explorar productos/i }).click()
  await expect(page).toHaveURL(/\/productos$/)
  await page.getByRole('link', { name: /sellos hidráulicos/i }).first().click()
  await expect(page).toHaveURL(/\/productos\/sellos-hidraulicos$/)
  await page.locator('a[href="/cotizacion?producto=sellos-hidraulicos"]').click()
  await expect(page).toHaveURL(/\/cotizacion\?producto=sellos-hidraulicos$/)
  await expect(page.getByLabel('Producto o solución')).toHaveValue('sellos-hidraulicos')
})

test('cliente puede enviar contacto sin duplicar la solicitud', async ({ page }) => {
  let submissions = 0
  await page.route('**/api/v1/analytics/events', async (route) => {
    await route.fulfill({ status: 202, contentType: 'application/json', body: '{"data":{"recorded":true}}' })
  })
  await page.route('**/api/v1/contact', async (route) => {
    submissions += 1
    await route.fulfill({ status: 201, contentType: 'application/json', body: '{"data":{"id":1}}' })
  })
  await page.goto('/contacto')
  await page.getByLabel('Nombre').fill('Cliente E2E')
  await page.getByLabel('Empresa').fill('Empresa E2E')
  await page.getByLabel('Email').fill('e2e@example.com')
  await page.getByLabel('¿En qué podemos ayudarle?').fill('Necesito una cotización')
  await page.getByRole('checkbox').check()
  await page.getByRole('button', { name: /enviar solicitud/i }).click()
  await expect(page.getByRole('status')).toContainText('Solicitud recibida')
  expect(submissions).toBe(1)
})
