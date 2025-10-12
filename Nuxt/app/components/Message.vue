<template>
  <div class="message-card">
    <div class="message-header">
      <p class="message-title">{{ title }}</p>

      <div class="message-actions">
        <!-- ❤️いいね -->
        <button class="like-btn" :class="{ liked: isLiked }" @click="toggleLike">
          <img src="/assets/heart.png" alt="いいね" class="icon"/>
          <span>{{ localLikes }}</span>
        </button>

        <!-- × いいね解除 -->
        <button class="unlike-btn" @click="unlike" title="いいね解除">
          <img src="/assets/cross.png" alt="解除" class="icon" />
        </button>

        <!-- ↪ シェア -->
        <button class="share-btn" @click="$emit('share')" title="シェア">
          <img src="/assets/detail.png" alt="シェア" class="icon" />
        </button>
      </div>
    </div>
    <p class="message-content">{{ content }}</p>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  title: String,
  content: String,
  likes: Number,
})

const emit = defineEmits(['update:likes'])

// 内部状態
const localLikes = ref(props.likes)
const isLiked = ref(false)

// 親の props が更新されたら同期
watch(
  () => props.likes,
  (val) => {
    localLikes.value = val
  }
)

// ❤️ いいね押下時
const toggleLike = () => {
  if (!isLiked.value) {
    isLiked.value = true
    localLikes.value++
    emit('update:likes', localLikes.value)
  }
}

// ✖ いいね解除
const unlike = () => {
  if (isLiked.value) {
    isLiked.value = false
    localLikes.value = Math.max(0, localLikes.value - 1)
    emit('update:likes', localLikes.value)
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
  justify-content: space-between;
  align-items: center;
}

.message-title {
  font-weight: bold;
  font-size: 18px;
}

.message-actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* 画像アイコン（❤️✖↪） */
.icon {
  width: 18px;  /* タイトル文字サイズに合わせる */
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

.message-content {
  margin-top: 10px;
  color: #ddd;
  font-size: 15px;
}
</style>
