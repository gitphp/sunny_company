<template>
    <div class="admin-tags">
        <span
            v-for="tag in tagsView.visited"
            :key="tag.path"
            class="admin-tag"
            :class="{ 'is-active': route.path === tag.path }"
            @click="$router.push(tag.path)"
        >
            {{ tag.title }}
            <el-icon v-if="!tag.affix" @click.stop="closeTag(tag.path)"><Close /></el-icon>
        </span>
    </div>
</template>

<script setup>
import { watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTagsViewStore } from '../stores/tagsView';

const route = useRoute();
const router = useRouter();
const tagsView = useTagsViewStore();

watch(
    () => route.path,
    () => {
        tagsView.addView(route);
    },
    { immediate: true },
);

function closeTag(path) {
    const current = route.path === path;
    tagsView.closeView(path);
    if (current) {
        const last = tagsView.visited[tagsView.visited.length - 1];
        router.push(last?.path || '/index');
    }
}
</script>
