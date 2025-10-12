<template>
  <div class="layout">
    <SideNav />

    <main class="main-content">
      <h2 class="page-title">ホーム</h2>

      <div class="post-list">
        <Message
          v-for="(post, index) in posts"
          :key="index"
          :title="post.title"
          :content="post.content"
          :likes="post.likes"
          @update:likes="updateLikes(index, $event)"
        />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import SideNav from '@/components/SideNav.vue'
import Message from '@/components/Message.vue'

const posts = ref([
  { title: 'test1', content: 'test', likes: 0 },
])

const updateLikes = (index, newLikes) => {
  posts.value[index].likes = newLikes
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
