<template>
  <Layout>
    <h1 class="wp-heading-inline">WP Agents</h1>

    <hr class="wp-header-end"/>

    <Nav/>

    <main class="tw:clear-left">
      <RouterView v-slot="{Component}">
        <transition name="fade" mode="out-in">
          <keep-alive>
            <component :is="Component"/>
          </keep-alive>
        </transition>
      </RouterView>
    </main>
  </Layout>
</template>

<script setup lang="ts">
import Layout from "./components/Layout.vue";
import {RouterView} from "vue-router";
import Nav from "./components/Nav.vue";
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity .1s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
