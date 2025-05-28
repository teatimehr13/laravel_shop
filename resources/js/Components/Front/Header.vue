<template>
  <div class="header">
    <el-row>
      <el-col :span="12" style="margin: auto;">
        <div class="left-title">
          <a :href="route('categories.front.index')">
            前台
          </a>

          <div>
            <slot name="extraRight" />
          </div>
        </div>
      </el-col>


      <el-col :span="12" class="right-content">
        <div>
          <el-button link>
            <!-- <a :href="route('cart.cart')">
              <el-icon size="20">
                <ShoppingCart />
              </el-icon>
            </a> -->
            <Link :href="route('cart.cart')">
             <el-icon size="20">
                <ShoppingCart />
              </el-icon>
            </Link>
          </el-button>
        </div>

        <div class="profile" v-if="user">
          <el-dropdown trigger="click">
            <div class="el-dropdown-link right-text" style="display: flex; align-items: center;">
              <div class="right-text-content">
                <div class="right-text">
                  <el-avatar :src="user.avatar || '/storage/user/5556468.png'" size="small" />
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
                <el-dropdown-item @click="logout">
                  登出
                </el-dropdown-item>
                <el-dropdown-item>
                  <a :href="route('orders.index')">訂單</a>
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>

        <div v-else>
          <el-button link>
            <a :href="route('login')">
              登入
            </a>
          </el-button>
        </div>

      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { usePage, Link, router } from '@inertiajs/vue3';
const user = usePage().props.auth.user;
// console.log(user);
function logout() {
  router.post(route('logout'))
}


</script>

<style scoped>
.header {
  background-color: #ffffff;
  color: #333;
  text-align: center;
  height: 100%;
  display: grid;
  align-items: center;
}

::v-deep(.header > .el-row) {
  height: 100%;
}

.left-title {
  text-align: left;
  padding-left: 25px;
  font-weight: bold;
  font-size: 20px;
  color: #55595c;
  display: flex;
}

.right-text {
  display: grid;
  align-items: center;
  gap: 5px;
}

.right-text-content {
  display: flex;
  gap: 8px;
  font-weight: bold;
  color: #55595c;
  /* font-size: 1rem; */
}

.right-content {
  display: flex;
  justify-content: end;
  padding-right: 10px;
  align-items: center;
  gap: 20px;
}


/* .right-content div {
  position: relative;
  padding: 8px;
} */

/* .right-content div:not(:first-child)::before {
  content: '';
  display: inline-block;
  margin: 0px;
  padding: 0px;
  height: 7px;
  width: 1px;
  background: #606266;
  position: absolute;
  top: calc((100% - 7px) / 2);
  left: 0px;
} */

.profile {
  display: flex;
  justify-content: end;
}

::v-deep(.el-dropdown__popper.el-popper) {
  margin-top: -15px !important;
}
</style>