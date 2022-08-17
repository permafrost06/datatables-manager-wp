<script setup>
import { ref } from "vue";

const heading = ref("Datatables Manager");
</script>

<template>
  <div v-loading.lock="loading">
    <el-header>
      <el-row>
        <el-col
          ><h1>{{ heading }}</h1></el-col
        >
      </el-row>
    </el-header>
    <el-main class="dtm-admin-app-main">
      <router-view v-slot="{ Component }">
        <template v-if="Component">
          <Suspense @pending="pendingHandler" @resolve="resolveHandler">
            <component :is="Component"></component>
          </Suspense>
        </template>
      </router-view>
    </el-main>
  </div>
</template>

<style lang="scss">
.dtm-admin-app-main .el-input > * {
  & > input {
    border: none;
    background: none;
    padding: 0;
  }

  & > input:focus {
    box-shadow: none;
    outline: none;
  }

  & > input:focus-visible {
    outline: none;
  }
}
</style>
