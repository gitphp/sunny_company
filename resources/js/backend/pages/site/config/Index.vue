<template>
    <div class="app-container">
        <el-tabs v-model="active">
            <el-tab-pane v-for="group in groups" :key="group.group" :label="group.group_label" :name="group.group">
                <el-form label-width="100px" style="max-width:720px">
                    <el-form-item v-for="item in group.items" :key="item.conf_key" :label="item.conf_desc || item.conf_key">
                        <el-input
                            v-if="item.input_type === 'textarea' || item.input_type === 'json'"
                            v-model="form[item.conf_key]"
                            type="textarea"
                            :rows="3"
                        />
                        <el-input v-else v-model="form[item.conf_key]" :placeholder="item.input_type === 'image' || item.input_type === 'file' ? '请输入地址' : ''" />
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>
        <div class="toolbar">
            <el-button v-if="can('cms:config:edit')" type="primary" :loading="saving" @click="submit">保存</el-button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { ElMessage } from 'element-plus';
import { fetchSiteConfigs, saveSiteConfigs } from '../../../api/config';
import { useUserStore } from '../../../stores/user';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const groups = ref([]);
const active = ref('basic');
const form = reactive({});
const saving = ref(false);

async function loadConfigs() {
    const { data } = await fetchSiteConfigs();
    groups.value = data.groups ?? [];
    if (!groups.value.some((item) => item.group === active.value) && groups.value[0]) {
        active.value = groups.value[0].group;
    }
    groups.value.forEach((group) => {
        (group.items || []).forEach((item) => {
            form[item.conf_key] = item.conf_value ?? '';
        });
    });
}

async function submit() {
    saving.value = true;
    try {
        await saveSiteConfigs({ ...form });
        ElMessage.success('保存成功');
        loadConfigs();
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '保存失败');
    } finally {
        saving.value = false;
    }
}

onMounted(loadConfigs);
</script>
