<template>
  <div class="header">
    <el-row>
      <el-col :span="4">
        <div class="left-title">後台管理</div>
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
        <el-dropdown trigger="click" >
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
              <el-dropdown-item>Action 1</el-dropdown-item>
              <el-dropdown-item>Action 2</el-dropdown-item>
              <el-dropdown-item>Action 3</el-dropdown-item>
              <el-dropdown-item disabled>Action 4</el-dropdown-item>
              <el-dropdown-item divided>Action 5</el-dropdown-item>
            </el-dropdown-menu>
          </template>
        </el-dropdown>

      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
const user = usePage().props.auth.user;
console.log(user);

</script>

<style scoped>
.header {
  background-color: #ffffff;
  color: #333;
  text-align: center;
  line-height: 60px;
}

.left-title {
  text-align: left;
  padding-left: 25px;
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