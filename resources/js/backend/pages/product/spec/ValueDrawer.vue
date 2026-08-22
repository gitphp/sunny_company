<!--
/**
 * 规格值抽屉
 *
 * @package     Resources\Backend\Pages\Product\Spec
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-drawer :model-value="modelValue" :title="spec ? `规格值 · ${spec.spec_name}` : '规格值'" size="480px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <div class="toolbar">
            <el-button type="primary" plain @click="addValue">新增规格值</el-button>
        </div>
        <el-table :data="rows" border>
            <el-table-column label="编码" prop="value_code" width="110" />
            <el-table-column label="规格值" min-width="140">
                <template #default="{ row }">
                    <el-input v-if="row.editing" v-model="row.value" />
                    <span v-else>{{ row.value }}</span>
                </template>
            </el-table-column>
            <el-table-column label="排序" width="90">
                <template #default="{ row }">
                    <el-input-number v-if="row.editing" v-model="row.sort_order" :min="0" controls-position="right" style="width:80px" />
                    <span v-else>{{ row.sort_order }}</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="140">
                <template #default="{ row }">
                    <template v-if="row.editing">
                        <el-button link type="primary" @click="saveValue(row)">保存</el-button>
                        <el-button link @click="cancelEdit(row)">取消</el-button>
                    </template>
                    <template v-else>
                        <el-button link type="primary" @click="row.editing = true">修改</el-button>
                        <el-button link type="danger" @click="removeValue(row)">删除</el-button>
                    </template>
                </template>
            </el-table-column>
        </el-table>
    </el-drawer>
</template>

<script setup>
import { ref, watch } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { createProductSpecValue, deleteProductSpecValue, updateProductSpecValue } from '../../../api/product';

const props = defineProps({
    modelValue: Boolean,
    spec: Object,
});
const emit = defineEmits(['update:modelValue', 'saved']);
const rows = ref([]);

watch(
    () => [props.modelValue, props.spec],
    () => {
        rows.value = (props.spec?.values || []).map((item) => ({ ...item, editing: false }));
    },
);

function onClose() {
    emit('update:modelValue', false);
    emit('saved');
}

function addValue() {
    rows.value.unshift({
        id: '',
        spec_id: props.spec?.id,
        value: '',
        sort_order: 0,
        value_status: 1,
        editing: true,
        isNew: true,
    });
}

function cancelEdit(row) {
    if (row.isNew) {
        rows.value = rows.value.filter((item) => item !== row);
        return;
    }
    const origin = (props.spec?.values || []).find((item) => item.id === row.id);
    if (origin) {
        Object.assign(row, origin, { editing: false });
    }
}

async function saveValue(row) {
    if (!row.value?.trim()) {
        ElMessage.warning('请填写规格值');
        return;
    }
    try {
        if (row.id) {
            await updateProductSpecValue(props.spec.id, row.id, row);
        } else {
            const { data } = await createProductSpecValue(props.spec.id, row);
            Object.assign(row, data.value ?? {}, { isNew: false });
        }
        ElMessage.success('保存成功');
        row.editing = false;
        emit('saved');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '保存失败');
    }
}

async function removeValue(row) {
    if (!row.id) {
        rows.value = rows.value.filter((item) => item !== row);
        return;
    }
    await ElMessageBox.confirm(`是否删除规格值「${row.value}」？`, '警告', { type: 'warning' });
    try {
        await deleteProductSpecValue(props.spec.id, row.id);
        ElMessage.success('删除成功');
        emit('saved');
    } catch (error) {
        ElMessage.error(error.response?.data?.message || '删除失败');
    }
}
</script>
