<!--
/**
 * 联系我们
 *
 * @package     Resources\Js\Frontend\Pages\Contact
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <div>
        <section class="page-banner">
            <div class="site-wrap">
                <h1>联系我们</h1>
                <p>{{ site.contact_address }}</p>
            </div>
        </section>
        <section class="section" style="padding-top: 0">
            <div class="site-wrap contact-form">
                <div>
                    <h2>在线留言</h2>
                    <form @submit.prevent="submit">
                        <label class="field">
                            姓名
                            <input v-model="form.fb_name" required maxlength="32" />
                        </label>
                        <label class="field">
                            电话
                            <input v-model="form.fb_phone" maxlength="16" />
                        </label>
                        <label class="field">
                            邮箱
                            <input v-model="form.fb_email" type="email" maxlength="32" />
                        </label>
                        <label class="field">
                            标题
                            <input v-model="form.fb_title" required maxlength="128" />
                        </label>
                        <label class="field">
                            留言内容
                            <textarea v-model="form.fb_content" rows="5" required maxlength="5000"></textarea>
                        </label>
                        <p v-if="message">{{ message }}</p>
                        <button class="btn" type="submit" :disabled="saving">提交留言</button>
                    </form>
                </div>
                <div>
                    <h2>联系方式</h2>
                    <p v-if="site.contact_phone">电话：{{ site.contact_phone }}</p>
                    <p v-if="site.contact_email">邮箱：{{ site.contact_email }}</p>
                    <p v-if="site.contact_address">地址：{{ site.contact_address }}</p>
                    <p>工作日我们将尽快回复您的咨询。</p>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { submitFeedback } from '../../api/site';
import { useSiteStore } from '../../stores/site';

const siteStore = useSiteStore();
const site = computed(() => siteStore.site);
const saving = ref(false);
const message = ref('');
const form = reactive({
    fb_name: '',
    fb_phone: '',
    fb_email: '',
    fb_title: '',
    fb_content: '',
});

async function submit() {
    saving.value = true;
    message.value = '';
    try {
        await submitFeedback(form);
        message.value = '提交成功，我们会尽快与您联系。';
        form.fb_name = '';
        form.fb_phone = '';
        form.fb_email = '';
        form.fb_title = '';
        form.fb_content = '';
    } catch (error) {
        message.value = error.response?.data?.message || '提交失败，请稍后重试。';
    } finally {
        saving.value = false;
    }
}
</script>
