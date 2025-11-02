<template>
  <header class="auth-header">
    <div class="logo-container">
      <img src="/assets/logo.png" alt="SHAREロゴ" class="logo-img" />
    </div>

    <nav class="right-nav">
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
  width: 100%;
  box-sizing: border-box;
}

.logo-container {
  display: flex;
  align-items: center;
}

.logo-img {
  height: 40px;
}

.right-nav {
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