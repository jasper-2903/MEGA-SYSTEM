const map = {
  pending: 'secondary',
  processing: 'primary',
  paid: 'primary',
  shipped: 'info',
  delivered: 'success',
  completed: 'success',
  cancelled: 'danger',
  failed: 'danger',
  refunded: 'warning',
}

export default function StatusBadge({ status }) {
  const s = String(status || '').toLowerCase()
  const cls = map[s] || 'secondary'
  return <span className={`badge text-bg-${cls} text-capitalize`}>{status}</span>
}