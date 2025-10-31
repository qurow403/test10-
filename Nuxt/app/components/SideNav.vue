<template>
  <nav class="side-nav">
    <div class="logo">
      <img src="/assets/logo.png" alt="SHAREロゴ" class="logo-img" />
    </div>

    <ul class="menu">
      <li>
        <NuxtLink to="/">
          <img src="/assets/home.png" alt="ホーム" class="menu-icon" />
          ホーム
        </NuxtLink>
      </li>
      <li>
        <button @click="handleLogout" class="logout-btn">
          <img src="/assets/logout.png" alt="ログアウト" class="menu-icon" />
          ログアウト
        </button>
      </li>
    </ul>

    <div v-if="$auth.currentUser" class="share-section">
      <h3>シェア</h3>
      <Form @submit="onSubmit" :validation-schema="schema">
        <Field name="content" v-slot="{ field, errorMessage }">
          <textarea v-bind="field" placeholder="メッセージを入力"></textarea>
          <span class="error">{{ errorMessage }}</span>
        </Field>
        <button type="submit" class="share-btn" :disabled="loading">
          {{ loading ? '送信中…' : 'シェアする' }}
        </button>
      </Form>
    </div>

    <div v-else class="login-warning">
      <p>投稿するにはログインが必要です。</p>
      <NuxtLink to="/login" class="login-link">ログインする</NuxtLink>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useNuxtApp } from '#app'
import { Form, Field } from 'vee-validate'
import * as yup from 'yup'

const emit = defineEmits(['post:created'])
const { $auth, $api } = useNuxtApp()
const loading = ref(false)

const schema = yup.object({
  content: yup.string().required('メッセージは必須です').max(120, '120文字以内で入力してください')
})

const onSubmit = async (values) => {
  if (!process.client) return
  try {
    loading.value = true
    const user = $auth.currentUser
    if (!user) throw new Error('ログインが必要です')

    const token = await user.getIdToken()

    const res = await $api.post(
      '/posts',
      { content: values.content, uid: user.uid, name: user.displayName || '名無し' },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
      }
)

    emit('post:created', res.data)
    values.content = ''
  } catch (err) {
    console.error('投稿エラー:', err)
    alert(err.response?.data?.message || err.message || '投稿に失敗しました')
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  try {
    if (process.client) {
      await $auth.signOut()
      window.location.href = '/login'
    }
  } catch (err) {
    console.error(err)
    alert('ログアウトに失敗しました')
  }
}
</script>

<style scoped>
.side-nav {
  width: 270px;
  background-color: #0f141c;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.logo {
  text-align: left;
  margin-bottom: 40px;
}

.logo-img {
  width: 160px;
  height: auto;
}

.menu {
  list-style: none;
  padding: 0;
  margin: 0;
  margin: 0 0 8px 0;
  width: 100%;
}

.menu li {
  margin-bottom: 15px;
}

.menu a,
.logout-btn {
  color: white;
  text-decoration: none;
  font-size: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 10px 15px;
  border-radius: 8px;
  transition: background 0.2s;
}

.menu a:hover,
.logout-btn:hover {
  color: #8c9eff;
  background-color: rgba(255, 255, 255, 0.1);
}

.menu-icon {
  width: 24px;
  height: 20px;
  object-fit: contain;
}

.share-section h3 {
  margin-bottom: 10px;
  font-size: 16px;
}

textarea {
  width: 100%;
  height: 80px;
  background: none;
  border: 1px solid #ccc;
  color: white;
  border-radius: 6px;
  padding: 10px;
  margin-bottom: 10px;
  resize: none;
}

.share-btn {
  width: 100%;
  background-color: #5b3dfd;
  color: white;
  border: none;
  border-radius: 20px;
  padding: 8px 0;
  cursor: pointer;
  transition: background 0.2s;
}

.share-btn:hover {
  background-color: #715bff;
}

.error {
  color: #e74c3c;
  font-size: 13px;
  margin-top: 4px;
  display: block;
}
</style>
