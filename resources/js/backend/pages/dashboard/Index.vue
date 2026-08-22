<!--
/**
 * 仪表盘页面
 *
 * @package     Resources\Backend\Pages\Dashboard
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div v-loading="loading" class="dashboard">
        <section class="dash-welcome">
            <div>
                <h2>{{ greeting }}，{{ displayName }}</h2>
                <p>{{ todayText }} · 上次登录 {{ userStore.user?.last_login_at || '暂无记录' }}</p>
            </div>
            <div class="dash-todos">
                <button
                    v-for="item in (stats.todos || [])"
                    :key="item.label"
                    type="button"
                    class="dash-todo"
                    @click="go(item.path)"
                >
                    <strong>{{ item.value }}</strong>
                    <span>{{ item.label }}</span>
                </button>
            </div>
        </section>

        <el-row :gutter="16">
            <el-col v-for="card in (stats.cards || [])" :key="card.key" :xs="24" :sm="12" :lg="6">
                <article class="dash-card" :class="'is-' + card.key" @click="go(card.path)">
                    <div class="dash-card-icon">
                        <el-icon :size="26"><component :is="cardIcons[card.key]" /></el-icon>
                    </div>
                    <div class="dash-card-body">
                        <span>{{ card.label }}</span>
                        <strong>{{ card.value }}</strong>
                        <p>
                            今日 {{ card.today }}
                            <em :class="deltaClass(card)">{{ deltaText(card) }}</em>
                            · {{ card.extra_label }} {{ card.extra }}
                        </p>
                    </div>
                </article>
            </el-col>
        </el-row>

        <el-row :gutter="16" class="dash-block">
            <el-col :xs="24" :lg="16">
                <el-card shadow="never" class="dash-panel">
                    <template #header>
                        <div class="dash-panel-head">
                            <span>近 7 日业务趋势</span>
                            <div class="dash-legend">
                                <span v-for="(item, index) in trendSeries" :key="item.name" class="dash-legend-item">
                                    <i :style="{ background: trendColors[index] }" />{{ item.name }}
                                </span>
                            </div>
                        </div>
                    </template>
                    <div class="dash-chart">
                        <svg viewBox="0 0 640 220" preserveAspectRatio="none">
                            <line v-for="y in [20, 80, 140, 200]" :key="y" x1="36" :y1="y" x2="624" :y2="y" class="dash-grid" />
                            <polyline
                                v-for="(item, index) in trendPolylines"
                                :key="item.name"
                                fill="none"
                                :stroke="trendColors[index]"
                                stroke-width="2.4"
                                stroke-linejoin="round"
                                stroke-linecap="round"
                                :points="item.points"
                            />
                            <circle
                                v-for="dot in trendDots"
                                :key="dot.key"
                                :cx="dot.x"
                                :cy="dot.y"
                                r="3.2"
                                :fill="dot.color"
                            />
                        </svg>
                        <div class="dash-chart-labels">
                            <span v-for="label in trendLabels" :key="label">{{ label }}</span>
                        </div>
                    </div>
                </el-card>
            </el-col>
            <el-col :xs="24" :lg="8">
                <el-card shadow="never" class="dash-panel">
                    <template #header>文章状态分布</template>
                    <div v-if="articleTotal === 0" class="dash-empty">暂无文章数据</div>
                    <ul v-else class="dash-bars">
                        <li v-for="item in (stats.article_status || [])" :key="item.value">
                            <div class="dash-bar-meta">
                                <span>{{ item.label }}</span>
                                <b>{{ item.count }}</b>
                            </div>
                            <div class="dash-bar-track">
                                <i :style="{ width: barWidth(item.count, articleTotal) }" />
                            </div>
                        </li>
                    </ul>
                    <div class="dash-job-mini">
                        <h4>职位状态</h4>
                        <div class="dash-pills">
                            <span v-for="item in (stats.job_status || [])" :key="item.value">
                                {{ item.label }} {{ item.count }}
                            </span>
                        </div>
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <el-row :gutter="16" class="dash-block">
            <el-col :xs="24" :lg="12">
                <el-card shadow="never" class="dash-panel">
                    <template #header>
                        <div class="dash-panel-head">
                            <span>最新留言</span>
                            <el-button link type="primary" @click="go('/site/feedback')">查看全部</el-button>
                        </div>
                    </template>
                    <el-table :data="stats.recent_feedbacks || []" size="small" empty-text="暂无留言">
                        <el-table-column label="标题" prop="fb_title" min-width="140" show-overflow-tooltip />
                        <el-table-column label="联系人" prop="fb_name" width="90" />
                        <el-table-column label="状态" width="90" align="center">
                            <template #default="{ row }">
                                <el-tag :type="row.fb_status === 1 ? 'success' : 'warning'" size="small">{{ row.fb_status_label }}</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="时间" prop="created_at" min-width="150" />
                    </el-table>
                </el-card>
            </el-col>
            <el-col :xs="24" :lg="12">
                <el-card shadow="never" class="dash-panel">
                    <template #header>
                        <div class="dash-panel-head">
                            <span>最近操作</span>
                            <el-button link type="primary" @click="go('/system/log/operlog')">查看全部</el-button>
                        </div>
                    </template>
                    <el-table :data="stats.recent_logs || []" size="small" empty-text="暂无操作记录">
                        <el-table-column label="操作人" prop="operator_name" width="90" />
                        <el-table-column label="模块" prop="biz_type" width="90" />
                        <el-table-column label="操作" prop="action" width="80" />
                        <el-table-column label="摘要" prop="biz_label" min-width="120" show-overflow-tooltip />
                        <el-table-column label="结果" width="70" align="center">
                            <template #default="{ row }">
                                <el-tag :type="row.operator_status === 1 ? 'success' : 'danger'" size="small">
                                    {{ row.operator_status === 1 ? '成功' : '失败' }}
                                </el-tag>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-card>
            </el-col>
        </el-row>

        <el-row :gutter="16" class="dash-block">
            <el-col :span="24">
                <el-card shadow="never" class="dash-panel">
                    <template #header>
                        <div class="dash-panel-head">
                            <span>热门招聘</span>
                            <el-button link type="primary" @click="go('/site/job')">职位管理</el-button>
                        </div>
                    </template>
                    <el-table :data="stats.hot_jobs || []" size="small" empty-text="暂无发布中的职位">
                        <el-table-column label="职位" prop="job_title" min-width="180" />
                        <el-table-column label="部门" prop="department" width="120" />
                        <el-table-column label="地点" prop="workplace" width="140" />
                        <el-table-column label="急聘" width="80" align="center">
                            <template #default="{ row }">
                                <el-tag v-if="row.is_hot === 1" type="danger" size="small">急聘</el-tag>
                                <span v-else>-</span>
                            </template>
                        </el-table-column>
                        <el-table-column label="浏览" prop="view_count" width="90" align="center" />
                    </el-table>
                </el-card>
            </el-col>
        </el-row>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ChatLineSquare, Connection, Document, Histogram, Operation, Picture, Suitcase, User } from '@element-plus/icons-vue';
import { fetchDashboard } from '../../api/dashboard';
import { useUserStore } from '../../stores/user';

const router = useRouter();
const userStore = useUserStore();
const loading = ref(false);
const stats = ref({
    cards: [],
    todos: [],
    trends: { labels: [], series: [] },
    article_status: [],
    job_status: [],
    recent_feedbacks: [],
    recent_logs: [],
    hot_jobs: [],
});

const cardIcons = {
    users: User,
    articles: Document,
    jobs: Suitcase,
    feedbacks: ChatLineSquare,
    logs: Operation,
    ads: Picture,
    links: Connection,
    views: Histogram,
};

const trendColors = ['#409eff', '#67c23a', '#e6a23c', '#f56c6c'];
const weekLabels = ['日', '一', '二', '三', '四', '五', '六'];

const displayName = computed(() => userStore.user?.real_name || userStore.user?.user_name || '管理员');
const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) {
        return '早上好';
    }
    if (hour < 18) {
        return '下午好';
    }
    return '晚上好';
});
const todayText = computed(() => {
    const now = new Date();
    return `${now.getFullYear()}年${now.getMonth() + 1}月${now.getDate()}日 星期${weekLabels[now.getDay()]}`;
});
const articleTotal = computed(() => (stats.value.article_status || []).reduce((sum, item) => sum + item.count, 0));
const trendLabels = computed(() => stats.value.trends?.labels ?? []);
const trendSeries = computed(() => stats.value.trends?.series ?? []);
const trendPolylines = computed(() => buildTrendGeometry().lines);
const trendDots = computed(() => buildTrendGeometry().dots);

function deltaText(card) {
    const diff = (card.today || 0) - (card.yesterday || 0);
    if (card.key === 'views') {
        return '';
    }
    if (diff === 0) {
        return '持平';
    }
    return `${diff > 0 ? '+' : ''}${diff}`;
}

function deltaClass(card) {
    const diff = (card.today || 0) - (card.yesterday || 0);
    if (diff > 0) {
        return 'is-up';
    }
    if (diff < 0) {
        return 'is-down';
    }
    return '';
}

function barWidth(count, total) {
    if (!total) {
        return '0%';
    }
    return `${Math.max(6, Math.round((count / total) * 100))}%`;
}

function buildTrendGeometry() {
    const series = trendSeries.value;
    const count = trendLabels.value.length || 7;
    const values = series.flatMap((item) => item.data || []);
    const max = Math.max(1, ...values);
    const left = 40;
    const right = 620;
    const top = 20;
    const bottom = 200;
    const step = count > 1 ? (right - left) / (count - 1) : 0;
    const lines = series.map((item, index) => ({
        name: item.name,
        points: (item.data || []).map((value, i) => {
            const x = left + step * i;
            const y = bottom - ((value || 0) / max) * (bottom - top);
            return `${x},${y}`;
        }).join(' '),
        color: trendColors[index],
    }));
    const dots = series.flatMap((item, index) => (item.data || []).map((value, i) => ({
        key: `${item.name}-${i}`,
        x: left + step * i,
        y: bottom - ((value || 0) / max) * (bottom - top),
        color: trendColors[index],
    })));

    return { lines, dots };
}

function go(path) {
    if (path) {
        router.push(path);
    }
}

async function loadDashboard() {
    loading.value = true;
    try {
        const { data } = await fetchDashboard();
        stats.value = data;
    } finally {
        loading.value = false;
    }
}

onMounted(loadDashboard);
</script>
