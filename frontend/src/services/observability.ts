import { trackEvent } from './analytics'

export function reportFrontendError(error: unknown, type: 'frontend_error' | 'api_failure' = 'frontend_error'): void {
  const errorType = error instanceof Error ? error.name : 'UnknownError'
  trackEvent(type, { properties: { error_type: errorType } })
}

export function installErrorReporting(): () => void {
  const onError = (event: ErrorEvent) => reportFrontendError(event.error || event.message)
  const onRejection = (event: PromiseRejectionEvent) => reportFrontendError(event.reason)
  window.addEventListener('error', onError)
  window.addEventListener('unhandledrejection', onRejection)
  return () => {
    window.removeEventListener('error', onError)
    window.removeEventListener('unhandledrejection', onRejection)
  }
}
