import axios from 'axios'

export default defineNuxtPlugin((nuxtApp) => {
  const api = axios.create({
    baseURL: 'http://localhost/api',
    credentials: 'include',
  })

  nuxtApp.provide('api', api)
})
