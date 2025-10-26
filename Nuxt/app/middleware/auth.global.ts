export default defineNuxtRouteMiddleware(async () => {
    const nuxtApp = useNuxtApp()
    const { $auth } = nuxtApp

if (process.client) {
    await new Promise<void>((resolve) => {
    const unsubscribe = $auth.onAuthStateChanged((user) => {
            if (!user) navigateTo('/login')
            unsubscribe()
            resolve()
        })
    })
    }
})
