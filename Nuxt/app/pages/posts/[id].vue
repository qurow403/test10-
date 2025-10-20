<template>
  <div class="layout">
    <!-- ① サイドナビ -->
    <SideNav />

    <!-- ② メインコンテンツ -->
    <main class="main-content">
      <h2 class="page-title">コメント</h2>

      <!-- 投稿内容 -->
      <div class="post-card">
        <div class="post-header">
          <h3 class="post-title">{{ post.title }}</h3>

          <div class="post-actions">
            <!-- ❤️いいね -->
            <button class="like-btn" :class="{ liked: isLiked }" @click="toggleLike">
              <img src="/assets/heart.png" alt="いいね" class="icon" />
              <span>{{ localLikes }}</span>
            </button>

            <!-- × いいね解除 -->
            <button class="unlike-btn" :disabled="!isLiked" @click="unlike" title="いいね解除">
              <img src="/assets/cross.png" alt="解除" class="icon" />
            </button>

            <!-- ↪ シェア -->
            <button class="share-btn" @click="$emit('share')" title="シェア">
              <img src="/assets/detail.png" alt="シェア" class="icon" />
            </button>
          </div>
        </div>

        <p class="post-content">{{ post.content }}</p>
      </div>

      <!-- コメント一覧 -->
      <div class="comment-list">
        <div
          v-for="(comment, index) in comments"
          :key="index"
          class="comment-item"
        >
          <strong>{{ comment.user }}</strong>
          <p>{{ comment.text }}</p>
        </div>
      </div>

      <!-- コメント追加フォーム -->
      <form class="comment-form" @submit.prevent="handleCommentSubmit">
        <label for="comment">コメント</label>
        <textarea id="comment" v-model="comment" placeholder="コメントを入力"></textarea>
        <span v-if="errorMessage" class="error">{{ errorMessage }}</span>
        <button type="submit" class="comment-btn">コメント</button>
      </form>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import SideNav from '@/components/SideNav.vue'

const { $api } = useNuxtApp()

// ======================
// 基本設定
// ======================
const route = useRoute()
const postId = route.params.id

const post = ref({})
const localLikes = ref(0)
const isLiked = ref(false)
const comments = ref([])

// ======================
// 投稿データ取得（GET /posts/{id}）
// ======================
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
}

onMounted(fetchPost)

// ======================
// ❤️ いいね切り替え（POST /posts/{id}）
// ======================
const toggleLike = async () => {
  try {
    const res = await $api.post(`/posts/${postId}`)
    localLikes.value = res.data.likes_count
    isLiked.value = res.data.liked
  } catch (err) {
    console.error('いいね処理エラー:', err)
  }
}

// ======================
// ✖ いいね解除（unlike）
// ======================
const unlike = async () => {
  if (!isLiked.value) return
  await toggleLike() // toggleLike が解除も兼ねる
}

// ======================
// 📝 コメント投稿（PUT /posts/{id}）
// ======================
const schema = yup.object({
  comment: yup
    .string()
    .required('コメントは必須です')
    .max(120, '120文字以内で入力してください'),
})

const { handleSubmit } = useForm({ validationSchema: schema })
const { value: comment, errorMessage } = useField('comment', undefined, {
  validateOnInput: true,
})

const handleCommentSubmit = handleSubmit(async (values) => {
  try {
    const res = await $api.put(`/posts/${postId}`, {
      comment: values.comment,
    })
    comments.value.push(res.data)
    comment.value = '' // 入力欄をクリア
  } catch (err) {
    console.error('コメント送信エラー:', err)
    errorMessage.value =
      err.response?.data?.errors?.comment?.[0] ||
      'コメント送信に失敗しました'
  }
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

/* タイトル */
.page-title {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 20px;
}

/* 投稿カード */
.post-card {
  background-color: #232a36;
  border-radius: 12px;
  padding: 15px 20px;
  margin-bottom: 20px;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.post-title {
  font-weight: bold;
  font-size: 18px;
}

/* ❤️✖↪ 共通アクション */
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

/* ❤️いいねボタン */
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

/* ✖解除ボタン */
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

/* ↪シェアボタン */
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

/* コメント関連 */
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
</style>
