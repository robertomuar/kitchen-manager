import { getJsonCookie, setJsonCookie } from '@/utils/cookies';

export const TERMS_VERSION = '2025-12-22';
export const COOKIE_VERSION = '2025-12-22';

export const TERMS_COOKIE = 'km_terms_accepted';
export const COOKIE_CONSENT_COOKIE = 'km_cookie_consent';

export const cookieConsentKey = Symbol('cookieConsent');

export const getTermsAcceptance = () => {
  const parsed = getJsonCookie(TERMS_COOKIE);

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

  setJsonCookie(TERMS_COOKIE, payload, 365);
  return payload;
};

export const getCookieConsent = () => {
  const parsed = getJsonCookie(COOKIE_CONSENT_COOKIE);

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
  setJsonCookie(COOKIE_CONSENT_COOKIE, payload, 180);
};
