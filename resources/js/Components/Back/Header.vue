<template>
  <div class="header px-4">
    <el-row>
      <el-col :span="4">
        <div class="left-title ms-6">後台管理</div>
      </el-col>
      <el-col :span="4">
        <div></div>
      </el-col>
      <el-col :span="4">
        <div></div>
      </el-col>
      <el-col :span="4">
        <div></div>
      </el-col>
      <el-col :span="4">
        <div></div>
      </el-col>
      <el-col :span="4" class="right-content">
        <el-dropdown trigger="click">
          <div class="el-dropdown-link right-text" style="display: flex; align-items: center;">
            <div class="right-text-content">
              <div class="right-text">
                <el-avatar src="/storage/user/5556468.png" />
              </div>
              <div class="right-text">
                {{ user.name }}
              </div>
            </div>
            <el-icon class="el-icon--right">
              <arrow-down />
            </el-icon>
          </div>
          <template #dropdown>
            <el-dropdown-menu>
              <el-dropdown-item>
                <a :href="route('categories.front.index')">
                  去前台
                </a>
              </el-dropdown-item>
              <el-dropdown-item @click="logout">
                登出
              </el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>

      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { usePage, Link, router } from '@inertiajs/vue3';
const user = usePage().props.auth.user;
console.log(user);
function logout() {
  router.post(route('logout'))
}
</script>

<style scoped>
.header {
  background-color: #ffffff;
  color: #333;
  text-align: center;
  line-height: 60px;
  border-bottom: 1px solid #f3f3f3;
}

.left-title {
  text-align: left;
  font-weight: bold;
  font-size: 20px;
  color: #55595c;
}

.right-text {
  display: grid;
  align-items: center;
  gap: 10px;
}

.right-text-content {
  display: flex;
  gap: 8px;
  font-weight: bold;
  color: #55595c;
  font-size: 1rem;
}

.right-content {
  display: flex;
  justify-content: end;
  padding-right: 10px;
}
</style>