<template>
    <div class="app-container">
        <el-empty :description="`${title} 功能开发中`">
            <el-button type="primary" @click="$router.push('/index')">返回首页</el-button>
        </el-empty>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const title = computed(() => route.meta?.title || '当前模块');
</script>
