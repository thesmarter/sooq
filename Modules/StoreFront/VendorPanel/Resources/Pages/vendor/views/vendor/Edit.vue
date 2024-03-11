<template>
    <Head :title="$t('core__vendor_my_store_module')" />
    <ps-layout>
        <!-- breadcrumb start -->
        <ps-breadcrumb-2 :items="breadcrumb" class="mb-5 sm:mb-6 lg:mb-8" />

        <!-- noti start -->
        <div v-if="showNoti" class="flex justify-between gap-6 items-start bg-red-50 p-6 rounded border-l-4 border-red-500 mb-5 sm:mb-6 lg:mb-8">
            <ps-label textColor="text-secondary-500">{{$t("core_vendor__item_quantity_noti")}}</ps-label>
            <ps-label textColor="leading-5 text-sm text-secondary-400 flex items-center gap-2 cursor-pointer" @click="showNoti = false">
                <ps-icon name="close-fill" w="16" h="16" viewBox="0 0 16 16"/>
                {{ $t("core_be__dismiss") }}
            </ps-label>
        </div>
        <!-- noti end -->

        <vendor-detail-subscription-notification :expiredStatus="vendor.expire_status" :expiredDate="vendor.expired_date"/>

        <ps-label class="mb-8 text-xl lg:text-3xl font-semibold" >{{ $t('core_vendor__vendor_info') }}</ps-label>

        <!-- vendor information -->
        <div class="rounded-md border border-secondary-200 px-6 py-8 mb-8">
            <ps-label class=" text-xl lg:text-2xl font-semibold" >{{ $t('core_vendor__basic_info') }}</ps-label>

            <vendor-detail-vendor-info class="mt-8"
                        :validation="validation"
                        :errors="errors"
                        :form="form"
                        :coreFieldFilterSettings="coreFieldFilterSettings"
                        :customizeHeaders="customizeHeaders"
                        :authUser="authUser"
                        :customizeDetails="customizeDetails"/>
        </div>


        <div v-if="vendorStatus && vendorShow > 0" class="rounded-md border border-secondary-200 px-6 py-8 mb-8">
            <ps-label class=" text-xl lg:text-2xl font-semibold flex flex-col space-y-8" >{{ $t('core_vendor__vendor_branches') }}</ps-label>

            <div    v-for="index in vendorShow" :key="index">
                <vendor-detail-store-branches class="mt-8"
                    :storeBranches="vendorList.data[index-1]"
                    :authUser="authUser"
                    :coreFieldFilterSettings="branchesCoreFieldFilterSettings"/>

                <div class="mt-6">
                    <div class="flex flex-row justify-end space-x-4">

                        <ps-label @click="addVendorBranch()" class="flex flex-row text-base cursor-pointer px-4 py-2 border border-primary-500 rounded text-primary-500" textColor="text-primary-500"
                            v-if="index == vendorShow" >
                            <ps-icon name="plus" class="me-1" />
                            {{  $t("core_vendor__add_branch")}}
                        </ps-label>

                        <ps-label class="flex flex-row  px-4 py-2 border border-primary-500 rounded text-base cursor-pointer	text-primary-500" textColor="text-primary-500"
                            @click="removeVendorBranch(index - 1)">
                            <ps-icon name="minus" class="me-1" />
                            {{ $t("core_vendor__remove_branch") }}
                        </ps-label>
                    </div>
                </div>
            </div>
        </div>


        <div v-if="vendorShow == 0" class="flex">
            <ps-label @click="addVendorBranch()" class="flex flex-row  mb-8 text-base cursor-pointer px-4 py-2 border border-primary-500 rounded text-primary-500" textColor="text-primary-500">
                 <ps-icon name="plus" class="me-1" />
                 {{ $t("core_vendor__add_branch")}}
            </ps-label>

        </div>
         <div class="mb-2.5 flex flex-row justify-end">
            <ps-button @click="handleCancel()" type="reset" class="me-4" colors="text-primary-500" hover="">{{ $t('core__vendor_btn_cancel') }}</ps-button>
            <ps-button :disabled="!storeCan.updateMyVendor" @click="handleSubmit(vendor.id)" class="transition-all duration-300 min-w-3xs" padding="px-7 py-2" rounded="rounded" hover="" focus="" >
                <ps-label textColor="text-white dark:text-secondaryDark-black" > {{ $t('core__vendor_btn_update') }} </ps-label>
            </ps-button>
        </div>
        <ps-loading-circle-dialog ref="ps_loading_circle_dialog" />
        <ps-success-dialog ref="ps_success_dialog" />
    </ps-layout>
</template>

<script>
import { ref, defineComponent, reactive , watch } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { trans } from 'laravel-vue-i18n';
import PsVendorLayout from "@vendorPanel/vendor/components/layouts/container/PsVendorLayout.vue";
import PsBreadcrumb2 from "@/Components/Core/Breadcrumbs/PsBreadcrumb2.vue";
import PsLabelHeader6 from "@/Components/Core/Label/PsLabelHeader6.vue";
import PsButton from "@/Components/Core/Buttons/PsButton.vue";
import VendorDetailVendorInfo from './components/VendorDetailVendorInfo.vue';
import VendorDetailSubscriptionNotification from './components/VendorDetailSubscriptionNotification.vue';
import VendorDetailStoreBranches from './components/VendorDetailStoreBranches.vue';
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsLoadingCircleDialog from '@/Components/Core/Dialog/PsLoadingCircleDialog.vue';
import PsSuccessDialog from '@/Components/Core/Dialog/PsSuccessDialog.vue';
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";

    export default defineComponent({
        name: "Index",
        components: {
            Head,
            PsBreadcrumb2,
            PsLabelHeader6,
            PsButton,
            VendorDetailVendorInfo,
            VendorDetailStoreBranches,
            VendorDetailSubscriptionNotification,
            PsLabel,
            PsSuccessDialog,
            PsLoadingCircleDialog,
            PsIcon
        },
        layout: PsVendorLayout,
        props: {
            storeCan : Object,
            errors : Object,
            vendor : Object,
            coreFieldFilterSettings : Object,
            branchesCoreFieldFilterSettings : Object,
            customizeHeaders : Object,
            customizeDetails: Object,
            validation: Object,
            authUser: Object,
        },
        setup(props){
console.log(props.vendor)
              let form = useForm({
                id: props.vendor.id,
                name : props.vendor.name,
                email : props.vendor.email,
                phone : props.vendor.phone,
                address : props.vendor.address,
                description : props.vendor.description,
                website : props.vendor.website,
                facebook : props.vendor.facebook,
                instagram : props.vendor.instagram,
                logo : props.vendor.logo?.img_path,
                banner_1 : props.vendor.banner_1?.img_path,
                banner_2 : props.vendor.banner_2?.img_path,
                vendor_relation: {},
                vendorBranches :  props.vendor.vendorBranches,
                "_method": "put"
            });

            const vendorShow = ref(0);
            const vendorStatus = ref(true);
            const showNoti = ref(true);
            const vendorList = reactive({'data' : []});
            const ps_loading_circle_dialog = ref();
            const ps_success_dialog = ref();

            const defaultVendorBranch = {
                'id' : '',
                'vendor_id' : '',
                'name' : '',
                'email' : '',
                'phone' : '',
                'address' : '',
                'description' : '',
                'added_user_id' : '',
                "added_date_str" : '',
                "is_empty_object" : 1,
            };

            watch(() => props.vendor, (newValue, oldValue) => {

                vendorStatus.value = false;
                setBranchList();
                form.name = props.vendor.name;
                form.id = props.vendor.id;
                form.phone = props.vendor.phone;
                form.address = props.vendor.address;
                form.description = props.vendor.description;
                form.website = props.vendor.website;
                form.facebook = props.vendor.facebook;
                form.instagram = props.vendor.instagram;
                form.logo = props.vendor.logo?.img_path;
                form.banner_1 = props.vendor.banner_1?.img_path;
                form.banner_2 = props.vendor.banner_2?.img_path;

                setTimeout(() => {
                    vendorStatus.value = true;
                }, 200);

                props.customizeHeaders.map((customizeHeader, index) => {
                    props.vendor.vendor_relation.map((value) => {
                        if (customizeHeader.core_keys_id === value.core_keys_id) {
                            let data = (value.value);
                            form.vendor_relation[customizeHeader.core_keys_id] = data === '1' ? true : (data === '0' ? false : data)
                        }
                    });
                })
            })

            setBranchList();

            function setBranchList(){
                if(!(props.vendor.vendorBranches.length == 1 && props.vendor.vendorBranches[0].is_empty_object == '1')){
                    vendorShow.value = props.vendor.vendorBranches.length;
                    vendorList.data = props.vendor.vendorBranches;
                    form.vendorBranches = props.vendor.vendorBranches;
                }else{
                    vendorShow.value = 0;
                    vendorList.data = [];
                    form.vendorBranches = [];
                }
            }

            function removeVendorBranch(i) {

                vendorShow.value = vendorShow.value - 1;
                vendorList.data.splice(i, 1);


            }

            function addVendorBranch() {
                vendorShow.value = vendorShow.value + 1;
                vendorList.data.push(JSON.parse(JSON.stringify(defaultVendorBranch)));

            }

            function handleSubmit(id){

                form.vendorBranches = vendorList.data;

                this.$inertia.post(route("vendor_info.update", id), form, {
                    forceFormData: true,
                    onBefore: () => {
                        ps_loading_circle_dialog.value.openModal(trans('core_vendor__update_vendor_title'),trans('core_vendor__update_vendor_note'));
                    },
                    onSuccess: () => {
                        ps_loading_circle_dialog.value.closeModal();
                        ps_success_dialog.value.openModal(trans('core_vendor__update_vendor_success'), trans('core_vendor__update_vendor_success_message'), trans('core__vendor_btn_ok'),
                            ()=>{

                            },
                            false);
                    },
                    onError: () => {
                        ps_loading_circle_dialog.value.closeModal();
                    },
                });
            }


            return {
                form,
                showNoti,
                vendorShow,
                removeVendorBranch,
                addVendorBranch,
                vendorStatus,
                vendorList,
                handleSubmit,
                ps_loading_circle_dialog,
                ps_success_dialog,
            }
        },
        computed: {
            breadcrumb() {
                return [
                    {
                        label: trans('core__vendor_my_store_module'),
                    },
                    {
                        label: trans('core_vendor__edit_vendor_info'),
                        color: "text-primary-500"
                    }
                ]
            },
        },
        methods: {
            handleCancel() {
                this.$inertia.get(route('vendor_info.index'));
            },
        },
        created() {
            this.customizeHeaders.map((customizeHeader, index) => {
                this.vendor.vendor_relation.map((value) => {
                    if (customizeHeader.core_keys_id === value.core_keys_id) {
                        let data = (value.value);
                        this.form.vendor_relation[customizeHeader.core_keys_id] = data === '1' ? true : (data === '0' ? false : data)
                    }
                });
            })
        },
    })
</script>
