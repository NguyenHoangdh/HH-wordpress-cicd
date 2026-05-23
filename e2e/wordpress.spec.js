const { test, expect } = require('@playwright/test');

const BASE_URL = 'http://localhost:8082';

test('WordPress homepage loads successfully', async ({ page }) => {
  const response = await page.goto(BASE_URL);
  expect(response.status()).toBeLessThan(400);
});

test('WordPress admin login page is accessible', async ({ page }) => {
  await page.goto(`${BASE_URL}/wp-login.php`);
  await expect(page.locator('#user_login')).toBeVisible();
  await expect(page.locator('#user_pass')).toBeVisible();
  await expect(page.locator('#wp-submit')).toBeVisible();
});

test('WordPress admin login works', async ({ page }) => {
  await page.goto(`${BASE_URL}/wp-login.php`);
  await page.fill('#user_login', 'admin');
  await page.fill('#user_pass', 'admin123');
  await page.click('#wp-submit');
  await expect(page).toHaveURL(/wp-admin/);
});