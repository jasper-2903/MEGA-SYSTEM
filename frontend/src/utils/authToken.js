export const tokenStorageKey = 'auth_token'

export function setAuthToken(token) {
  if (token) {
    localStorage.setItem(tokenStorageKey, token)
  } else {
    localStorage.removeItem(tokenStorageKey)
  }
}

export function clearAuthToken() {
  localStorage.removeItem(tokenStorageKey)
}