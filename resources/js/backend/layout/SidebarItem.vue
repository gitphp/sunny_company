<template>
    <el-sub-menu v-if="hasChildren" :index="item.id">
        <template #title>
            <el-icon v-if="item.menu_icon">
                <component :is="item.menu_icon" />
            </el-icon>
            <span>{{ item.menu_name }}</span>
        </template>
        <sidebar-item v-for="child in visibleChildren" :key="child.id" :item="child" />
    </el-sub-menu>
    <el-menu-item v-else :index="item.menu_path">
        <el-icon v-if="item.menu_icon">
            <component :is="item.menu_icon" />
        </el-icon>
        <template #title>{{ item.menu_name }}</template>
    </el-menu-item>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
});

const visibleChildren = computed(() => (props.item.children ?? []).filter((child) => !child.is_button));
const hasChildren = computed(() => visibleChildren.value.length > 0);
</script>
