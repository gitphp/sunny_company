<!--
/**
 * 商品编辑弹窗
 *
 * @package     Resources\Backend\Pages\Product
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
-->
<template>
    <el-dialog :model-value="modelValue" :title="form.id ? '修改商品' : '新增商品'" width="920px" top="4vh" @close="onClose" @update:model-value="(visible) => !visible && onClose()">
        <el-form ref="formRef" v-loading="loading" :model="form" :rules="rules" label-width="90px">
            <el-tabs v-model="activeTab">
                <el-tab-pane label="基本信息" name="basic">
                    <el-form-item label="商品名称" prop="product_name">
                        <el-input v-model="form.product_name" />
                    </el-form-item>
                    <el-row :gutter="12">
                        <el-col :span="12">
                            <el-form-item label="商品分类">
                                <el-tree-select
                                    v-model="form.category_id"
                                    :data="categories"
                                    check-strictly
                                    node-key="id"
                                    :props="{ label: 'category_name' }"
                                    clearable
                                    style="width:100%"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="品牌">
                                <el-select v-model="form.brand_id" clearable style="width:100%">
                                    <el-option v-for="item in brands" :key="item.id" :label="item.brand_name" :value="item.id" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="12">
                        <el-col :span="12">
                            <el-form-item label="型号">
                                <el-input v-model="form.product_model" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="排序">
                                <el-input-number v-model="form.sort_order" :min="0" style="width:100%" />
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="12">
                        <el-col :span="12">
                            <el-form-item label="材质">
                                <el-input v-model="form.material_quality" />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="填充">
                                <el-input v-model="form.filling" />
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-form-item label="主图">
                        <el-input v-model="form.main_image_url" placeholder="图片 URL" />
                    </el-form-item>
                    <el-form-item label="状态">
                        <el-radio-group v-model="form.product_status">
                            <el-radio :value="1">上架</el-radio>
                            <el-radio :value="0">下架</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="简介">
                        <el-input v-model="form.short_desc" type="textarea" :rows="3" />
                    </el-form-item>
                </el-tab-pane>
                <el-tab-pane label="SKU" name="sku">
                    <el-form-item label="规格">
                        <el-select v-model="selectedSpecIds" multiple placeholder="选择规格后生成 SKU" style="width:100%">
                            <el-option v-for="item in specs" :key="item.id" :label="item.spec_name" :value="item.id" />
                        </el-select>
                    </el-form-item>
                    <div v-for="spec in selectedSpecs" :key="spec.id" style="margin-bottom:10px">
                        <span style="margin-right:8px;color:#606266">{{ spec.spec_name }}</span>
                        <el-checkbox-group v-model="pickedValues[spec.id]">
                            <el-checkbox v-for="item in spec.values" :key="item.id" :value="item.id">{{ item.value }}</el-checkbox>
                        </el-checkbox-group>
                    </div>
                    <el-button type="primary" plain style="margin-bottom:12px" @click="generateSkus">生成 SKU</el-button>
                    <el-button plain style="margin-bottom:12px" @click="addEmptySku">添加一条</el-button>
                    <el-table :data="form.skus" border size="small">
                        <el-table-column label="规格" min-width="160">
                            <template #default="{ row }">{{ row.spec_text || '默认规格' }}</template>
                        </el-table-column>
                        <el-table-column label="SKU编码" width="120">
                            <template #default="{ row }">
                                <el-input v-model="row.sku_code" placeholder="自动" />
                            </template>
                        </el-table-column>
                        <el-table-column label="售价" width="110">
                            <template #default="{ row }">
                                <el-input-number v-model="row.price" :min="0" :precision="2" controls-position="right" style="width:90px" />
                            </template>
                        </el-table-column>
                        <el-table-column label="库存" width="100">
                            <template #default="{ row }">
                                <el-input-number v-model="row.stock_num" :min="0" controls-position="right" style="width:80px" />
                            </template>
                        </el-table-column>
                        <el-table-column label="上架" width="70" align="center">
                            <template #default="{ row }">
                                <el-switch v-model="row.sale_status" :active-value="1" :inactive-value="0" />
                            </template>
                        </el-table-column>
                        <el-table-column label="" width="60">
                            <template #default="{ $index }">
                                <el-button link type="danger" @click="form.skus.splice($index, 1)">删</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
            </el-tabs>
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
import { createProduct, fetchProduct, updateProduct } from '../../api/product';
import { fetchOptionProductBrands, fetchOptionProductCategories, fetchOptionProductSpecs } from '../../api/options';

const props = defineProps({
    modelValue: Boolean,
    productId: {
        type: String,
        default: '',
    },
    categoryId: {
        type: String,
        default: '',
    },
});
const emit = defineEmits(['update:modelValue', 'saved']);
const formRef = ref();
const saving = ref(false);
const loading = ref(false);
const activeTab = ref('basic');
const brands = ref([]);
const categories = ref([]);
const specs = ref([]);
const selectedSpecIds = ref([]);
const pickedValues = reactive({});
const form = reactive(emptyForm());
const rules = {
    product_name: [{ required: true, message: '请输入商品名称', trigger: 'blur' }],
};

const selectedSpecs = computed(() => specs.value.filter((item) => selectedSpecIds.value.includes(item.id)));

watch(
    () => [props.modelValue, props.productId],
    async () => {
        if (!props.modelValue) {
            return;
        }
        activeTab.value = 'basic';
        await loadOptions();
        if (props.productId) {
            loading.value = true;
            try {
                const { data } = await fetchProduct(props.productId);
                Object.assign(form, emptyForm(), data.product, {
                    category_id: data.product.category_id === '0' ? '' : data.product.category_id,
                    brand_id: data.product.brand_id === '0' ? '' : data.product.brand_id,
                    skus: (data.product.skus || []).map((item) => ({
                        ...item,
                        price: Number(item.price),
                        stock_num: Number(item.stock_num),
                    })),
                });
                restoreSpecPicker(form.skus);
            } finally {
                loading.value = false;
            }
        } else {
            Object.assign(form, emptyForm(), {
                category_id: props.categoryId && props.categoryId !== '0' ? props.categoryId : '',
            });
            selectedSpecIds.value = [];
            Object.keys(pickedValues).forEach((key) => delete pickedValues[key]);
        }
    },
);

function emptyForm() {
    return {
        id: '',
        product_name: '',
        product_model: '',
        category_id: '',
        brand_id: '',
        material_quality: '',
        filling: '',
        short_desc: '',
        main_image_url: '',
        product_status: 1,
        sort_order: 0,
        skus: [],
    };
}

async function loadOptions() {
    const [brandRes, catRes, specRes] = await Promise.all([
        fetchOptionProductBrands(),
        fetchOptionProductCategories(),
        fetchOptionProductSpecs(),
    ]);
    brands.value = brandRes.data.brands ?? [];
    categories.value = catRes.data.categories ?? [];
    specs.value = specRes.data.specs ?? [];
}

function restoreSpecPicker(skus) {
    const specIds = new Set();
    Object.keys(pickedValues).forEach((key) => delete pickedValues[key]);
    skus.forEach((sku) => {
        (sku.spec_value_ids || []).forEach((valueId) => {
            const spec = specs.value.find((item) => item.values.some((value) => value.id === valueId));
            if (!spec) {
                return;
            }
            specIds.add(spec.id);
            pickedValues[spec.id] = Array.from(new Set([...(pickedValues[spec.id] || []), valueId]));
        });
    });
    selectedSpecIds.value = Array.from(specIds);
}

function cartesian(lists) {
    return lists.reduce((acc, curr) => acc.flatMap((row) => curr.map((item) => [...row, item])), [[]]);
}

function generateSkus() {
    const groups = selectedSpecs.value
        .map((spec) => (pickedValues[spec.id] || []).map((id) => spec.values.find((item) => item.id === id)).filter(Boolean))
        .filter((group) => group.length > 0);

    if (groups.length === 0) {
        ElMessage.warning('请先勾选规格值');
        return;
    }

    form.skus = cartesian(groups).map((combo, index) => ({
        id: '',
        sku_code: '',
        price: 0,
        market_price: 0,
        cost_price: 0,
        stock_num: 0,
        weight: 0,
        volume: 0,
        sale_status: 1,
        sort_order: 100 - index,
        spec_value_ids: combo.map((item) => item.id),
        spec_text: combo.map((item) => {
            const spec = specs.value.find((row) => row.values.some((value) => value.id === item.id));
            return spec ? `${spec.spec_name}:${item.value}` : item.value;
        }).join(' / '),
    }));
}

function addEmptySku() {
    form.skus.push({
        id: '',
        sku_code: '',
        price: 0,
        market_price: 0,
        cost_price: 0,
        stock_num: 0,
        weight: 0,
        volume: 0,
        sale_status: 1,
        sort_order: 0,
        spec_value_ids: [],
        spec_text: '默认规格',
    });
}

function onClose() {
    emit('update:modelValue', false);
}

async function submit() {
    await formRef.value.validate();
    saving.value = true;
    try {
        const payload = { ...form };
        if (form.id) {
            await updateProduct(form.id, payload);
            ElMessage.success('修改成功');
        } else {
            await createProduct(payload);
            ElMessage.success('新增成功');
        }
        emit('saved');
        onClose();
    } catch (error) {
        const bag = error.response?.data?.errors ?? {};
        ElMessage.error(Object.values(bag)[0]?.[0] || error.response?.data?.message || '保存失败');
    } finally {
        saving.value = false;
    }
}
</script>
