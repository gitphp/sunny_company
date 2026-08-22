<template>
    <div class="ai-page">
        <header class="ai-toolbar">
            <div class="ai-toolbar-left">
                <el-select v-model="providerId" placeholder="选择模型" style="width:240px">
                    <el-option
                        v-for="item in providers"
                        :key="item.id"
                        :label="item.is_default ? `${item.provider_name} · 默认` : item.provider_name"
                        :value="item.id"
                    >
                        <span>{{ item.provider_name }}</span>
                        <span class="ai-option-model">{{ item.model }}</span>
                    </el-option>
                </el-select>
                <el-tag v-if="currentProvider" type="info" effect="plain">{{ currentProvider.model }}</el-tag>
                <el-tag v-if="currentProvider && !currentProvider.has_key" type="warning" effect="plain">未配置密钥</el-tag>
            </div>
            <div>
                <el-button @click="resetChat">新对话</el-button>
                <el-button v-if="can('ai:config')" type="primary" plain @click="openSettings">模型配置</el-button>
            </div>
        </header>

        <section ref="listRef" class="ai-list">
            <div v-if="messages.length === 0" class="ai-empty">
                <el-icon :size="42"><ChatDotRound /></el-icon>
                <h3>名杨科技 AI 助手</h3>
                <p>已接入 DeepSeek 等 OpenAI 兼容模型。先在「模型配置」中填写 API Key，再开始对话。</p>
                <div class="ai-hints">
                    <el-button v-for="hint in hints" :key="hint" size="small" @click="useHint(hint)">{{ hint }}</el-button>
                </div>
            </div>
            <article v-for="(item, index) in messages" :key="index" class="ai-msg" :class="'is-' + item.role">
                <div class="ai-avatar">{{ item.role === 'user' ? '我' : 'AI' }}</div>
                <div class="ai-bubble">{{ item.content }}</div>
            </article>
        </section>

        <footer class="ai-composer">
            <el-input
                v-model="draft"
                type="textarea"
                :rows="3"
                resize="none"
                placeholder="输入问题，Enter 发送，Shift+Enter 换行"
                :disabled="sending"
                @keydown="onKeydown"
            />
            <div class="ai-composer-actions">
                <span>当前模型：{{ currentProvider?.provider_name || '未选择' }}</span>
                <div>
                    <el-button v-if="sending" @click="stopChat">停止</el-button>
                    <el-button type="primary" :loading="sending" :disabled="!draft.trim()" @click="sendChat">发送</el-button>
                </div>
            </div>
        </footer>

        <el-drawer v-model="settings.visible" title="模型配置" size="720px">
            <div class="toolbar">
                <el-button type="primary" plain @click="openDialog()">新增模型</el-button>
            </div>
            <el-table v-loading="settings.loading" :data="settings.rows" border>
                <el-table-column label="名称" prop="provider_name" min-width="120" />
                <el-table-column label="模型" prop="model" min-width="140" />
                <el-table-column label="接口" prop="base_url" min-width="180" show-overflow-tooltip />
                <el-table-column label="密钥" width="90" align="center">
                    <template #default="{ row }">
                        <el-tag :type="row.has_key ? 'success' : 'info'" size="small">{{ row.has_key ? '已配置' : '未填' }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="默认" width="80" align="center">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_default === 1" type="success" size="small">默认</el-tag>
                        <el-button v-else link type="primary" @click="makeDefault(row)">设为默认</el-button>
                    </template>
                </el-table-column>
                <el-table-column label="状态" width="80" align="center">
                    <template #default="{ row }">
                        <el-switch :model-value="row.status" :active-value="1" :inactive-value="0" @change="(value) => toggleStatus(row, value)" />
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button link type="primary" @click="testProvider(row)">测试</el-button>
                        <el-button link type="primary" @click="openDialog(row)">修改</el-button>
                        <el-button link type="danger" @click="removeProvider(row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-drawer>

        <provider-dialog v-model="dialog.visible" :provider="dialog.provider" :presets="settings.presets" @saved="onProviderSaved" />
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import {
    changeAiProviderStatus,
    deleteAiProvider,
    fetchAiProviderOptions,
    fetchAiProviders,
    setDefaultAiProvider,
    streamChat,
    testAiProvider,
} from '../../api/ai';
import { useUserStore } from '../../stores/user';
import ProviderDialog from './ProviderDialog.vue';

const userStore = useUserStore();
const can = (code) => userStore.hasPermission(code);
const providers = ref([]);
const providerId = ref('');
const draft = ref('');
const sending = ref(false);
const messages = ref([]);
const listRef = ref();
const abortRef = ref(null);
const hints = ['帮我写一则公司公告', '总结一下招聘职位该怎么写', '把这段话改得更正式'];
const settings = reactive({
    visible: false,
    loading: false,
    rows: [],
    presets: [],
});
const dialog = reactive({
    visible: false,
    provider: null,
});

const currentProvider = computed(() => providers.value.find((item) => item.id === providerId.value));

async function loadOptions() {
    const { data } = await fetchAiProviderOptions();
    providers.value = data.providers ?? [];
    if (!providers.value.some((item) => item.id === providerId.value)) {
        providerId.value = providers.value.find((item) => item.is_default === 1)?.id || providers.value[0]?.id || '';
    }
}

async function loadSettings() {
    settings.loading = true;
    try {
        const { data } = await fetchAiProviders({ per_page: 50 });
        settings.rows = data.data ?? [];
        settings.presets = data.presets ?? [];
    } finally {
        settings.loading = false;
    }
}

function openSettings() {
    settings.visible = true;
    loadSettings();
}

function openDialog(row) {
    dialog.provider = row ? { ...row, api_key: '' } : null;
    dialog.visible = true;
}

async function onProviderSaved() {
    await loadSettings();
    await loadOptions();
}

async function makeDefault(row) {
    await setDefaultAiProvider(row.id);
    ElMessage.success('已设为默认模型');
    await onProviderSaved();
}

async function toggleStatus(row, value) {
    try {
        await changeAiProviderStatus(row.id, value);
        ElMessage.success('状态已更新');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '更新失败');
    } finally {
        await onProviderSaved();
    }
}

async function testProvider(row) {
    const loading = ElMessage.info({ message: '正在测试连接…', duration: 0 });
    try {
        const { data } = await testAiProvider(row.id);
        ElMessage.success(data.message + (data.content ? `：${data.content}` : ''));
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        ElMessage.error(Object.values(bag)[0]?.[0] || error.response?.data?.message || '测试失败');
    } finally {
        loading.close();
    }
}

async function removeProvider(row) {
    await ElMessageBox.confirm(`是否删除模型「${row.provider_name}」？`, '警告', { type: 'warning' });
    await deleteAiProvider(row.id);
    ElMessage.success('删除成功');
    await onProviderSaved();
}

function resetChat() {
    stopChat();
    messages.value = [];
    draft.value = '';
}

function useHint(text) {
    draft.value = text;
}

function onKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendChat();
    }
}

async function sendChat() {
    const text = draft.value.trim();
    if (!text || sending.value) {
        return;
    }
    if (!providerId.value) {
        ElMessage.warning('请先配置并选择模型');
        return;
    }

    messages.value.push({ role: 'user', content: text });
    draft.value = '';
    const assistant = { role: 'assistant', content: '' };
    messages.value.push(assistant);
    sending.value = true;
    abortRef.value = new AbortController();
    await scrollToBottom();

    try {
        await streamChat(
            {
                provider_id: providerId.value,
                messages: messages.value.filter((item) => item.content).slice(0, -1),
            },
            (delta) => {
                assistant.content += delta;
                scrollToBottom();
            },
            abortRef.value.signal,
        );
        if (!assistant.content) {
            assistant.content = '模型没有返回内容。';
        }
    } catch (error) {
        if (error.name === 'AbortError') {
            assistant.content = assistant.content || '已停止生成';
        } else {
            assistant.content = error.message || '对话失败';
        }
    } finally {
        sending.value = false;
        abortRef.value = null;
        await scrollToBottom();
    }
}

function stopChat() {
    abortRef.value?.abort();
}

async function scrollToBottom() {
    await nextTick();
    if (listRef.value) {
        listRef.value.scrollTop = listRef.value.scrollHeight;
    }
}

onMounted(loadOptions);
</script>
