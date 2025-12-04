# Browser console errors: what they mean and how to resolve them

The following notes explain the browser console messages observed while using the application and provide suggested actions.

## `Autofocus processing was blocked because a document already has a focused element`

**Meaning:** Chrome prevents an element with `autofocus` from taking focus when another element already has focus. This is a standard browser safeguard and not an application failure.

**How to fix/avoid:** No action is required unless the blocked focus breaks a workflow. If it does, review pages that set `autofocus` on multiple inputs or open modals while another control is focused and remove redundant `autofocus` attributes.

## `GET chrome-extension://pejdijmoenmkgeppbflobdenhhabjlaj/... net::ERR_FILE_NOT_FOUND`

**Meaning:** The browser is trying to load files from a local extension with ID `pejdijmoenmkgeppbflobdenhhabjlaj`, but the extension is missing or disabled on the current profile. These requests are unrelated to the Kitchen Manager codebase.

**How to fix/avoid:** Disable the extension, reinstall it, or ignore the warning. It does not originate from the application and will not affect production behavior.

## `POST https://kitchenmanager.duckdns.org/logout 419`

**Meaning:** The server returned HTTP 419 (Page Expired) for the logout request, which usually indicates a missing or stale CSRF token or an expired session in Laravel applications.

**How to fix/avoid:** Refresh the page before retrying, ensure the logout request includes the current CSRF token, and confirm that the session is still active. If the token is injected by JavaScript, verify that the meta tag or Axios configuration still matches Laravel's CSRF expectations.
