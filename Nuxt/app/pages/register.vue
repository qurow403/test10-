<template>
  <div class="register-page">
    <AuthHeader />
    <div class="register-container">
      <h2>新規登録</h2>

      <Form :validation-schema="$schemas?.register" @submit="handleRegister">
        <div>
          <Field name="username" v-slot="{ field, errorMessage }">
            <input v-bind="field" type="text" placeholder="ユーザーネーム" />
            <span class="error">{{ errorMessage }}</span>
          </Field>
        </div>

        <div>
          <Field name="email" v-slot="{ field, errorMessage }">
            <input v-bind="field" type="email" placeholder="メールアドレス" />
            <span class="error">{{ errorMessage }}</span>
          </Field>
        </div>

        <div>
          <Field name="password" v-slot="{ field, errorMessage }">
            <input v-bind="field" type="password" placeholder="パスワード" />
            <span class="error">{{ errorMessage }}</span>
          </Field>
        </div>

        <button type="submit">新規登録</button>
      </Form>
    </div>
  </div>
</template>

<script setup>
import AuthHeader from '~/components/AuthHeader.vue'
import { Form, Field } from 'vee-validate'
import { createUserWithEmailAndPassword, updateProfile } from 'firebase/auth'
import { computed } from 'vue'

const { $auth, $schemas } = useNuxtApp()

const schemas = computed(() => $schemas || {})

const handleRegister = async (values) => {
  try {
    const userCredential = await createUserWithEmailAndPassword($auth, values.email, values.password)
    await updateProfile(userCredential.user, { displayName: values.username })
    alert('登録が完了しました！')
    navigateTo('/posts')
  } catch (error) {
    console.error('登録エラー:', error.message)
    alert('登録に失敗しました：' + error.message)
  }
}
</script>

<style scoped>
.register-page {
  background-color: #121b24;
  height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.register-container {
  background: white;
  padding: 40px 60px;
  border-radius: 10px;
  margin-top: 80px;
  width: 400px;
  text-align: center;
}

h2 {
  margin-bottom: 25px;
}

input {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border: 1px solid #555;
  border-radius: 8px;
}

button {
  background-color: #5a3ef2;
  color: white;
  border: none;
  padding: 12px 0;
  width: 100%;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  box-shadow: 0 4px 0 #2e2c72;
}

button:hover {
  background-color: #4733d6;
}

.error {
  color: red;
  font-size: 13px;
  margin-top: 4px;
  display: block;
}
</style>
