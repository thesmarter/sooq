<template>
    <Head :title="$t('subcategory_module')" />
    <ps-layout>

        <!-- breadcrumb start -->
        <ps-breadcrumb-2 :items="breadcrumb" class="mb-5 sm:mb-6 lg:mb-8" />
        <!-- breadcrumb end -->

        <!-- alert banner start -->
        <ps-banner-icon  v-if="visible" :visible="visible"
        :theme="(status.flag)=='danger'?'bg-red-500':(status.flag)=='warning'?'bg-yellow-500':'bg-green-500'"
        :iconName="(status.flag)=='danger'?'close-circle':(status.flag)=='warning'?'alert-triangle':'rightalert'"
        class="text-white mb-5 sm:mb-6 lg:mb-8"
         iconColor="white">{{status.msg}}</ps-banner-icon>
        <!-- alert banner end -->

        <!-- data table start -->
        <ps-table2  :row="row" :search="search" :object="this.subcategories" :colFilterOptions="colFilterOptions" :columns="columns" :sort_field="sort_field" :sort_order="sort_order" @FilterOptionshandle="FilterOptionshandle"
        @handleSort="handleSorting"  @handleSearch="handleSearching" @handleRow="handleRow"
            :globalSearchPlaceholder="$t('core__be_search_subcategory')"
            :searchable="showFilter">

            <!-- add new field start -->
            <template #button>
                <ps-button v-if="can.createSubcategory" @click="handleCreate()"  rounded="rounded-lg" type="button" class="flex flex-wrap items-center"> <ps-icon name="plus" class="me-2 font-semibold" />{{$t('core__be_add_subcategory')}}</ps-button>
            </template>
            <template #responsive_button>
                <ps-button v-if="can.createSubcategory" @click="handleCreate()"  rounded="rounded-lg" type="button" class="flex flex-wrap items-center"> <ps-icon name="plus" class="me-2 font-semibold" />{{$t('core__be_add_subcategory')}}</ps-button>
            </template>
            <!-- add new field end -->

            <template #searchRight>
                <!-- <ps-dropdown align="" class="lg:w-56 md:40 h-10 sm:w-full">
                    <template #select>
                            <ps-dropdown-select :placeholder="$t('core__be_categories')"
                                                    :selectedValue="(selected_cat == '' || selected_cat == 'all') ? '' : categories.filter(option => option.id == selected_cat)[0].name"
                                                    />
                    </template>
                            <template #list>
                                <div class="rounded-md shadow-xs lg:w-56 md:40 h-10 sm:w-full">
                                    <div class="pt-2 z-30  ">
                                        <div class="lg:w-56 md:40 h-10 sm:w-full flex py-2 px-2 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center"
                                                @click="handleCategoryfilter('all')">
                                            <ps-label class="text-gray-200">{{ $t('core__be_select_all') }}</ps-label>
                                        </div>
                                        <div v-for="category in categories" :key="category.id"
                                                class="lg:w-56 md:40 h-10 sm:w-full flex py-2 px-2 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center"
                                                @click="handleCategoryfilter(category.id)">
                                            <ps-label class="ms-2"
                                                        :class="category.id == selected_cat ? ' font-bold' : ''">
                                                {{ category.name }} </ps-label>
                                        </div>
                                    </div>
                                </div>
                    </template>
                </ps-dropdown> -->
                               <!-- category filter -->
                <ps-dropdown @on-click="dropdownClick" align="" class="lg:w-56 md:40 h-10 sm:w-full" >
                    <template #select>
                        <ps-dropdown-select :placeholder="$t('core__be_category')" :border="(selected_cat !== '' && selected_cat !== 'all') ?'border border-indigo-500/100':'border border-1 border-secondary-200'"
                            :selectedValue="(selected_cat == '' || selected_cat == 'all') ? '' : selectedCategory.name "
                        />
                    </template>
                    <template #list>
                        <div class="rounded-md shadow-xs w-56 ">
                            <div class="pt-2 z-30  ">
                                <div class="w-56 flex py-2 px-2 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center"
                                    @click="handleCategoryfilter('all')">
                                    <ps-label class="text-gray-200 ms-2">{{$t('core__be_select_all')}}</ps-label>
                                </div>
                                <div v-for="category in categories" :key="category.id"
                                    class="w-56 flex py-2 px-2 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center"
                                    @click="handleCategoryfilter(category.id)">
                                    <ps-label class="ms-2" :class="category.id == selected_cat ? ' font-bold' : ''">
                                        {{ category.name }} </ps-label>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template #loadmore>
                       <div  @click="dropdownClick(true)" v-if="category_loadmore_visible" class="w-56 flex py-2 px-2 hover:bg-primary-000 dark:hover:bg-primary-900 cursor-pointer items-center">
 			            <div class="flex flex-row items-center justify-between">
                                    <ps-label  class="ms-2 ">
                                        {{is_loading ? $t('core__be_loading') :$t('core__be_load_more')}}
                                    </ps-label>
                                    <ps-icon theme="text-black dark:text-primary-900" name="load" w="16" h="16" />
                        </div>
                       </div>
                    </template>
                     <template #filter>

                        <div class="mt-1 mx-1">
                            <ps-input-with-right-icon  class="w-full h-10"  rounded="rounded-lg" v-model:value="catSearch" :placeholder="$t('core__be_search')" >
                                <template #icon >
                                    <ps-icon name="search" class='cursor-pointer' />
                                </template>
                            </ps-input-with-right-icon>
                        </div>
                    </template>
                </ps-dropdown>
            </template>

            <!-- for csv file import start -->
            <template #searchLeft>
                <ps-button  v-if="can.createSubcategory"  @click="csvUploadClicked" rounded="rounded" class="flex flex-wrap items-center ms-3 " >
                    <ps-icon name="plus" class="me-2 font-semibold" />
                    <ps-label textColor="text-white dark:text-secondary-800">{{ $t('core__be_import_file') }}</ps-label>
                </ps-button>
                <ps-csv-modal ref="ps_csv_modal" url="https://drive.google.com/file/d/1PtFk3RrBH5AhMZzo-6Ga_tP8yE3flwUm/view?usp=sharing"/>
            </template>
            <!-- for csv file import start -->

            <template #tableActionRow="rowProps">
                <!-- For action (edit/delete) start -->
                <ps-label v-if="rowProps.field == 'action'">
                    <div class="flex flex-row">
                        <ps-button :disabled="!rowProps.row.authorization.update" @click="handleEdit(rowProps.row.id)" class="me-2" colors="bg-green-400 text-white" padding="p-1.5"
                            hover="hover:outline-none hover:ring hover:ring-green-100"
                            focus="focus:outline-none focus:ring focus:ring-green-200">
                            <ps-icon theme="text-white dark:text-primary-900" name="editPencil" w="16" h="16" />
                        </ps-button>
                        <ps-button :disabled="!rowProps.row.authorization.delete" @click="confirmDeleteClicked(rowProps.row.id)" colors="bg-red-400 text-white" padding="p-1.5"
                            hover="hover:outline-none hover:ring hover:ring-red-100"
                            focus="focus:outline-none focus:ring focus:ring-red-200">
                            <ps-icon theme="text-white dark:text-primary-900" name="trash" w="16" h="16" />
                        </ps-button>
                        <ps-danger-dialog ref="ps_danger_dialog" />
                    </div>
                </ps-label>
                <!-- For action (edit/delete) end -->
            </template>
            <template #tableRow="rowProps">
                <ps-label v-if="rowProps.field == 'status'" v-show="colFilterOptions.filter((item) => item.key == 'status').length == 0
                            ? true : !colFilterOptions.filter((item) => item.key == 'status')[0].hidden ">
                    <ps-toggle :disabled="!rowProps.row.authorization.update" :selectedValue="rowProps.row.status == 1 ? true : false"
                        @click="handlePublish(rowProps.row.id,rowProps.row.authorization.update)"></ps-toggle>
                </ps-label>
        </template>

        </ps-table2>
        <!-- data table end -->
    </ps-layout>
</template>

<script>
import { defineComponent, ref, reactive, watch } from 'vue'
import PsLayout from "@/Components/PsLayout.vue";
import { Link, Head, useForm } from '@inertiajs/vue3';
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsButton from "@/Components/Core/Buttons/PsButton.vue";
import PsTable2 from "@/Components/Core/Table/PsTable2.vue";
import PsAlert from "@/Components/Core/Alert/PsAlert.vue";
import PsBreadcrumb2 from "@/Components/Core/Breadcrumbs/PsBreadcrumb2.vue";
import PsDangerDialog from "@/Components/Core/Dialog/PsDangerDialog.vue";
import PsToggle from '@/Components/Core/Toggle/PsToggle.vue';
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsCsvModal from '@/Components/Core/Modals/PsCsvModal.vue';
import PsBannerIcon from "@/Components/Core/Banners/PsBannerIcon.vue";
import Dropdown from "@/Components/Core/DropdownModal/Dropdown.vue";
import PsDropdown from "@/Components/Core/Dropdown/PsDropdown.vue";
import PsDropdownSelect from "@/Components/Core/Dropdown/PsDropdownSelect.vue";
import PsIconButton from "@/Components/Core/Buttons/PsIconButton.vue";
import PsTextButton from "@/Components/Core/Buttons/PsTextButton.vue";
import { trans } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';
import { getCategories, getSubCat, getCustomFields, getCities, getTownships, getUsers } from '@/Api/psApiService.js'
import PsInput from "@/Components/Core/Input/PsInput.vue";
import debounce from 'lodash/debounce';
import PsInputWithRightIcon from '@/Components/Core/Input/PsInputWithRightIcon.vue';

    export default defineComponent({
        name: "Index",
        components: {
            PsDropdown,
            PsDropdownSelect,
            Link,
            Head,
            PsLabel,
            PsButton,
            PsTable2,
            PsAlert,
            PsBreadcrumb2,
            PsDangerDialog,
            PsToggle,
            PsIcon,
            PsCsvModal,
            PsBannerIcon,
            Dropdown,
            PsIconButton,
            PsTextButton,
            PsInput,
            PsInputWithRightIcon
        },
        layout : PsLayout,
        props:{
            can:Object,
            status:Object,
            categories:Object,
            subcategories:Object,
            owners:Object,
            hideShowFieldForFilterArr:Object,
            showCoreAndCustomFieldArr:Object,
            selectedCategory:{type:String,default:''},
            authUser:Object,
            sort_field:{
                    type:String,
                    default:"",

                },
            sort_order:{
                type:String,
                default:'desc',
            },
            search:String,
        },
        data() {
            return {
                form: useForm(
                    {
                        csvFile : ""
                    }
                )
            }
        },
        setup(props) {
            // For data table
             const showFilter = ref(false);
            const clearFilter = ref(false);
            let sorting = "";
            let search = props.search ? ref(props.search) : ref('');
            let sort_field = props.sort_field ? ref(props.sort_field) : ref('');
            let sort_order = props.sort_order ? ref(props.sort_order) : ref('desc');
            let selected_cat = props.selectedCategory ? ref(props.selectedCategory.id) : ref('');

            let categories = ref([]);
            let category_loadmore_visible= ref(false);
            let catSearch = ref();
            let is_loading = ref(false);

            const ps_danger_dialog = ref();
            const ps_csv_modal = ref();
            let visible = ref(false)


        const colFilterOptions = ref();

        const columns = ref();

        function confirmDeleteClicked($id) {
            ps_danger_dialog.value.openModal(
                trans('core__delete'),
                trans('core__comfirm_to_delete_subcategory'),
                trans('core__be_btn_confirm'),
                trans('core__be_btn_cancel'),
                () => {
                    this.$inertia.delete(route("subcategory.destroy", $id),{
                        onSuccess: () => {
                            visible.value = true;
                            setTimeout(() => {
                                visible.value = false;
                            }, 3000);
                        },
                        onError: () => {
                            visible.value = true;
                            setTimeout(() => {
                                visible.value = false;
                            }, 3000);
                        },
                    })
                },
                () => { }
            );
        }

         function handleSorting(value){
            sort_field.value = value.field
            sort_order.value = value.sort_order
            handleSearchingSorting()
        }
        function handleClearFilter(){
             selected_cat.value = 'all';

             handleSearchingSorting();
        }
        function handleCategoryfilter(value){
            selected_cat.value = value
            let page=1;
            handleSearchingSorting(page);

        }

        function csvUploadClicked(){
            ps_csv_modal.value.openModal(
                (selectedFile) => {
                    let form = useForm({
                        csvFile: selectedFile,
                        "_method": "put"
                    })
                    router.post(route('subcategory.import.csv'), form)
                }
            );
        }
         function handleSearching(value){
            search.value = value;
            let page=1;
            handleSearchingSorting(page);
        }
        function handleRow(value){
            handleSearchingSorting(1,value);
        }

        function handleSearchingSorting(page = null,row=null){
            router.get(route('subcategory.index'),
            {
                sort_field : sort_field.value,
                sort_order: sort_order.value,
                page:page ?? props.subcategories.meta.current_page,
                row:row ?? props.subcategories.meta.per_page,
                search:search.value,
                category_filter:selected_cat.value,
            },
            {
                preserveScroll: true,
                preserveState:true,
            })
        }
                            // Category data
            function getCategoriesData(offset){
                category_loadmore_visible.value = true;
                is_loading.value = true
                getCategories(offset,catSearch.value,props.authUser.id).then(response => {

                    if(!response.data.length){
                        category_loadmore_visible.value = false;
                    }
                    else{
                        response.data.forEach(element =>{
                            categories.value.push(element);
                        });
                    }
                    is_loading.value=false;
                });
            }

            function dropdownClick(loadMore = null) {

                let offset = categories.value ? categories.value.length : 0 ;
                if(offset == 0 || loadMore == true){

                    getCategoriesData(offset);
                }
            }
            watch(catSearch,_.debounce((current,previous)=>{
                let offset= 0;
                categories.value = [];
                getCategoriesData(offset);

            },500))

        return {
            columns,
            ps_danger_dialog,
            confirmDeleteClicked,
            ps_csv_modal,
            csvUploadClicked,
            colFilterOptions,
            visible,

            showFilter,
            clearFilter,
            handleSorting,
            handleSearchingSorting,
            handleCategoryfilter,
            handleClearFilter,
            handleRow,
            handleSearching,
            selected_cat,
             dropdownClick,
            categories,
            category_loadmore_visible,
            catSearch,
            is_loading,
            }
    },
    created() {
        this.columns = this.showCoreAndCustomFieldArr.map(column => {
            return {
                action: column.action,
                field: column.field,
                label: trans(column.label),
                sort: column.sort,
                type: column.type
            };
        });

        this.colFilterOptions = this.hideShowFieldForFilterArr.map(columnFilterOption => {
            return {
                hidden: columnFilterOption.hidden,
                id: columnFilterOption.id,
                key: trans(columnFilterOption.key),
                key_id: columnFilterOption.key_id,
                label: trans(columnFilterOption.label),
                module_name: columnFilterOption.module_name
            };
        });
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
                    color: "text-primary-500"
                }
            ]
        }
    },
    methods: {
        handleCreate() {
            this.$inertia.get(route("subcategory.create"));
        },
        handleEdit(id){
            this.$inertia.get(route('subcategory.edit',id));
        },
        handlePublish(id,hasPermission){
            if(hasPermission){
                this.$inertia.put(route('subcategory.statusChange',id));
            }
        },
        FilterOptionshandle(value) {
        router.put(route('subcategory.screenDisplayUiSetting.store'),
            {
                value,
                sort_field :this.sort_field ,
                sort_order:this.sort_order,
                row:this.categories.per_page,
                search:this.search.value,
                current_page:this.categories.current_page
            },
            {
                preserveScroll: true,
                preserveState:true,
            });

        },
    },
})
</script>



<style scoped>

</style>
