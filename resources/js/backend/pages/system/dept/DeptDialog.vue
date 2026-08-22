<!--
/**
 * 部门编辑弹窗
 *
 * @package     Resources\Backend\Pages\System\Dept
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改部门' : '新增部门'" width="520px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="90px">
            <el-form-item label="上级部门" prop="parent_id">
                <el-tree-select
                    v-model="form.parent_id"
                    :data="parentOptions"
                    check-strictly
                    node-key="id"
                    :props="{ label: 'dept_name' }"
                    placeholder="顶级部门"
                    clearable
                    style="width:100%"
                />
            </el-form-item>
            <el-form-item label="部门名称" prop="dept_name">
                <el-input v-model="form.dept_name" />
            </el-form-item>
            <el-form-item label="部门编码" prop="dept_code">
                <el-input v-model="form.dept_code" />
            </el-form-item>
            <el-form-item label="显示排序">
                <el-input-number v-model="form.dept_sort" :min="0" style="width:100%" />
            </el-form-item>
            <el-form-item label="联系电话">
                <el-input v-model="form.dept_phone" />
            </el-form-item>
            <el-form-item label="状态">
                <el-radio-group v-model="form.dept_status">
                    <el-radio :value="1">正常</el-radio>
                    <el-radio :value="0">停用</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="onClose">取消</el-button>
            <el-button type="primary" :loading="saving" @click="submit">确定</el-button>
        </template>
    </el-dialog>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { ElMessage } from 'element-plus';
import { createDepartment, updateDepartment } from '../../../api/dept';

const props = defineProps({
    modelValue: Boolean,
    department: Object,
    parent: Object,
    tree: {
        type: Array,
        default: () => [],
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const form = reactive(emptyForm());
const rules = {
    dept_name: [{ required: true, message: '请输入部门名称', trigger: 'blur' }],
    dept_code: [{ required: true, message: '请输入部门编码', trigger: 'blur' }],
};
const parentOptions = computed(() => [
    { id: '0', dept_name: '顶级部门', children: props.tree },
]);

watch(
    () => [props.modelValue, props.department, props.parent],
    () => {
        if (props.department) {
            Object.assign(form, {
                id: props.department.id,
                parent_id: String(props.department.parent_id || '0'),
                dept_name: props.department.dept_name,
                dept_code: props.department.dept_code,
                dept_sort: props.department.dept_sort,
                dept_phone: props.department.dept_phone,
                dept_status: props.department.dept_status,
            });
        } else {
            Object.assign(form, emptyForm(), {
                parent_id: props.parent?.id || '0',
            });
        }
    },
);

function emptyForm() {
    return {
        id: '',
        parent_id: '0',
        dept_name: '',
        dept_code: '',
        dept_sort: 0,
        dept_phone: '',
        dept_status: 1,
    };
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        if (form.id) {
            await updateDepartment(form.id, form);
            ElMessage.success('修改成功');
        } else {
            await createDepartment(form);
            ElMessage.success('新增成功');
        }
        emit('saved');
        onClose();
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        const first = Object.values(bag)[0]?.[0];
        ElMessage.error(first || error.response?.data?.message || '保存失败');
    } finally {
        saving.value = false;
    }
}
</script>
