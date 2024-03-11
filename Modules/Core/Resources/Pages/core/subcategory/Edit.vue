<template>
    <Head :title="$t('core__be_edit_subcategory')" />
    <ps-layout>

        <!-- breadcrumb start -->
        <ps-breadcrumb-2 :items="breadcrumb" class="mb-5 sm:mb-6 lg:mb-8" />
        <!-- breadcrumb end -->

        <ps-card class="w-full h-auto">
            <div class="rounded-xl">
                <!-- card header start -->
                <div class="bg-primary-50 dark:bg-primary-900 py-2.5 ps-4 rounded-t-xl">
                    <ps-label-header-6 textColor=" text-secondary-800 dark:text-secondary-100">{{$t('core__be_subcategory_info')}}</ps-label-header-6>
                </div>
                <!-- card header end -->

                <!-- card body start -->
                <div class="px-4 pt-6 dark:bg-backgroundDark">

                    <form @submit.prevent="handleSubmit(this.subcategory.id)">
                        <div class="grid w-full sm:w-1/2 gap-6">
                        <!-- for category dropdown -->
                            <div>
                                <ps-label class="text-base mb-2">{{$t('core_be_select_category')}}<span class="text-red-800 font-medium ms-1">*</span>
                                </ps-label>
                                <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full'>
                                    <template #select>
                                        <ps-dropdown-select :placeholder="$t('core_be_select_category')"
                                            :selectedValue="(form.category_id == '') ? '' : categories.filter(category => category.id == form.category_id)[0].name"
                                            @change="validateEmptyInput('category_id', form.category_id)"
                                            @blur="validateEmptyInput('category_id', form.category_id)" />
                                    </template>
                                    <template #list>
                                        <div class="rounded-md shadow-xs w-56 ">
                                            <div class="pt-2 z-30 ">
                                                <div v-if="categories.length == null">
                                                    <ps-label class='p-2 flex' @click="route('category.index')">
                                                        {{$t('core_be_create_new_category')}}</ps-label>
                                                </div>
                                                <div v-else>
                                                    <!-- <div class="w-56 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                        @click="[form.category_id = '', validateEmptyInput('category_id', form.category_id)]">
                                                        <ps-label class="text-secondary-200">{{$t('core_be_select_category')}}</ps-label>
                                                    </div> -->
                                                    <div v-for="category in categories" :key="category.id"
                                                        class="w-56 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                        @click="[form.category_id = category.id, validateEmptyInput('category_id', form.category_id)]">
                                                        <ps-label class="ms-2"
                                                            :class="category.id == form.category_id ? ' font-bold' : ''">
                                                            {{ category.name }} </ps-label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </ps-dropdown>
                                <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ errors.category_id }}</ps-label-caption>
                            </div>
                            <!-- end category -->

                            <div>
                                <ps-label class="text-base mb-2">{{$t('core__be_subcategory_label')}}<span class="text-red-800 font-medium ms-1">*</span></ps-label>
                                <ps-input type="text" v-model:value="form.name" :placeholder="$t('core__be_subcategory_placeholder')"
                                    @keyup="validateNameInput('name', form.name)"
                                    @blur="validateNameInput('name', form.name)" />
                                <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ errors.name }}</ps-label-caption>
                            </div>

                            <div>
                                <ps-label class="text-base mb-2">{{$t('core__status_label')}}</ps-label>

                                <ps-checkbox-value v-model:value="form.status" :title="$t('core__publish_label')" />
                            </div>

                            <div>
                                <ps-label class="text-base mb-2">{{ $t('core__be_subcategory_photo') }}<span class="text-red-800 font-medium ms-1">*</span></ps-label>
                                <ps-label-title-3 v-if="!subcategory.default_photo"> {{ $t('core__be_recommended_size_400_200') }} </ps-label-title-3>
                                <div v-if="subcategory.default_photo" class="flex items-end pt-4">
                                    <img
                                    v-lazy=" { src: $page.props.uploadUrl + '/' + subcategory.default_photo.img_path, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                     width="400" height="200"
                                        class="w-96 h-48" alt="category cover" />
                                    <ps-button type="button" @click="replaceImageClicked(subcategory.default_photo.id)"
                                        rounded="rounded-full" shadow="drop-shadow-2xl" class="-ms-10 mb-2"
                                        colors="bg-white text-primary-500 dark:bg-secondaryDark-black" border="border border-1 dark:border-secondary-700 border-secondary-300" padding="p-1.5" hover="" focus="">
                                        <ps-icon name="pencil-btn" w="21" h="21" />
                                    </ps-button>
                                    <ps-image-icon-modal ref="ps_image_icon_modal" />
                                    <ps-action-modal ref="ps_action_modal" />
                                    <ps-danger-dialog ref="ps_danger_dialog" />
                                </div>
                                <ps-image-upload v-else uploadType="image" v-model:imageFile="form.cover" />
                                <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ errors.cover }}</ps-label-caption>
                            </div>

                            <div>
                                <ps-label class="text-base mb-2">{{ $t('core__be_subcategory_icon') }}<span class="text-red-800 font-medium ms-1">*</span></ps-label>
                                <ps-label-title-3 v-if="!subcategory.default_icon"> {{ $t('core__be_recommended_size_200_200') }} </ps-label-title-3>
                                <div v-if="subcategory.default_icon" class="flex items-end pt-4">
                                    <img
                                    v-lazy=" { src: $page.props.uploadUrl + '/' + subcategory.default_icon.img_path, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                     width="200" height="200"
                                        class="w-48 h-48" alt="category icon" />
                                    <ps-button type="button" @click="replaceIconClicked(subcategory.default_icon.id)" rounded="rounded-full"
                                        shadow="drop-shadow-2xl" class="-ms-10 mb-2"
                                        colors="bg-white text-primary-500 dark:bg-secondaryDark-black" border="border border-1 dark:border-secondary-700 border-secondary-300" padding="p-1.5" hover="" focus="">
                                        <ps-icon name="pencil-btn"  w="21" h="21" />
                                    </ps-button>
                                    <ps-image-icon-modal ref="ps_image_icon_modal" />
                                    <ps-action-modal ref="ps_action_modal" />
                                    <ps-danger-dialog ref="ps_danger_dialog" />
                                </div>
                                <ps-image-upload v-else uploadType="icon" v-model:imageFile="form.icon" />
                                <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ errors.icon }}</ps-label-caption>
                            </div>

                            <div class="mb-2.5 flex flex-row justify-end">
                                <ps-button @click="handleCancel()" textSize="text-base" type="reset" class="me-4"
                                    colors="text-primary-500" focus="" hover="">{{ $t('core__be_btn_cancel') }}</ps-button>
                                <ps-button class="transition-all duration-300 min-w-3xs" padding="px-5 py-2"
                                    rounded="rounded" hover="" focus="">
                                    <ps-loading v-if="loading"
                                        theme="border-2 border-t-2 border-text-8 border-t-primary-500"
                                        loadingSize="h-5 w-5" />
                                    <ps-icon v-if="success" name="check" viewBox="0 0 18 14" w="14" h="10"
                                        class="me-1.5 transition-all duration-300" />
                                    <ps-label v-if="success" class="transition-all duration-300" textColor="text-white dark:text-secondaryDark-black">{{ $t('core__be_btn_saved') }}</ps-label>
                                    <ps-label v-if="!loading && !success" textColor="text-white dark:text-secondaryDark-black" > {{ $t('core__be_btn_save') }} </ps-label>
                                </ps-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </ps-card>
    </ps-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import PsLayout from "@/Components/PsLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import useValidators from '@/Validation/Validators'
import PsInput from "@/Components/Core/Input/PsInput.vue";
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsButton from "@/Components/Core/Buttons/PsButton.vue";
import PsLabelHeader6 from "@/Components/Core/Label/PsLabelHeader6.vue";
import PsDropdown from "@/Components/Core/Dropdown/PsDropdown.vue";
import PsDropdownSelect from "@/Components/Core/Dropdown/PsDropdownSelect.vue";
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsLoading from "@/Components/Core/Loading/PsLoading.vue";
import PsCheckboxValue from "@/Components/Core/Checkbox/PsCheckboxValue.vue";
import PsBreadcrumb2 from "@/Components/Core/Breadcrumbs/PsBreadcrumb2.vue";
import PsLabelCaption from "@/Components/Core/Label/PsLabelCaption.vue";
import PsImageUpload from "@/Components/Core/Upload/PsImageUpload.vue";
import PsLabelTitle3 from "@/Components/Core/Label/PsLabelTitle3.vue";
import PsActionModal from '@/Components/Core/Modals/PsActionModal.vue';
import PsImageIconModal from '@/Components/Core/Modals/PsImageIconModal.vue';
import PsDangerDialog from "@/Components/Core/Dialog/PsDangerDialog.vue";
import { trans } from 'laravel-vue-i18n';

export default defineComponent({
    name: "Edit",
    components: {
        Head,
        PsInput,
        PsLabel,
        PsButton,
        PsLabelHeader6,
        PsDropdown,
        PsDropdownSelect,
        PsIcon,
        PsLoading,
        PsCheckboxValue,
        PsBreadcrumb2,
        PsLabelCaption,
        PsImageUpload,
        PsLabelTitle3,
        PsActionModal,
        PsImageIconModal,
        PsDangerDialog,
    },
    layout: PsLayout,
    props: ['errors', 'categories', 'subcategory','validation'],
    setup(props) {
        const loading = ref(false);
        const success = ref(false);
        const name = ref();
        const category_id = ref();
        const ps_action_modal = ref();
        const ps_image_icon_modal = ref();
        const ps_danger_dialog = ref();

        // Client Side Validation
        const { isEmpty, minLength } = useValidators();

        const validateNameInput = (fieldName, fieldValue) => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : minLength(fieldName, fieldValue, 3);
            if(fieldName == 'name'){
                name.value.isError = (!Boolean(props.errors.name)) ? false: true;
            }
        }

        const validateEmptyInput = (fieldName, fieldValue) => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : '';
            if(fieldName == 'category_id'){
                category_id.value.isError = (!Boolean(props.errors.category_id)) ? false: true;
            }
        }

        let form = useForm({
            name: props.subcategory.name,
            // category_id: props.subcategory.category_id,
            category_id: props.categories.find(element=> element.id == props.subcategory.category_id) ? props.subcategory.category_id : '',
            status: props.subcategory.status,
            cover: "",
            icon: "",
            "_method": "put"
        })

        function handleSubmit(id) {
            this.$inertia.post(route("subcategory.update", id), form, {
                forceFormData: true,
                onBefore: () => {
                    loading.value = true;
                },
                onSuccess: () => {
                    loading.value = false;
                    success.value = true;
                    setTimeout(()=>{
                        this.$inertia.get(route('subcategory.index'));
                    },500)
                },
                onError: () => {
                    loading.value = false;
                    name.value.isError = (!Boolean(props.errors.name)) ? false: true;
                    category_id.value.isError = (!Boolean(props.errors.category_id)) ? false: true;
                },
            });
        }

        function replaceImageClicked(id) {
            ps_action_modal.value.openModal(trans('core__be_replace_subcat_photo'),
                trans('core__be_replace_img_label'),
                trans('core__be_remove_img_label'),
                'image',
                'trash',
                '24',
                '24',
                () => {
                    ps_image_icon_modal.value.openModal(
                        trans('core__be_upload_photo'),
                        'cloudUpload',
                        (imageFile) => {
                            let imageForm = useForm({
                                image: imageFile,
                                "_method": "put"
                            })

                            this.$inertia.post(route("image.replace", id), imageForm);
                        });
                },
                () => {
                    ps_danger_dialog.value.openModal(
                        trans('core__be_remove_label'),
                        trans('core__be_are_u_sure_remove_photo'),
                        trans('core__be_btn_confirm'),
                        trans('core__be_btn_cancel'),
                        () => {
                            this.$inertia.delete(route("image.destroy", id), {
                                onBefore: () => {
                                    loading.value = true;
                                },
                                onSuccess: () => {
                                    loading.value = false;
                                    success.value = true;
                                    setTimeout(() => {
                                        success.value = false;
                                    }, 2000);
                                },
                                onError: () => {
                                    loading.value = false;
                                },
                            });
                        },
                        () => { }
                    );
                },
                !props.validation.includes('cover')
            );
        }

        function replaceIconClicked(id) {
            ps_action_modal.value.openModal(trans('core__be_replace_subcat_icon'),
                trans('core__be_replace_icon_label'),
                trans('core__be_remove_icon_label'),
                'image',
                'trash',
                '24',
                '24',
                () => {
                    ps_image_icon_modal.value.openModal(
                        trans('core__be_upload_icon'),
                        'cloudUpload',
                        (imageFile) => {
                            let imageForm = useForm({
                                image: imageFile,
                                "_method": "put"
                            })

                            this.$inertia.post(route("image.replace", id), imageForm);
                        });
                },
                () => {
                    ps_danger_dialog.value.openModal(
                        trans('core__be_remove_label'),
                        trans('core__be_are_u_sure_remove_icon'),
                        trans('core__be_btn_confirm'),
                        trans('core__be_btn_cancel'),
                        () => {
                            this.$inertia.delete(route("image.destroy", id), {
                                onBefore: () => {
                                    loading.value = true;
                                },
                                onSuccess: () => {
                                    loading.value = false;
                                    success.value = true;
                                    setTimeout(() => {
                                        success.value = false;
                                    }, 2000);
                                },
                                onError: () => {
                                    loading.value = false;
                                },
                            });
                        },
                        () => { }
                    );
                },
                !props.validation.includes('icon')
            );
        }

        return { replaceIconClicked,replaceImageClicked,validateNameInput, validateEmptyInput, handleSubmit, form, loading, success, name, category_id ,ps_action_modal,ps_danger_dialog, ps_image_icon_modal}
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('core__be_dashboard_label'),
                    url: route('admin.index')
                },
                {
                    label: trans('subcategory_module'),
                    url: route('subcategory.index'),
                },
                {
                    label: trans('core__be_edit_subcategory'),
                    color: "text-primary-500"
                }
            ]
        }
    },
    methods: {
        handleCancel() {
            this.$inertia.get(route('subcategory.index'));
        },
    },
})
</script>
