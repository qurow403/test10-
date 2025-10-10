import { initializeApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'

export default defineNuxtPlugin(() => {
  const firebaseConfig = {
    apiKey: "AIzaSyC49P15Ydqi9GdlPLsFT2p7jX0hogjcFAw",
    authDomain: "share-app-1ca44.firebaseapp.com",
    projectId: "share-app-1ca44",
    storageBucket: "share-app-1ca44.firebasestorage.app",
    messagingSenderId: "299293538225",
    appId: "1:299293538225:web:df020d8bcf84193085ef90",
    measurementId: "G-5PLMRQ5N29"
};

  const app = initializeApp(firebaseConfig)
  const auth = getAuth(app)

  // Nuxt全体で使えるようにprovide
  return {
    provide: {
      auth,
    },
  }
})
