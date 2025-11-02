<template>
  <div v-if="isAuthenticated" class="layout">
    <SideNav @post:created="handlePostCreated" />

    <main class="main-content">
      <h2 class="page-title">ホーム</h2>

      <div class="post-list">
        <p v-if="posts.length === 0" class="no-posts">まだ投稿がありません。</p>

        <Message
          v-for="(post, index) in posts"
          :key="post.id"
          :post-id="post.id"
          :username="post.username"
          :content="post.content"
          :likes="post.likes"
          :initial-liked="post.liked"
          :uid="post.uid"
          :name="post.name"
          @update:likes="updateLikes(index, $event)"
        />
      </div>
    </main>
  </div>

  <div v-else class="login-redirect">
    <p>投稿を見るにはログインが必要です。</p>
    <NuxtLink to="/login" class="login-link">ログインする</NuxtLink>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SideNav from '@/components/SideNav.vue'
import Message from '@/components/Message.vue'

const { $api, $auth } = useNuxtApp()
const posts = ref([])
const isAuthenticated = ref(false)

const fetchPosts = async () => {
  try {
    const user = $auth.currentUser
    if (!user) {
      isAuthenticated.value = false
      return
    }

    const token = await user.getIdToken()
    const res = await $api.get('/posts', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })

    posts.value = res.data
    isAuthenticated.value = true
  } catch (err) {
    console.error('投稿取得エラー:', err)
    if (err.response?.status === 401) {
      isAuthenticated.value = false
    }
  }

  // posts.value = [
  //   { id: 1, username: 'test1', content: 'test', likes: 0 },
  // ]
}


const handlePostCreated = (newPost) => {
  posts.value.unshift(newPost)
}

onMounted(fetchPosts)

const updateLikes = async (index, newLikes) => {
  try {
    const user = $auth.currentUser
    if (!user) throw new Error('ログインが必要です')

    const token = await user.getIdToken()
    const post = posts.value[index]

    const res = await $api.post(
      `/posts/${post.id}/like`,
      { uid: user.uid, name: user.displayName || '名無し' },
      { headers: { Authorization: `Bearer ${token}` } }
    )

    posts.value[index].likes = res.data.likes_count
  } catch (err) {
    console.error('いいね更新エラー:', err.response?.data || err.message)
  }

  // posts.value[index].likes += 1
}
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

.post-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.login-redirect {
  color: white;
  text-align: center;
  padding-top: 200px;
}
.login-link {
  color: #8c9eff;
  text-decoration: underline;
  cursor: pointer;
}
</style>
