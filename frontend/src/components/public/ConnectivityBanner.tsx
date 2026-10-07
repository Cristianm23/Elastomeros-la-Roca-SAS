import { useEffect, useState } from 'react'

export function ConnectivityBanner() {
  const [online, setOnline] = useState(() => navigator.onLine)
  const [restored, setRestored] = useState(false)

  useEffect(() => {
    const handleOnline = () => {
      setOnline(true)
      setRestored(true)
      window.setTimeout(() => setRestored(false), 4000)
    }
    const handleOffline = () => setOnline(false)
    window.addEventListener('online', handleOnline)
    window.addEventListener('offline', handleOffline)
    return () => {
      window.removeEventListener('online', handleOnline)
      window.removeEventListener('offline', handleOffline)
    }
  }, [])

  if (online && !restored) return null
  return <div className={`connectivity-banner ${online ? 'is-online' : 'is-offline'}`} role="status">{online ? 'Conexión restaurada.' : 'Sin conexión. Las solicitudes requieren internet.'}</div>
}
