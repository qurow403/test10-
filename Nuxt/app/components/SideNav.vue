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
        <NuxtLink to="/logout">
          <img src="/assets/logout.png" alt="ログアウト" class="menu-icon" />
          ログアウト
        </NuxtLink>
      </li>
    </ul>

    <div class="share-section">
      <h3>シェア</h3>
      <form @submit.prevent="handleShare">
        <textarea v-model="message" placeholder="メッセージを入力"></textarea>
        <button type="submit" class="share-btn">シェアする</button>
      </form>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'

const schema = yup.object({
  message: yup
    .string()
    .required('メッセージは必須です')
    .max(120, '120文字以内で入力してください'),
})

const { handleSubmit } = useForm({ validationSchema: schema })
const { value: message } = useField('message')

const handleShare = handleSubmit((values) => {
  console.log('送信された内容:', values)
  alert('シェアしました！')
  message.value = ''
})
</script>

<style scoped>
.side-nav {
  width: 270px;
  background-color: #0f141c;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.logo {
  text-align: center;
  margin-bottom: 40px;
}

.logo-img {
  width: 160px;
  height: auto;
}

/* メニュー */
.menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu li {
  margin-bottom: 15px;
}

.menu a {
  color: white;
  text-decoration: none;
  font-size: 16px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.menu a:hover {
  color: #8c9eff;
}

.menu-icon {
  width: 20px;
  height: 20px;
  object-fit: contain;
}

.share-section {
  margin-top: 30px;
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
</style>