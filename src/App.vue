<template>
  <Layout>
    <h1 class="wp-heading-inline">WP Agents</h1>

    <hr class="wp-header-end"/>

    <Nav/>

    <main class="tw:clear-left">
      <RouterView/>
    </main>
  </Layout>
</template>

<script setup lang="ts">
import Layout from "./components/Layout.vue";
import {RouterView} from "vue-router";
import Nav from "./components/Nav.vue";
</script>
