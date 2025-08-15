export default function Pagination({ page, pageSize, total, onPageChange }) {
  const totalPages = Math.max(1, Math.ceil((total || 0) / (pageSize || 10)))
  const current = Math.min(Math.max(1, page || 1), totalPages)

  const go = (p) => {
    if (p < 1 || p > totalPages || p === current) return
    onPageChange?.(p)
  }

  const pages = []
  for (let i = Math.max(1, current - 2); i <= Math.min(totalPages, current + 2); i += 1) {
    pages.push(i)
  }

  return (
    <nav aria-label="Page navigation">
      <ul className="pagination mb-0">
        <li className={`page-item ${current === 1 ? 'disabled' : ''}`}>
          <button className="page-link" onClick={() => go(current - 1)}>Previous</button>
        </li>
        {pages[0] > 1 && (
          <li className="page-item"><button className="page-link" onClick={() => go(1)}>1</button></li>
        )}
        {pages[0] > 2 && <li className="page-item disabled"><span className="page-link">…</span></li>}
        {pages.map((p) => (
          <li key={p} className={`page-item ${p === current ? 'active' : ''}`}>
            <button className="page-link" onClick={() => go(p)}>{p}</button>
          </li>
        ))}
        {pages[pages.length - 1] < totalPages - 1 && <li className="page-item disabled"><span className="page-link">…</span></li>}
        {pages[pages.length - 1] < totalPages && (
          <li className="page-item"><button className="page-link" onClick={() => go(totalPages)}>{totalPages}</button></li>
        )}
        <li className={`page-item ${current === totalPages ? 'disabled' : ''}`}>
          <button className="page-link" onClick={() => go(current + 1)}>Next</button>
        </li>
      </ul>
    </nav>
  )
}