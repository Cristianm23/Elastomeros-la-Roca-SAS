import { expect, test } from '@playwright/test'

test('administrador navega dashboard, productos y cotizaciones', async ({ page }) => {
  await page.addInitScript(() => localStorage.setItem('auth_token', 'e2e-admin-token'))
  await page.route('**/api/v1/analytics/events', async (route) => {
    await route.fulfill({ status: 202, contentType: 'application/json', body: '{"data":{"recorded":true}}' })
  })
  await page.route('**/api/v1/admin/dashboard', async (route) => {
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: { quotes: 1, pending_quotes: 1, contacts: 2, products: 3, users: 4, activity: [] } }) })
  })
  await page.route('**/api/v1/admin/analytics', async (route) => {
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: { totals: [], products: [], recent: [] } }) })
  })
  await page.route('**/api/v1/admin/products', async (route) => {
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [] }) })
  })
  await page.route('**/api/v1/admin/quotes', async (route) => {
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify({ data: [] }) })
  })
  await page.goto('/admin')
  await expect(page.getByRole('heading', { name: 'Resumen' })).toBeVisible()
  await page.getByRole('button', { name: 'Productos' }).click()
  await expect(page.getByRole('heading', { name: 'Productos' })).toBeVisible()
  await page.getByRole('button', { name: 'Cotizaciones' }).click()
  await expect(page.getByRole('heading', { name: 'Cotizaciones' })).toBeVisible()
})
