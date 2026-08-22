<!--
/**
 * 角色编辑弹窗
 *
 * @package     Resources\Backend\Pages\System\Role
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改角色' : '新增角色'" width="720px" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
            <el-row :gutter="16">
                <el-col :span="12">
                    <el-form-item label="角色名称" prop="role_name">
                        <el-input v-model="form.role_name" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="权限字符" prop="role_code">
                        <el-input v-model="form.role_code" :disabled="Boolean(form.id && form.role_type === 1)" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="角色顺序" prop="role_sort">
                        <el-input-number v-model="form.role_sort" :min="0" style="width:100%" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="状态">
                        <el-radio-group v-model="form.role_status">
                            <el-radio :value="1">启用</el-radio>
                            <el-radio :value="0">停用</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="数据范围">
                        <el-select v-model="form.data_scope" style="width:100%">
                            <el-option v-for="item in scopeOptions" :key="item.value" :label="item.label" :value="item.value" />
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col v-if="form.data_scope === 5" :span="12">
                    <el-form-item label="指定部门">
                        <el-tree-select
                            v-model="form.scope_departments"
                            :data="deptTree"
                            multiple
                            show-checkbox
                            check-strictly
                            node-key="id"
                            :props="{ label: 'dept_name' }"
                            style="width:100%"
                        />
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="备注">
                        <el-input v-model="form.role_remark" type="textarea" :rows="2" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="菜单权限">
                        <el-tree
                            ref="menuTreeRef"
                            :data="menuTree"
                            node-key="id"
                            show-checkbox
                            default-expand-all
                            :props="{ label: 'menu_name', children: 'children' }"
                            style="max-height:280px;overflow:auto;border:1px solid #ebeef5;padding:8px;width:100%"
                        />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="操作权限">
                        <el-tree
                            ref="permTreeRef"
                            :data="permTree"
                            node-key="id"
                            show-checkbox
                            default-expand-all
                            :props="{ label: 'per_name', children: 'children' }"
                            style="max-height:280px;overflow:auto;border:1px solid #ebeef5;padding:8px;width:100%"
                        />
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
        <template #footer>
            <el-button @click="onClose">取消</el-button>
            <el-button type="primary" :loading="saving" @click="submit">确定</el-button>
        </template>
    </el-dialog>
</template>

<script setup>
import { nextTick, reactive, ref, watch } from 'vue';
import { ElMessage } from 'element-plus';
import { createRole, fetchRole, updateRole } from '../../../api/role';
import { fetchOptionDepartments, fetchOptionMenus, fetchPermissions } from '../../../api/options';

const props = defineProps({
    modelValue: Boolean,
    roleId: String,
});
const emit = defineEmits(['update:modelValue', 'saved']);

const formRef = ref();
const menuTreeRef = ref();
const permTreeRef = ref();
const saving = ref(false);
const menuTree = ref([]);
const permTree = ref([]);
const deptTree = ref([]);
const form = reactive(emptyForm());
const scopeOptions = [
    { label: '全部数据', value: 1 },
    { label: '本部门及下级', value: 2 },
    { label: '本部门', value: 3 },
    { label: '仅本人', value: 4 },
    { label: '自定义部门', value: 5 },
];
const rules = {
    role_name: [{ required: true, message: '请输入角色名称', trigger: 'blur' }],
    role_code: [{ required: true, message: '请输入权限字符', trigger: 'blur' }],
};

watch(
    () => props.modelValue,
    async (visible) => {
        if (!visible) {
            return;
        }
        await loadTrees();
        if (props.roleId) {
            const { data } = await fetchRole(props.roleId);
            Object.assign(form, {
                ...emptyForm(),
                ...data.role,
                password: undefined,
            });
            await nextTick();
            menuTreeRef.value?.setCheckedKeys(data.role.menu_ids || []);
            permTreeRef.value?.setCheckedKeys(data.role.permission_ids || []);
        } else {
            Object.assign(form, emptyForm());
            await nextTick();
            menuTreeRef.value?.setCheckedKeys([]);
            permTreeRef.value?.setCheckedKeys([]);
        }
    },
);

function emptyForm() {
    return {
        id: '',
        role_name: '',
        role_code: '',
        role_type: 2,
        role_sort: 0,
        data_scope: 1,
        scope_departments: [],
        role_status: 1,
        role_remark: '',
    };
}

async function loadTrees() {
    const [menus, perms, depts] = await Promise.all([
        fetchOptionMenus(),
        fetchPermissions(),
        fetchOptionDepartments(),
    ]);
    menuTree.value = menus.data.menus ?? [];
    permTree.value = perms.data.permissions ?? [];
    deptTree.value = depts.data.departments ?? [];
}

function checkedIds(treeRef) {
    return [...(treeRef?.getCheckedKeys() || []), ...(treeRef?.getHalfCheckedKeys() || [])];
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        const payload = {
            ...form,
            menu_ids: checkedIds(menuTreeRef.value),
            permission_ids: checkedIds(permTreeRef.value),
        };
        if (form.id) {
            await updateRole(form.id, payload);
            ElMessage.success('修改成功');
        } else {
            await createRole(payload);
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
