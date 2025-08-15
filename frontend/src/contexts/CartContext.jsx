import { createContext, useContext, useEffect, useMemo, useState } from 'react'

const CartContext = createContext(null)
const CART_KEY = 'cart_items'

export function CartProvider({ children }) {
  const [items, setItems] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY) || '[]')
    } catch {
      return []
    }
  })

  useEffect(() => {
    localStorage.setItem(CART_KEY, JSON.stringify(items))
  }, [items])

  const addItem = (product, quantity = 1) => {
    setItems((prev) => {
      const idx = prev.findIndex((p) => p.product.id === product.id)
      if (idx !== -1) {
        const copy = [...prev]
        copy[idx] = { ...copy[idx], quantity: copy[idx].quantity + quantity }
        return copy
      }
      return [...prev, { product, quantity }]
    })
  }

  const removeItem = (productId) => {
    setItems((prev) => prev.filter((p) => p.product.id !== productId))
  }

  const updateQuantity = (productId, quantity) => {
    setItems((prev) => prev.map((p) => p.product.id === productId ? { ...p, quantity: Math.max(1, quantity) } : p))
  }

  const clear = () => setItems([])

  const totalItems = items.reduce((sum, it) => sum + it.quantity, 0)
  const totalPrice = items.reduce((sum, it) => sum + (Number(it.product.price || it.product.unit_price || 0) * it.quantity), 0)

  const value = useMemo(() => ({ items, addItem, removeItem, updateQuantity, clear, totalItems, totalPrice }), [items, totalItems, totalPrice])

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>
}

export function useCart() {
  const ctx = useContext(CartContext)
  if (!ctx) throw new Error('useCart must be used within CartProvider')
  return ctx
}