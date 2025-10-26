<template>
  <div class="layout">
    <SideNav />

    <main class="main-content">
      <h2 class="page-title">コメント</h2>

      <div class="post-card" v-if="post.id">
        <div class="post-header">
          <h3 class="post-username">{{ post.username }}</h3>

          <div class="post-actions">
            <button class="like-btn" :class="{ liked: isLiked }" @click="toggleLike">
              <img src="/assets/heart.png" alt="いいね" class="icon" />
              <span>{{ localLikes }}</span>
            </button>

            <button class="unlike-btn" :disabled="!isLiked" @click="unlike">
              <img src="/assets/cross.png" alt="解除" class="icon" />
            </button>

            <button class="share-btn" @click="$emit('share')" title="シェア">
              <img src="/assets/detail.png" alt="シェア" class="icon" />
            </button>
          </div>
        </div>

        <p class="post-content">{{ post.content }}</p>
      </div>

      <div class="comment-list">
        <label for="comment">コメント</label>
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
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import * as yup from 'yup'
import SideNav from '@/components/SideNav.vue'
import { useNuxtApp } from '#app'

const { $auth, $api } = useNuxtApp()
const route = useRoute()
const postId = route.params.id

const post = ref({})
const localLikes = ref(0)
const isLiked = ref(false)
const comments = ref([])

const comment = ref('')
const errors = ref({ comment: '' })

const fetchPost = async () => {
  try {
    const res = await $api.get(`/posts/${postId}`)
    post.value = res.data.post
    localLikes.value = res.data.likes_count
    isLiked.value = res.data.liked ?? false
    comments.value = res.data.comments
  } catch (err) {
    console.error('投稿データ取得エラー:', err)
  }

  // post.value = { id: 1, username: 'test1', content: 'test', likes_count: 0 }
  // localLikes.value = post.value.likes_count
  // isLiked.value = false
  // comments.value = [
  //   { user: 'test1', text: 'comment' },
  // ]
}

onMounted(fetchPost)

const toggleLike = async () => {
  try {
    let user
    if (process.client) user = $auth.currentUser
    if (!user) throw new Error('ログインが必要です')

    const res = await $api.post(`/posts/${postId}`, {
      uid: user.uid,
      name: user.displayName || '名無し'
    })
    localLikes.value = res.data.likes_count
    isLiked.value = res.data.liked
  } catch (err) {
    console.error(err)
  }
}

const unlike = async () => {
  if (!isLiked.value) return
  await toggleLike()
}

const schema = yup.object({
  comment: yup.string().required('コメントは必須です').max(120, '120文字以内で入力してください')
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
    let user
    if (process.client) user = $auth.currentUser
    if (!user) throw new Error('ログインが必要です')

    const res = await $api.put(`/posts/${postId}`, {
      comment: comment.value,
      uid: user.uid,
      name: user.displayName || '名無し'
    })
    comments.value.push(res.data)
    comment.value = ''
  } catch (err) {
    console.error(err)
    alert(err.message || 'コメント投稿に失敗しました')
  }
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

.post-card {
  background-color: #232a36;
  border-radius: 12px;
  padding: 15px 20px;
  margin-bottom: 20px;
}

.post-header {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 12px;
}

.post-username {
  font-weight: bold;
  font-size: 18px;
}

.post-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.icon {
  width: 18px;
  height: 18px;
  object-fit: contain;
  vertical-align: middle;
}

.like-btn {
  background: none;
  border: none;
  color: #aaa;
  font-size: 16px;
  cursor: pointer;
  transition: transform 0.1s ease, color 0.3s;
  display: flex;
  align-items: center;
  gap: 5px;
}

.like-btn.liked {
  color: #f87171;
  transform: scale(1.1);
}

.unlike-btn {
  background: none;
  border: none;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
}

.unlike-btn:hover {
  color: #f87171;
}

.unlike-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.share-btn {
  background: none;
  border: none;
  color: #fff;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
}

.share-btn:hover {
  color: #60a5fa;
}

.post-content {
  margin-top: 10px;
  color: #ddd;
  font-size: 15px;
}

.comment-list {
  margin-bottom: 20px;
}

.comment-list label {
  display: block;
  text-align: center;
  font-weight: bold;
  font-size: 16px;
  margin-bottom: 10px;
  color: #fff;
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
</style>
