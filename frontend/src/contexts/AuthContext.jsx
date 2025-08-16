import { createContext, useContext, useEffect, useMemo, useState } from 'react'
import { AuthAPI } from '../services/api'
import { setAuthToken, clearAuthToken } from '../utils/authToken'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [token, setToken] = useState(localStorage.getItem('auth_token') || null)
  const [loading, setLoading] = useState(!!token)

  useEffect(() => {
    let isMounted = true
    async function bootstrap() {
      if (!token) return
      try {
        const data = await AuthAPI.me()
        if (!isMounted) return
        setUser(data?.user || data)
      } catch {
        setUser(null)
        setToken(null)
        clearAuthToken()
      } finally {
        if (isMounted) setLoading(false)
      }
    }
    setAuthToken(token)
    bootstrap()
    return () => {
      isMounted = false
    }
  }, [token])

  const login = async (credentials) => {
    const data = await AuthAPI.login(credentials)
    const newToken = data?.token || data?.access_token
    if (newToken) {
      setAuthToken(newToken)
      setToken(newToken)
    }
    const currentUser = data?.user || (await AuthAPI.me())
    setUser(currentUser?.user || currentUser)
    return currentUser
  }

  const register = async (payload) => {
    const data = await AuthAPI.register(payload)
    const newToken = data?.token || data?.access_token
    if (newToken) {
      setAuthToken(newToken)
      setToken(newToken)
    }
    const currentUser = data?.user || (await AuthAPI.me())
    setUser(currentUser?.user || currentUser)
    return currentUser
  }

  const logout = async () => {
    try {
      await AuthAPI.logout()
    } catch {
      // ignore
    }
    clearAuthToken()
    setToken(null)
    setUser(null)
  }

  const value = useMemo(() => ({
    user,
    token,
    isAuthenticated: !!user && !!token,
    role: user?.role || user?.type || 'customer',
    loading,
    login,
    register,
    logout,
  }), [user, token, loading])

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within AuthProvider')
  return ctx
}