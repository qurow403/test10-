<template>
  <header class="auth-header">
    <h1 class="logo">SHARE</h1>
    <nav>
      <NuxtLink to="/register" class="link">新規登録</NuxtLink>
      <NuxtLink to="/login" class="link">ログイン</NuxtLink>
    </nav>
  </header>
</template>

<style scoped>
.auth-header {
  background-color: #121b24;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 50px;
}

.logo {
  font-family: 'Fredoka One', cursive;
  font-size: 40px;
  letter-spacing: 2px;
}

nav {
  display: flex;
  gap: 20px;
}

.link {
  color: white;
  font-size: 18px;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}
</style>