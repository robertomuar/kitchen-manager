export const TERMS_VERSION = '2025-12-22';
export const COOKIE_VERSION = '2025-12-22';

export const TERMS_COOKIE = 'km_terms_accepted';
export const COOKIE_CONSENT_COOKIE = 'km_cookie_consent';

export const cookieConsentKey = Symbol('cookieConsent');

const readCookie = (name) => {
  if (typeof document === 'undefined') return null;
  const value = document.cookie
    .split('; ')
    .find((row) => row.startsWith(`${name}=`));

  if (!value) return null;

  return decodeURIComponent(value.split('=').slice(1).join('='));
};

const writeCookie = (name, value, days) => {
  if (typeof document === 'undefined') return;
  const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
  document.cookie = `${name}=${encodeURIComponent(value)}; Expires=${expires}; Path=/; SameSite=Lax`;
};

const parseJson = (value) => {
  if (!value) return null;
  try {
    return JSON.parse(value);
  } catch (error) {
    return null;
  }
};

export const getTermsAcceptance = () => {
  const raw = readCookie(TERMS_COOKIE);
  const parsed = parseJson(raw);

  if (!parsed || parsed.version !== TERMS_VERSION) {
    return null;
  }

  return parsed;
};

export const acceptTerms = () => {
  const payload = {
    version: TERMS_VERSION,
    acceptedAt: new Date().toISOString(),
  };

  writeCookie(TERMS_COOKIE, JSON.stringify(payload), 365);
  return payload;
};

export const getCookieConsent = () => {
  const raw = readCookie(COOKIE_CONSENT_COOKIE);
  const parsed = parseJson(raw);

  if (!parsed || parsed.version !== COOKIE_VERSION) {
    return null;
  }

  return parsed;
};

export const createCookieConsent = ({ choice, analytics }) => ({
  version: COOKIE_VERSION,
  choice,
  analytics: Boolean(analytics),
  acceptedAt: new Date().toISOString(),
});

export const saveCookieConsent = (payload) => {
  writeCookie(COOKIE_CONSENT_COOKIE, JSON.stringify(payload), 180);
};
