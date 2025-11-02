<template>
  <div v-if="loading" class="loading">
    <p>読み込み中...</p>
  </div>

  <div v-else>
    <div class="layout" v-if="user">
      <SideNav />

      <main class="main-content">
        <h2 class="page-title">コメント</h2>

        <section class="post-section">
          <Message
            v-for="(p, index) in posts"
            :key="p.id"
            :post-id="p.id"
            :username="p.username"
            :content="p.content"
            :likes="p.likes"
            :uid="p.uid"
            :name="p.name"
            @update:likes="updatePostLikes(index, $event)"
          />
        </section>

        <section class="comment-section">
          <h3 class="sub-title">コメント</h3>
          <div class="comment-list">
            <div v-for="(comment, index) in comments" :key="index" class="comment-item">
              <strong>{{ comment.user }}</strong>
              <p>{{ comment.text }}</p>
            </div>
          </div>

          <form class="comment-form" @submit.prevent="onCommentSubmit">
            <textarea id="comment" v-model="comment" placeholder="コメントを入力"></textarea>
            <span v-if="errors.comment" class="error">{{ errors.comment }}</span>
            <button type="submit" class="comment-btn">コメント</button>
          </form>
        </section>
      </main>
    </div>

    <div v-else class="login-redirect">
      <p>投稿を見るにはログインが必要です。</p>
      <NuxtLink to="/login" class="login-link">ログインする</NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import * as yup from 'yup'
import SideNav from '@/components/SideNav.vue'
import Message from '@/components/Message.vue'
import { useNuxtApp } from '#app'

const { $auth, $api } = useNuxtApp()
const route = useRoute()
const postId = route.params.id

const user = ref(null)
const posts = ref([])
const comments = ref([])
const comment = ref('')
const errors = ref({ comment: '' })
const loading = ref(true)

const fetchPosts = async () => {
  try {
    const token = await user.value.getIdToken()
    const res = await $api.get('/posts', {
      headers: { Authorization: `Bearer ${token}` },
    })
    posts.value = res.data
  } catch (err) {
    console.error('投稿一覧取得エラー:', err)
  }
}

const fetchPostComments = async () => {
  try {
    const token = await user.value.getIdToken()
    const res = await $api.get(`/posts/${postId}`, {
      headers: { Authorization: `Bearer ${token}` },
      params: { firebase_uid: user.value.uid },
    })
    comments.value = res.data.comments || []
  } catch (err) {
    console.error('コメント取得エラー:', err)
  }
}

const updatePostLikes = (index, newLikes) => {
  posts.value[index].likes = newLikes
}

const schema = yup.object({
  comment: yup.string().required('コメントは必須です').max(120, '120文字以内で入力してください'),
})

const onCommentSubmit = async () => {
  errors.value.comment = ''
  try {
    await schema.validate({ comment: comment.value })
  } catch (err) {
    errors.value.comment = err.message
    return
  }

  try {
    const token = await user.value.getIdToken()
    const res = await $api.put(
      `/posts/${postId}`,
      { comment: comment.value, uid: user.value.uid, name: user.value.displayName || '名無し' },
      { headers: { Authorization: `Bearer ${token}` } },
    )
    comments.value.push(res.data)
    comment.value = ''
  } catch (err) {
    console.error('コメント投稿失敗:', err)
    alert('コメント投稿に失敗しました')
  }
}

onMounted(async () => {
  if (!process.client) return

  const current = $auth.currentUser
  if (current) {
    user.value = current
    await fetchPosts()
    await fetchPostComments()
  } else {
    $auth.onAuthStateChanged(async (u) => {
      if (u) {
        user.value = u
        await fetchPosts()
        await fetchPostComments()
      } else {
        loading.value = false
      }
    })
  }
  loading.value = false
})
</script>

<style scoped>
.layout {
  display: flex;
  height: 100vh;
  background-color: #1a1f29;
  color: #fff;
}

.main-content {
  flex: 1;
  padding: 30px 50px;
  overflow-y: auto;
}

.page-title {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 20px;
}

.sub-title {
  font-size: 18px;
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 10px;
  color: #fff;
  text-align: center;
}

.post-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.comment-section {
  margin-top: 30px;
}

.comment-list {
  margin-bottom: 20px;
}

.comment-item {
  background-color: #2a3140;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 10px;
}

.comment-form {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.comment-form textarea {
  width: 100%;
  height: 70px;
  border-radius: 8px;
  border: 1px solid #ccc;
  padding: 8px;
  background: none;
  color: white;
  resize: none;
}

.comment-btn {
  align-self: flex-end;
  background-color: #5b3dfd;
  border: none;
  color: white;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  transition: 0.2s;
}

.comment-btn:hover {
  background-color: #715bff;
}

.error {
  color: #ff7070;
  font-size: 13px;
}

.loading {
  display: flex;
  height: 100vh;
  align-items: center;
  justify-content: center;
  color: white;
  background-color: #1a1f29;
}
</style>
