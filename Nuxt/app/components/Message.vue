<template>
  <div class="message-card">
    <div class="message-header">
      <p class="message-username">{{ username }}</p>

      <div class="message-actions">
        <button class="like-btn" :class="{ liked: isLiked }" @click="toggleLike">
          <img src="/assets/heart.png" alt="いいね" class="icon"/>
          <span>{{ localLikes }}</span>
        </button>

        <button class="unlike-btn" :disabled="!isLiked" @click="unlike">
          <img src="/assets/cross.png" alt="解除" class="icon" />
        </button>

        <button class="share-btn" @click="sharePost" title="シェア">
          <img src="/assets/detail.png" alt="シェア" class="icon" />
        </button>
      </div>
    </div>
    <p class="message-content">{{ content }}</p>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  postId: { type: Number, required: true },
  username: String,
  content: String,
  likes: Number,
  initialLiked: { type: Boolean, default: false },
  uid: { type: String, required: true },
  name: { type: String, default: '名無し' },
})

const emit = defineEmits(['update:likes'])
const localLikes = ref(props.likes)
const isLiked = ref(props.initialLiked)

watch(() => props.likes, (val) => {
  localLikes.value = val
})

const toggleLike = async () => {
  try {
    const res = await axios.post(`/api/posts/${props.postId}/toggle-like`, {
      uid: props.uid,
      name: props.name
    })

    isLiked.value = res.data.liked
    localLikes.value = res.data.likes_count
    emit('update:likes', localLikes.value)

  } catch (err) {
    console.error('いいね更新エラー:', err)
    alert('いいねの更新に失敗しました')
  }
}

const sharePost = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    alert('リンクをコピーしました！')
  } catch (err) {
    console.error('共有エラー:', err)
  }
}
</script>

<style scoped>
.message-card {
  background-color: #232a36;
  border-radius: 12px;
  padding: 15px 20px;
}

.message-header {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  gap: 12px;
}

.message-username {
  font-weight: bold;
  font-size: 18px;
  margin: 0;
}

.message-actions {
  display: flex;
  align-items: right;
  gap: 10px;
  margin-top: 0;
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

.message-content {
  margin-top: 10px;
  color: #ddd;
  font-size: 15px;
}
</style>
