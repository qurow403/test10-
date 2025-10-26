<template>
  <div class="layout">
    <SideNav @post:created="fetchPosts" />

    <main class="main-content">
      <h2 class="page-title">ホーム</h2>

      <div class="post-list">
        <p v-if="posts.length === 0" class="no-posts">まだ投稿がありません。</p>

        <Message
          v-for="(post, index) in posts"
          :key="post.id"
          :username="post.username"
          :content="post.content"
          :likes="post.likes"
          @update:likes="updateLikes(index, $event)"
        />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SideNav from '@/components/SideNav.vue'
import Message from '@/components/Message.vue'

const { $api } = useNuxtApp()
const posts = ref([])

const fetchPosts = async () => {
  try {
    let token
    if (process.client && $auth.currentUser) {
      token = await $auth.currentUser.getIdToken()
    }

    const res = await $api.get('/posts', {
      headers: token ? { Authorization: `Bearer ${token}` } : {}
    })

    posts.value = res.data
  } catch (err) {
    console.error('投稿取得エラー:', err)
  }

  // posts.value = [
  //   { id: 1, username: 'test1', content: 'test', likes: 0 },
  // ]
}

onMounted(fetchPosts)

const updateLikes = async (index, newLikes) => {
  try {
    let user, token
    if (process.client && $auth.currentUser) {
      user = $auth.currentUser
      token = await user.getIdToken()
    }
    if (!user) throw new Error('ログインが必要です')

    const post = posts.value[index]
    const res = await $api.post(
      `/posts/${post.id}`,
      { uid: user.uid, name: user.displayName || '名無し' },
      { headers: { Authorization: `Bearer ${token}` } }
    )

    posts.value[index].likes = res.data?.likes_count ?? post.likes
  } catch (err) {
    console.error('いいね更新エラー:', err)
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
</style>
