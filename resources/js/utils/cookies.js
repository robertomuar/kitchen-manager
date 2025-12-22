export const getJsonCookie = (name) => {
  if (typeof document === 'undefined' || !name) return null;
  const match = document.cookie
    .split('; ')
    .find((row) => row.startsWith(`${encodeURIComponent(name)}=`));

  if (!match) return null;

  const raw = match.split('=').slice(1).join('=');
  if (!raw) return null;

  try {
    return JSON.parse(decodeURIComponent(raw));
  } catch (error) {
    return null;
  }
};

export const setJsonCookie = (name, value, days) => {
  if (typeof document === 'undefined' || !name) return;
  const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
  const encodedName = encodeURIComponent(name);
  const encodedValue = encodeURIComponent(JSON.stringify(value));
  document.cookie = `${encodedName}=${encodedValue}; Expires=${expires}; Path=/; SameSite=Lax`;
};

export const removeCookie = (name) => {
  if (typeof document === 'undefined' || !name) return;
  const encodedName = encodeURIComponent(name);
  document.cookie = `${encodedName}=; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; SameSite=Lax`;
};
