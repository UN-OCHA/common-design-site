import env from './_env'

describe('OCHAServices', () => {
  beforeAll(async() => {
    await page.goto(env.baseUrl);
  });

  it('should contain up to four links in the Related Platforms section', async() => {
    const relatedPlatformsLimit = 4;
    await page.waitForSelector('.cd-ocha-services__section:first-child');
    const relatedPlatformsLength = await page.$$eval('.cd-ocha-services__section:first-child', nodeList => nodeList.length);
    await expect(relatedPlatformsLength).toBeLessThanOrEqual(relatedPlatformsLimit);
  });

  it('should NOT contain links with default text in the Related Platforms section', async() => {
    const relatedPlatformsText = await page.$eval('.cd-ocha-services__section:first-child :is(.cd-ocha-services__link, .cd-ocha-dropdown__link) a', (el) => el.innerHTML);
    await expect(relatedPlatformsText).not.toMatch('Customizable');
  });

  it('should contain eight links in the Other OCHA Services section', async() => {
    const otherOchaServicesLimit = 8;
    await page.waitForSelector('.cd-ocha-services__section:not(:first-child)');
    const otherOchaServicesLength = await page.$$eval('.cd-ocha-services__section:not(:first-child)', nodeList => nodeList.length);
    await expect(otherOchaServicesLength).toBeLessThanOrEqual(otherOchaServicesLimit);
  });

  it('should contain specific links in the Other OCHA Services section', async() => {
    const otherOchaServicesCorporate = [
      'Financial Tracking Service',
      'Humanitarian Data Exchange',
      'Humanitarian ID',
      'ReliefWeb Response',
      'Inter-Agency Standing Committee',
      'OCHA website',
      'ReliefWeb',
      'Virtual OSOCC'
    ];
    const otherOchaServicesCorporateHref = [
      'https://fts.unocha.org/',
      'https://data.humdata.org/',
      'https://auth.humanitarian.id/',
      'https://response.reliefweb.int/',
      'https://interagencystandingcommittee.org/',
      'https://unocha.org/',
      'https://reliefweb.int/',
      'https://vosocc.unocha.org/'
    ];
    const otherOchaServices = await page.$$eval('.cd-ocha-services__section:not(:first-child) .cd-ocha-services__link a', text => { return text.map(text => text.textContent).slice(0, 8) });
    const otherOchaServicesHref = await page.$$eval('.cd-ocha-services__section:not(:first-child) .cd-ocha-services__link a', anchors => { return anchors.map(anchor => anchor.href).slice(0, 8) });
    await expect(otherOchaServices).toEqual(otherOchaServicesCorporate);
    await expect(otherOchaServicesHref).toEqual(otherOchaServicesCorporateHref);
  });

  it('should include a button to UNOCHA.org DS list', async() => {
    const seeAllButtonHref = await page.$eval('.cd-ocha-services__see-all', (el) => el.href);
    const expectedUrl = 'https://www.unocha.org/ocha-digital-services';
    await expect(seeAllButtonHref).toEqual(expectedUrl);
  });
});
