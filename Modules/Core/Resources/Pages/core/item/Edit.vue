<template>
    <Head :title="$t('core__be_edit_item_lbl')" />
    <ps-layout>
        <!-- breadcrumb start -->
        <ps-breadcrumb-2 :items="breadcrumb" class="mb-5 sm:mb-6 lg:mb-8" />
        <!-- breadcrumb end -->

        <ps-card class="w-full h-auto">
            <div class="rounded-xl dark:bg-backgroundDark">
                <!-- card header start -->
                <div class="bg-primary-50 dark:bg-primary-900 py-2.5 ps-4 rounded-t-xl">
                    <ps-label-header-6 textColor="text-secondary-800 dark:text-secondary-100">{{
                        $t('core__be_edit_item_lbl') }}</ps-label-header-6>
                </div>

                <div class='flex flex-col w-full mb-8 mt-2' v-if="vendorSetting && vendor_list.length > 0 && form.category_id != ''">
                    <ps-label-header-6 class="px-4 mt-4" textColor="text-secondary-300 dark:text-secondary-100">{{ $t('core_be__upload_as_vendor') }}
                    </ps-label-header-6>
                    <div class="px-4 mb-4" >
                        <ps-dropdown  :disabled="true" align="left" class='lg:mt-2 mt-1  w-full' >
                            <template #select>
                                <button :disabled="true"  class="w-full cursor-pointer flex flex-row justify-between px-4 py-1.5 items-center rounded-md border border-secondary-200">
                                    <div v-if="item.vendor && item.vendor?.id != ''" class="flex flex-row">
                                        <img v-lazy=" { src: $page.props.thumb1xUrl + '/' + item.vendor.logo?.img_path, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_vendor_shop.png' }"
                                                    class="w-6 h-6  rounded-full "  width="12px" height="12px" />
                                        <ps-label class="text-sm  ms-2" textColor="text-secondary-300" > {{ item.vendor?.name }} </ps-label>
                                    </div>
                                    <div v-else class="flex flex-row">
                                        <img v-lazy=" { src: $page.props.thumb1xUrl + '/' + authUser.user_cover_photo, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_profile.png' }"
                                                    class="w-6 h-6  rounded-full "  width="12px" height="12px" />
                                        <ps-label class="text-sm  ms-2" textColor="text-secondary-300 " > {{ authUser.name }} </ps-label>
                                    </div>

                                    <ps-icon name="downChervon" />

                                </button>
                            </template>
                        </ps-dropdown>
                    </div>
                    <div class="px-4 mt-0.5">
                        <div class="flex flex-row">
                            <div class="me-2 flex items-center">
                                <input type="checkbox" :disabled="true" class="rounded border text-primary-500" value="1"
                                     />
                                <ps-label class="ms-2" textColor="text-secondary-300" >{{ $t('core_be__always_choose_this_profile') }}</ps-label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- card header end -->
                <ps-label-header-6 class="px-4 mt-4" textColor="text-secondary-800 dark:text-secondary-100">{{ $t('category_selection_prioritize') }}
                </ps-label-header-6>
                <!-- for category dropdown -->
                <div class="w-full px-4 mt-4 grid xxl:grid-cols-2 xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1 gap-12">
                    <!-- for category dropdown -->
                    <div class="w-full"
                        v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'category_id' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                        <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                            v-if="coreField.mandatory = 1"
                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                        <ps-tooltip arrow="middle" tooltiptext="absolute z-50 bottom-full ms-36 md:ms-40 lg:ms-64 xl:ms-92 xxl:ms-110" tooltipWidth="relative w-2/3" >
                            <template #content>
                                <ps-dropdown class="lg:mt-2 mt-1 w-full" :disabled="true">
                                    <template #select>
                                        <ps-dropdown-select ref="category" :placeholder="$t(coreField.placeholder)"
                                            :disabled="true"
                                            :showCenter="true"
                                            :selectedValue="form.category_id == '' ? '' : categories.filter((category) => category.id == form.category_id)[0].name"/>
                                    </template>
                                </ps-dropdown>
                             </template>
                                <template #tooltips>
                                    {{ $t('category_edit_alert_desc') }}
                                </template>
                        </ps-tooltip>
                    </div>
                </div>
                <!-- card body start -->
                <div class="px-4 pt-6 dark:bg-backgroundDark">
                    <form @submit.prevent="handleSubmit(item.id)">
                        <div class="grid xxl:grid-cols-2 xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1">
                            <div class="grid gap-6">
                                <div>
                                    <ps-label-header-6 textColor="text-secondary-800 dark:text-secondary-100">{{
                                        $t('core__be_item_info_lbl') }}</ps-label-header-6>
                                </div>

                                <!-- title-->
                                <div class="md:me-6 sm:me-0"
                                    v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'title' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }} <span
                                            v-if="coreField.mandatory = 1"
                                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                                    <ps-input ref="title" type="text" v-model:value="form.title"
                                        :placeholder="$t(coreField.placeholder)"
                                        @keyup="coreField.mandatory == 1 ? validateEmptyInput('title', form.title) : ''"
                                        @blur="coreField.mandatory == 1 ? validateEmptyInput('title', form.title) : ''" />
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.title }}</ps-label-caption>
                                </div>

                                <div v-if="selected_price_type == PsConst.NORMAL_PRICE">
                                    <!-- original price-->
                                    <div class="md:me-6 sm:me-0"
                                        v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'original_price' && coreField.enable === 1 && coreField.is_delete === 0)"
                                        :key="index">
                                        <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                                v-if="coreField.mandatory = 1"
                                                class="text-red-800 font-medium ms-1">*</span></ps-label>
                                        <ps-input ref="original_price" type="text" v-model:value="form.original_price"
                                            :placeholder="$t(coreField.placeholder)"
                                            @keyup="[coreField.mandatory = 1 ? validatePriceInput('original_price', form.original_price) : '', handleUnitPrice()]"
                                            @blur="[coreField.mandatory = 1 ? validatePriceInput('original_price', form.original_price) : '', handleUnitPrice()]"
                                            @keypress="[coreField.mandatory = 1 ? onlyNumberWithCustom : '', handleUnitPrice()]" />
                                        <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                            class="mt-2 block">{{ errors.original_price }}</ps-label-caption>
                                    </div>

                                    <!-- sale price-->
                                    <div class="md:me-6 sm:me-0"
                                        v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'price' && coreField.enable === 1 && coreField.is_delete === 0)"
                                        :key="index">
                                        <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                                v-if="coreField.mandatory = 1"
                                                class="text-red-800 font-medium ms-1">*</span></ps-label>
                                        <ps-input :readonly="true" ref="price" type="text" v-model:value="form.price"
                                            :placeholder="$t(coreField.placeholder)"
                                            @keyup="coreField.mandatory = 1 ? validatePriceInput('price', form.price) : ''"
                                            @blur="coreField.mandatory = 1 ? validatePriceInput('price', form.price) : ''"
                                            @keypress="coreField.mandatory = 1 ? onlyNumberWithCustom : ''" />
                                        <ps-label-caption v-if="coreField.mandatory = 1" textColor="text-red-500 "
                                            class="mt-2 block">{{ errors.price }}</ps-label-caption>
                                    </div>

                                    <!-- for currency dropdown -->
                                    <div class="md:me-6 sm:me-0"
                                        v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'currency_id' && coreField.enable === 1 && coreField.is_delete === 0)"
                                        :key="index">
                                        <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                                v-if="coreField.mandatory = 1"
                                                class="text-red-800 font-medium ms-1">*</span></ps-label>
                                        <ps-dropdown align="left" class="lg:mt-2 mt-1 w-full">
                                            <template #select>
                                                <ps-dropdown-select ref="currency" :placeholder="$t(coreField.placeholder)"
                                                    :showCenter="true"
                                                    :selectedValue="form.currency_id == '' ? '' : currencies.filter((currency) => currency.id == form.currency_id)[0].currency_short_form"
                                                    @change="coreField.mandatory = 1 ? validateEmptyInput('currency_id', form.currency_id) : ''"
                                                    @blur="coreField.mandatory = 1 ? validateEmptyInput('currency_id', form.currency_id) : ''" />
                                            </template>
                                            <template #list>
                                                <div
                                                    class="rounded-md shadow-xs w-full bg-background dark:bg-backgroundDark">
                                                    <div class="pt-2 z-30">
                                                        <div v-if="currencies.length == null">
                                                            <ps-label class="p-2 flex" @click="route('currency.index')">{{
                                                                $t('core__be_add_new_currency') }}</ps-label>
                                                        </div>
                                                        <div v-else>
                                                            <div v-for="currency in currencies" :key="currency.id"
                                                                class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                                @click="[(form.currency_id = currency.id), coreField.mandatory = 1 ? validateEmptyInput('currency_id', form.currency_id) : '']">
                                                                <ps-label class="ms-2"
                                                                    :class="currency.id == form.currency_id ? ' font-bold' : ''">{{
                                                                        currency.currency_short_form }}</ps-label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </ps-dropdown>
                                        <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                            class="mt-2 block">{{ errors.currency_id }}</ps-label-caption>
                                    </div>
                                    <!-- end currency -->
                                </div>

                                <div v-if="selected_price_type == PsConst.PRICE_RANGE">
                                    <div class="md:me-6 sm:me-0"
                                        v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'original_price' && coreField.enable === 1 && coreField.is_delete === 0)"
                                        :key="index">
                                        <!-- <ps-label class="text-base">{{ $t(coreField.label_name)}}

                                        </ps-label> -->
                                        <ps-label class="text-base">{{ $t(coreField.label_name) }}<span v-if="coreField.mandatory=1" class="text-red-800 font-medium ms-1">*</span></ps-label>
                                        <ps-dropdown align="left" class="lg:mt-2 mt-1 w-full">
                                            <template #select>
                                                <ps-dropdown-select ref="category" :placeholder="$t(coreField.placeholder)"
                                                    :showCenter="true"
                                                    :selectedValue="form.original_price == '' ? '' : item_price_range.item_price_range_selections.filter((price) => price.id == form.original_price)[0].value"
                                                    @change="[coreField.mandatory = 1 ? validateEmptyInput('original_price', form.original_price) : '',]"
                                                    @blur="coreField.mandatory = 1 ? validateEmptyInput('original_price', form.original_price) : ''" />
                                            </template>
                                            <template #list>
                                                <div
                                                    class="rounded-md shadow-xs w-full bg-background dark:bg-backgroundDark">
                                                    <div class="pt-2 z-30">

                                                        <div v-for="price in item_price_range.item_price_range_selections"
                                                            :key="price.id"
                                                            class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                            @click="[(form.original_price = price.id), coreField.mandatory = 1 ? validateEmptyInput('original_price', form.original_price) : '',]">
                                                            <ps-label class="ms-2"
                                                                :class="price.id == form.original_price ? ' font-bold' : ''">{{
                                                                    price.value }}</ps-label>
                                                        </div>


                                                    </div>
                                                </div>
                                            </template>
                                        </ps-dropdown>
                                        <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                            class="mt-2 block">{{ errors.original_price }}</ps-label-caption>
                                    </div>
                                </div>

                                <!-- for subcategory dropdown -->
                                <!-- <div v-if="$page.props.isSubCategoryOn == '1'"> -->
                                <div class="md:me-6 sm:me-0"
                                    v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'subcategory_id' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }}
                                        <!-- <span v-if="coreField.mandatory=1" class="text-red-800 font-medium ms-1">*</span> -->
                                    </ps-label>
                                    <ps-dropdown align="left" class="lg:mt-2 mt-1 w-full">
                                        <template #select>
                                            <ps-dropdown-select ref="category" :placeholder="$t(coreField.placeholder)"
                                                :showCenter="true"
                                                :selectedValue="form.subcategory_id == '' ? '' : subcategories.filter((subCategory) => subCategory.id == form.subcategory_id)[0].name"
                                                @change="[(form.subcategory_id = subcategory.id), coreField.mandatory = 1 ? validateEmptyInput('subcategory', form.subcategory_id) : '',]" />
                                        </template>
                                        <template #list>
                                            <div class="rounded-md shadow-xs w-full bg-background dark:bg-backgroundDark">
                                                <div class="pt-2 z-30">
                                                    <div v-if="subcategories.length === 0">
                                                        <ps-label class="p-2 flex cursor-pointer">{{
                                                            $t('core__be_add_new_subcategory') }}</ps-label>
                                                    </div>
                                                    <div v-else-if="form.category_id">
                                                        <div v-for="subcat in subcategories.filter((subCat) => subCat.category_id == form.category_id)"
                                                            :key="subcat.id"
                                                            class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                            @click="[(form.subcategory_id = subcat.id), coreField.mandatory = 1 ? validateEmptyInput('subcategory_id', form.subcategory_id) : '',]">
                                                            <ps-label class="ms-2"
                                                                :class="subcat.id == form.subcategory_id ? ' font-bold' : ''">{{
                                                                    subcat.name }}</ps-label>
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        <div v-for="subcat in subcategories" :key="subcat.id"
                                                            class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                            @click="[(form.subcategory_id = subcat.id), coreField.mandatory = 1 ? validateEmptyInput('subcategory_id', form.subcategory_id) : '',]">
                                                            <ps-label class="ms-2"
                                                                :class="subcat.id == form.subcategory_id ? ' font-bold' : ''">{{
                                                                    subcat.name }}</ps-label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </ps-dropdown>
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.subcategory_id }}</ps-label-caption>
                                </div>
                                <!-- </div> -->
                                <!-- end subcategory -->

                                <!-- quantity -->
                                <div v-for="(custom_field_header) in customizeHeaders.filter((custom) => custom.core_keys_id == 'ps-itm00010')" :key="custom_field_header.id">
                                    <div class="md:me-6 sm:me-0" v-if="custom_field_header.ui_type_id === 'uit00007' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span class="text-red-800 font-medium ms-1" v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <ps-input type="number" class="w-full rounded" :placeholder="$t(custom_field_header.placeholder)" v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                            @keyup="handleCustomFieldError(custom_field_header)"
                                            @blur="handleCustomFieldError(custom_field_header)" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ $page.props.product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>
                                </div>
                                <!-- quantity -->

                                <!-- for location city dropdown -->
                                <div class="md:me-6 sm:me-0"
                                    v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'location_city_id' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                            v-if="coreField.mandatory = 1"
                                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                                    <ps-dropdown align="left" class="lg:mt-2 mt-1 w-full">
                                        <template #select>
                                            <ps-dropdown-select ref="location_city" :placeholder="$t(coreField.placeholder)"
                                                :showCenter="true"
                                                :selectedValue="form.location_city_id == '' ? '' : cities.filter((city) => city.id == form.location_city_id)[0].name"
                                                @change="[(form.location_city_id = city.id),
                                                coreField.mandatory == 1 ? validateEmptyInput('location_city_id', form.location_city_id) : '']" />
                                        </template>
                                        <template #list>
                                            <div class="rounded-md shadow-xs w-full bg-background dark:bg-backgroundDark">
                                                <div class="pt-2 z-20">
                                                    <div v-if="cities.length === 0">
                                                        <ps-label class="p-2 flex cursor-pointer">{{
                                                            $t('core__be_add_new_city') }}</ps-label>
                                                    </div>
                                                    <div v-else>
                                                        <div v-for="city in cities" :key="city.id"
                                                            class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                            @click="[(form.location_city_id = city.id),
                                                            form.location_township_id = form.location_township_id ? (townships.filter((township) => township.location_city_id == city.id).filter((township) => township.id == form.location_township_id)[0] ? form.location_township_id : '') : '']">
                                                            <ps-label class="ms-2"
                                                                :class="city.id == form.location_city_id ? ' font-bold' : ''">{{
                                                                    city.name }}</ps-label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </ps-dropdown>
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.category_id }}</ps-label-caption>
                                </div>
                                <!-- end location city -->

                                <!-- for location township dropdown -->
                                <div class="md:me-6 sm:me-0"
                                    v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'location_township_id' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }}
                                        <!-- <span v-if="coreField.mandatory=1" class="text-red-800 font-medium ms-1">*</span> -->
                                    </ps-label>
                                    <ps-dropdown align="left" class="lg:mt-2 mt-1 w-full">
                                        <template #select>
                                            <ps-dropdown-select ref="category" :placeholder="$t(coreField.placeholder)"
                                                :showCenter="true"
                                                :selectedValue="form.location_township_id == '' ? '' : townships.filter((township) => township.id == form.location_township_id)[0].name"
                                                @change="[(form.location_township_id = township.id), coreField.mandatory = 1 ? validateEmptyInput('township', form.location_township_id) : '',]" />
                                        </template>
                                        <template #list>
                                            <div class="rounded-md shadow-xs w-full bg-background dark:bg-backgroundDark">
                                                <div class="pt-2 z-30">
                                                    <div v-if="townships.length === 0">
                                                        <ps-label class="p-2 flex cursor-pointer">{{
                                                            $t('core__be_add_new_township') }}</ps-label>
                                                    </div>
                                                    <div v-else-if="form.location_city_id">
                                                        <div v-for="township in townships.filter((township) => township.location_city_id == form.location_city_id)"
                                                            :key="township.id"
                                                            class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                            @click="[(form.location_township_id = township.id), coreField.mandatory = 1 ? validateEmptyInput('location_township_id', form.location_township_id) : '',]">
                                                            <ps-label class="ms-2"
                                                                :class="township.id == form.location_township_id ? ' font-bold' : ''">{{
                                                                    township.name }}</ps-label>
                                                        </div>
                                                    </div>
                                                    <div v-else>
                                                        <div v-for="township in townships" :key="township.id"
                                                            class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                            @click="[(form.location_township_id = township.id), coreField.mandatory = 1 ? validateEmptyInput('location_township_id', form.location_township_id) : '',]">
                                                            <ps-label class="ms-2"
                                                                :class="township.id == form.location_township_id ? ' font-bold' : ''">{{
                                                                    township.name }}</ps-label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </ps-dropdown>
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.location_township_id }}</ps-label-caption>
                                </div>
                                <!-- end location township -->

                                <!-- for description-->
                                <div class="md:me-6 sm:me-0"
                                    v-for="(coreField, index ) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'description' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }} <span
                                            v-if="coreField.mandatory == 1"
                                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                                    <ps-textarea rows="4" v-model:value="form.description"
                                        :placeholder="$t(coreField.description)" />
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.description }}</ps-label-caption>
                                </div>

                                <div v-if="selected_price_type == 'NORMAL_PRICE'">
                                    <div class="md:me-6 sm:me-0"
                                        v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'percent' && coreField.enable === 1 && coreField.is_delete === 0)"
                                        :key="index">
                                        <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                                v-if="coreField.mandatory == 1"
                                                class="text-red-800 font-medium ms-1">*</span></ps-label>
                                        <ps-input ref="percent" type="text" v-model:value="form.percent"
                                            :placeholder="$t(coreField.placeholder)"
                                            @keyup="[coreField.mandatory = 1 ? validatePriceInput('percent', form.percent) : '', handleUnitPrice()]"
                                            @blur="[coreField.mandatory = 1 ? validatePriceInput('percent', form.percent) : '', handleUnitPrice()]"
                                            @keypress="[coreField.mandatory = 1 ? onlyNumberWithCustom : '', handleUnitPrice()]" />
                                        <ps-label-caption v-if="coreField.mandatory = 1" textColor="text-red-500 "
                                            class="mt-2 block">{{ errors.percent }}</ps-label-caption>
                                    </div>
                                </div>
                                <!-- percent -->

                                <!-- custom field start -->
                                <div v-for="(custom_field_header) in customizeHeaders.filter((custom) => custom.core_keys_id != 'ps-itm00009' && custom.core_keys_id != 'ps-itm00010')"
                                    :key="custom_field_header.id">
                                    <!-- dropdown-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00001' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory == 1">*</span></ps-label>
                                        <ps-dropdown align="left" class="lg:mt-2 mt-1 w-full">
                                            <template #select>
                                                <ps-dropdown-select ref="detail"
                                                    :placeholder="$t(custom_field_header.placeholder)" :showCenter="true"
                                                    :selectedValue="customizeDetails.filter((detail) => detail.id ==
                                                        form.product_relation[custom_field_header.core_keys_id]).length > 0 ?
                                                        customizeDetails.filter((detail) => detail.id == (form.product_relation[custom_field_header.core_keys_id]))[0].name : ''"
                                                    @change="handleCustomFieldError(custom_field_header)"
                                                    @blur="handleCustomFieldError(custom_field_header)" />
                                            </template>
                                            <template #list>
                                                <div
                                                    class="rounded-md shadow-xs w-full bg-background dark:bg-backgroundDark">
                                                    <div class="pt-2 z-30">
                                                        <div
                                                            v-if="customizeDetails.filter((customizeDetail) => customizeDetail.core_keys_id === custom_field_header.core_keys_id).length === 0">
                                                            <ps-label class="p-2 flex" @click="route('currency.index')">{{
                                                                $t('core__be_create_new') }}
                                                                {{ $t(custom_field_header.name) }}</ps-label>
                                                        </div>
                                                        <div v-else>
                                                            <div v-for="detail in customizeDetails.filter((customizeDetail) => customizeDetail.core_keys_id === custom_field_header.core_keys_id)"
                                                                :key="detail.id"
                                                                class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                                @click="[(form.product_relation[custom_field_header.core_keys_id] = detail.id), validateEmptyInput('currency_id', form.product_relation[custom_field_header.core_keys_id])]">
                                                                <ps-label class="ms-2"
                                                                    :class="detail.id == form.product_relation[custom_field_header.core_keys_id] ? ' font-bold' : ''">{{
                                                                        detail.name }}</ps-label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </ps-dropdown>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- text-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00002' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label>{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <ps-input type="text" class="w-full rounded"
                                            :placeholder="$t(custom_field_header.placeholder)"
                                            v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                            @keyup="handleCustomFieldError(custom_field_header)"
                                            @blur="handleCustomFieldError(custom_field_header)" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- radio-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00003' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div class="flex flex-row">
                                            <div class="me-2"
                                                v-for="detail in customizeDetails.filter((customizeDetail) => customizeDetail.core_keys_id === custom_field_header.core_keys_id)"
                                                :key="detail.id">
                                                <ps-radio-value color="text-purple-600 border-purple-300"
                                                    v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                                    :title="detail.id.toString()" :title_label="detail.name" />
                                                <ps-label :for="detail.id">{{ detail.attribute }}</ps-label>
                                            </div>
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- checkbox-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00004' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="mb-1">{{ $t(custom_field_header.name) }}</ps-label>
                                        <div class="flex flex-row">
                                            <div class="me-2 flex">
                                                <input type="checkbox" class="rounded border" value="0"
                                                    v-model="form.product_relation[custom_field_header.core_keys_id]"
                                                    @change="handleCustomFieldError(custom_field_header)" />
                                                <ps-label class="ms-2">{{ $t(custom_field_header.placeholder) }}</ps-label>
                                            </div>
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- datetime -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00005' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div>
                                            <date-picker
                                                v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                                @keyup="handleCustomFieldError(custom_field_header)"
                                                @blur="handleCustomFieldError(custom_field_header)" :enableTimePicker="true"
                                                :inline="false" :autoApply="false" />
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- textarea -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00006' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <ps-textarea rows="4" :placeholder="$t(custom_field_header.placeholder)"
                                            v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                            @keyup="handleCustomFieldError(custom_field_header)"
                                            @blur="handleCustomFieldError(custom_field_header)" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- number-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00007' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <ps-input type="number" class="w-full rounded"
                                            :placeholder="$t(custom_field_header.placeholder)"
                                            v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                            @keyup="handleCustomFieldError(custom_field_header)"
                                            @blur="handleCustomFieldError(custom_field_header)" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- multi Select-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00008' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div class="flex flex-row">
                                            <CheckBox :oldData="form.product_relation[custom_field_header.core_keys_id]"
                                                @toParent="handleMultiSelect" :customizeDetails="customizeDetails"
                                                :customizeHeader="custom_field_header" />
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- Image-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00009' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div v-if="item.image" class="flex items-end pt-4">
                                            <img v-lazy="{ src: $page.props.uploadUrl + '/' + item.image.img_path, loading: $page.props.sysImageUrl + '/loading_gif.gif', error: $page.props.sysImageUrl + '/default_photo.png' }"
                                                class="w-96 h-48" :alt="$t(core__be_item_photo)" />
                                            <ps-button type="button" @click="replaceImageClicked(item.image.id)"
                                                rounded="rounded-full" shadow="drop-shadow-2xl" class="-ms-10 mb-2"
                                                colors="bg-white text-primary-500 dark:bg-secondaryDark-black"
                                                border="border border-1 dark:border-secondary-700 border-secondary-300"
                                                padding="p-1.5" hover="" focus="">
                                                <ps-icon name="pencil-btn" w="21" h="21" />
                                            </ps-button>
                                            <ps-image-icon-modal ref="ps_image_icon_modal" />
                                            <ps-action-modal ref="ps_action_modal" />
                                            <ps-danger-dialog ref="ps_danger_dialog" />
                                        </div>
                                        <ps-image-upload v-else uploadType="image"
                                            v-model:imageFile="form.product_relation[custom_field_header.core_keys_id]" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- time Only -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00010' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <input type="time" class="w-full rounded"
                                            v-model="form.product_relation[custom_field_header.core_keys_id]" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- date Only -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00011' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div>
                                            <date-picker
                                                v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                                @keyup="handleCustomFieldError(custom_field_header)"
                                                @blur="handleCustomFieldError(custom_field_header)" :inline="false"
                                                :autoApply="false" />
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>
                                </div>
                                <!-- /.custom field end -->

                                <!-- status-->
                                <div class="md:me-6 sm:me-0"
                                    v-for="(coreField, index) in
                                    coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'status' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base mb-1">{{ $t(coreField.label_name) }}</ps-label>
                                    <ps-checkbox-value v-model:value="form.status" class="font-normal"
                                        :title="$t(coreField.placeholder)" />
                                </div>

                                <!-- item photo -->
                                <div>
                                    <ps-label class="text-base">{{ $t('core__be_item_photo') }}<span
                                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                                    <ps-label-title-3 v-if="!item.cover[0]">{{ $t('core__be_recommended_size_400_200')
                                    }}</ps-label-title-3>
                                    <!-- <div v-if="item.cover[0]" class="flex items-end pt-4">
                                        <img
                                        v-lazy=" { src: $page.props.uploadUrl + '/' + item.cover[0].img_path, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                         class="w-96 h-48" :alt="$t(core__be_item_photo)" />
                                        <ps-button type="button" @click="replaceImageClicked(item.cover[0].id,'item')" rounded="rounded-full" shadow="drop-shadow-2xl" class="-ms-10 mb-2" colors="bg-white text-primary-500 dark:bg-secondaryDark-black" border="border border-1 dark:border-secondary-700 border-secondary-300" padding="p-1.5" hover="" focus="">
                                            <ps-icon name="pencil-btn"  w="21" h="21" />
                                        </ps-button>
                                        <ps-image-icon-modal ref="ps_image_icon_modal" />
                                        <ps-action-modal ref="ps_action_modal" />
                                        <ps-danger-dialog ref="ps_danger_dialog" />
                                    </div>
                                    <ps-image-upload v-else uploadType="image" v-model:imageFile="form.cover" /> -->

                                    <Dropzone :dir="$page.props.dir" @clicked="pushImage" @removeImage="removeImage"
                                        @caption="caption" :images="form.item_image" :edit_mode="1" :item_id="item.id"
                                        :max_image_upload="this.systemConfig.max_img_upload_of_item" />
                                    <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ errors.images
                                    }}</ps-label-caption>
                                </div>

                                <div>
                                    <!-- <file-pond
                                    name="imageFilepond"
                                    ref="pond"
                                    v-bind:allow-multiple="true"
                                    accepted-file-types="image/png, image/jpeg"
                                    v-bind:server="{
                                        url: '/admin/item/upload-multi',
                                        timeout: 7000,
                                        process:{
                                            url: '/admin/item/upload-multi',
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': $page.props.csrf
                                            },
                                            withCredentials: false,
                                            onload: handleFilePondLoad,
                                            onerror: () => {}
                                        },
                                    remove: handleFilePondRemove,
                                        revert: handleFilePondRevert
                                    }"
                                    v-bind:files="myFiles"
                                    v-on:init="handleFilePondInit"
                                >
                                </file-pond> -->
                                    <!-- <file-pond



                                    name="imageFilepond"
                                    ref="pond"
                                    v-bind:allow-multiple="true"
                                    accepted-file-types="image/png, image/jpeg"
                                    @processfile="handleFilePondLoad"
                                    @removefile="handleFilePondRemove"
                                    filePosterHeight="200"


                                    v-bind:files="myFiles"
                                    v-on:init="handleFilePondInit"
                                >
                                </file-pond> -->
                                </div>

                                <!-- item video -->
                                <div>
                                    <ps-label class="text-base">{{ $t('core__be_item_video') }}</ps-label>
                                    <ps-label-title-3 v-if="!item.video[0]">{{ $t('core__be_recommended_size_400_200')
                                    }}</ps-label-title-3>
                                    <div v-if="item.video[0]" class="flex items-end pt-4">
                                        <video class="w-96 h-48" controls>
                                            <source :src="$page.props.uploadUrl + '/' + item.video[0].img_path"
                                                type="video/mp4">
                                            <source :src="$page.props.uploadUrl + '/' + item.video[0].img_path"
                                                type="video/ogg">
                                            {{ $t('core__be_item_video') }}
                                        </video>
                                        <ps-button type="button" @click="replaceVideoClicked(item.video[0].id)" rounded="rounded-full"
                                            shadow="drop-shadow-2xl" class="-ms-10 mb-2"
                                            colors="bg-white text-primary-500 dark:bg-secondaryDark-black"
                                            border="border border-1 dark:border-secondary-700 border-secondary-300"
                                            padding="p-1.5" hover="" focus="">
                                            <ps-icon name="pencil-btn" w="21" h="21" />
                                        </ps-button>
                                        <!-- <ps-image-icon-modal ref="ps_image_icon_modal" /> -->
                                        <ps-video-replace-modal ref="ps_video_replace_modal" />
                                        <ps-action-modal ref="ps_action_modal" />
                                        <ps-danger-dialog ref="ps_danger_dialog" />
                                    </div>
                                    <ps-video-upload v-else v-model:videoFile="form.video" />
                                    <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ errors.video
                                    }}</ps-label-caption>
                                </div>

                                <!-- item icon -->
                                <div>
                                    <ps-label class="text-base">{{ $t('core__be_item_icon') }}</ps-label>
                                    <ps-label-title-3 v-if="!item.icon[0]">{{ $t('core__be_recommended_size_200_200')
                                    }}</ps-label-title-3>
                                    <div v-if="item.icon[0]" class="flex items-end pt-4">
                                        <img v-lazy="{ src: $page.props.uploadUrl + '/' + item.icon[0].img_path, loading: $page.props.sysImageUrl + '/loading_gif.gif', error: $page.props.sysImageUrl + '/default_photo.png' }"
                                            class="w-72 h-48" :alt="$t(core__be_item_icon)" />
                                        <ps-button type="button" @click="replaceIconClicked(item.icon[0].id, 'item')"
                                            rounded="rounded-full" shadow="drop-shadow-2xl" class="-ms-10 mb-2"
                                            colors="bg-white text-primary-500 dark:bg-secondaryDark-black"
                                            border="border border-1 dark:border-secondary-700 border-secondary-300"
                                            padding="p-1.5" hover="" focus="">
                                            <ps-icon name="pencil-btn" w="21" h="21" />
                                        </ps-button>
                                        <ps-image-icon-modal ref="ps_image_icon_modal" />
                                        <ps-action-modal ref="ps_action_modal" />
                                        <ps-danger-dialog ref="ps_danger_dialog" />
                                    </div>
                                    <ps-image-upload v-else class="w-72" uploadType="icon"
                                        v-model:imageFile="form.video_icon" />
                                    <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{ errors.video_icon
                                    }}</ps-label-caption>
                                </div>

                            </div>
                            <div class="w-full ms-4">
                                <div class="mb-6">
                                    <ps-label-header-6 textColor="text-secondary-800 dark:text-secondary-100">{{
                                        $t('core__be_item_location') }}</ps-label-header-6>
                                </div>

                                <!-- contact number -->
                                <div class="mb-6"
                                    v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'phone' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                            v-if="coreField.mandatory == 1"
                                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                                    <ps-input ref="phone" type="text" v-model:value="form.phone"
                                        :placeholder="$t(coreField.placeholder)" />
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.phone }}</ps-label-caption>
                                </div>

                                <!-- custom field start -->
                                <div class="mb-6"
                                    v-for="(custom_field_header) in customizeHeaders.filter((custom) => custom.core_keys_id == 'ps-itm00009')"
                                    :key="custom_field_header.id">
                                    <!-- dropdown-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00001' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory == 1">*</span></ps-label>
                                        <ps-dropdown align="left" class="lg:mt-2 mt-1 w-full">
                                            <template #select>
                                                <ps-dropdown-select ref="detail"
                                                    :placeholder="$t(custom_field_header.placeholder)" :showCenter="true"
                                                    :selectedValue="customizeDetails.filter((detail) => detail.id ==
                                                        form.product_relation[custom_field_header.core_keys_id]).length > 0 ?
                                                        customizeDetails.filter((detail) => detail.id == (form.product_relation[custom_field_header.core_keys_id]))[0].name : ''"
                                                    @change="handleCustomFieldError(custom_field_header)"
                                                    @blur="handleCustomFieldError(custom_field_header)" />
                                            </template>
                                            <template #list>
                                                <div
                                                    class="rounded-md shadow-xs w-full bg-background dark:bg-backgroundDark">
                                                    <div class="pt-2 z-30">
                                                        <div
                                                            v-if="customizeDetails.filter((customizeDetail) => customizeDetail.core_keys_id === custom_field_header.core_keys_id).length === 0">
                                                            <ps-label class="p-2 flex" @click="route('currency.index')">{{
                                                                $t('core__be_create_new') }}
                                                                {{ $t(custom_field_header.name) }}</ps-label>
                                                        </div>
                                                        <div v-else>
                                                            <div v-for="detail in customizeDetails.filter((customizeDetail) => customizeDetail.core_keys_id === custom_field_header.core_keys_id)"
                                                                :key="detail.id"
                                                                class="w-96 flex py-4 px-2 hover:bg-primary-000 dark:hover:bg-secondary-700 cursor-pointer items-center"
                                                                @click="[(form.product_relation[custom_field_header.core_keys_id] = detail.id), validateEmptyInput('currency_id', form.product_relation[custom_field_header.core_keys_id])]">
                                                                <ps-label class="ms-2"
                                                                    :class="detail.id == form.product_relation[custom_field_header.core_keys_id] ? ' font-bold' : ''">{{
                                                                        detail.name }}</ps-label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </ps-dropdown>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- text-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00002' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label>{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <ps-input type="text" class="w-full rounded"
                                            :placeholder="$t(custom_field_header.placeholder)"
                                            v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                            @keyup="handleCustomFieldError(custom_field_header)"
                                            @blur="handleCustomFieldError(custom_field_header)" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- radio-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00003' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div class="flex flex-row">
                                            <div class="me-2"
                                                v-for="detail in customizeDetails.filter((customizeDetail) => customizeDetail.core_keys_id === custom_field_header.core_keys_id)"
                                                :key="detail.id">
                                                <ps-radio-value color="text-purple-600 border-purple-300"
                                                    v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                                    :title="detail.id.toString()" :title_label="detail.name" />
                                                <ps-label :for="detail.id">{{ detail.attribute }}</ps-label>
                                            </div>
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- checkbox-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00004' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}</ps-label>
                                        <div class="flex flex-row">
                                            <div class="me-2 flex">
                                                <input type="checkbox" class="rounded border mt-1.5" value="0"
                                                    v-model="form.product_relation[custom_field_header.core_keys_id]"
                                                    @change="handleCustomFieldError(custom_field_header)" />
                                                <ps-label class="ms-2">{{ $t(custom_field_header.placeholder) }}</ps-label>
                                            </div>
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- datetime -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00005' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div>
                                            <date-picker
                                                v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                                @keyup="handleCustomFieldError(custom_field_header)"
                                                @blur="handleCustomFieldError(custom_field_header)" :enableTimePicker="true"
                                                :inline="false" :autoApply="false" />
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- textarea -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00006' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <ps-textarea rows="4" :placeholder="$t(custom_field_header.placeholder)"
                                            v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                            @keyup="handleCustomFieldError(custom_field_header)"
                                            @blur="handleCustomFieldError(custom_field_header)" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- number-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00007' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <ps-input type="number" class="w-full rounded"
                                            :placeholder="$t(custom_field_header.placeholder)"
                                            v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                            @keyup="handleCustomFieldError(custom_field_header)"
                                            @blur="handleCustomFieldError(custom_field_header)" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- multi Select-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00008' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div class="flex flex-row">
                                            <CheckBox :oldData="form.product_relation[custom_field_header.id]"
                                                @toParent="handleMultiSelect" :customizeDetails="customizeDetails"
                                                :customizeHeader="custom_field_header" />
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- Image-->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00009' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div v-if="item.image" class="flex items-end pt-4">
                                            <img v-lazy="{ src: $page.props.uploadUrl + '/' + item.image.img_path, loading: $page.props.sysImageUrl + '/loading_gif.gif', error: $page.props.sysImageUrl + '/default_photo.png' }"
                                                class="w-96 h-48" :alt="$t(core__be_item_photo)" />
                                            <ps-button type="button" @click="replaceImageClicked(item.image.id, 'item')"
                                                rounded="rounded-full" shadow="drop-shadow-2xl" class="-ms-10 mb-2"
                                                colors="bg-white text-primary-500 dark:bg-secondaryDark-black"
                                                border="border border-1 dark:border-secondary-700 border-secondary-300"
                                                padding="p-1.5" hover="" focus="">
                                                <ps-icon name="pencil-btn" w="21" h="21" />
                                            </ps-button>
                                            <ps-image-icon-modal ref="ps_image_icon_modal" />
                                            <ps-action-modal ref="ps_action_modal" />
                                            <ps-danger-dialog ref="ps_danger_dialog" />
                                        </div>
                                        <ps-image-upload v-else uploadType="image"
                                            v-model:imageFile="form.product_relation[custom_field_header.core_keys_id]" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- time Only -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00010' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <input type="time" class="w-full rounded"
                                            v-model="form.product_relation[custom_field_header.core_keys_id]" />
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>

                                    <!-- date Only -->
                                    <div class="md:me-6 sm:me-0"
                                        v-if="custom_field_header.ui_type_id === 'uit00011' && custom_field_header.enable === 1 && custom_field_header.is_delete === 0">
                                        <ps-label class="text-base">{{ $t(custom_field_header.name) }}<span
                                                class="text-red-800 font-medium ms-1"
                                                v-show="custom_field_header.mandatory === 1">*</span></ps-label>
                                        <div>
                                            <date-picker
                                                v-model:value="form.product_relation[custom_field_header.core_keys_id]"
                                                @keyup="handleCustomFieldError(custom_field_header)"
                                                @blur="handleCustomFieldError(custom_field_header)" :inline="false"
                                                :autoApply="false" />
                                        </div>
                                        <ps-label-caption textColor="text-red-500 " class="mt-2 block">{{
                                            product_relation_errors &&
                                            product_relation_errors[custom_field_header.core_keys_id] }}</ps-label-caption>
                                    </div>
                                </div>
                                <!-- /.custom field end -->

                                <!-- maps start -->
                                <div class="mb-6" v-if="backendSettings.is_google_map == 1">
                                    <google-map-pin-picker :mapKey="$page.props.mapKey" :lat="parseFloat(form.lat)"
                                        :lng="parseFloat(form.lng)" widthHeight="width: 458px; height: 500px"
                                        :onChange="updateCoordinates" />
                                </div>
                                <div class="mb-6" v-else>
                                    <open-street-map-pin-picker :dir="$page.props.dir" :onChange="setCoordinates"
                                        :lat="parseFloat(form.lat)" :lng="parseFloat(form.lng)"
                                        class="h-[500px]"></open-street-map-pin-picker>
                                </div>
                                <!-- maps end -->

                                <div class="mb-6"
                                    v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'lat' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                            v-if="coreField.mandatory == 1"
                                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                                    <ps-input type="text" v-model:value="form.lat" :placeholder="$t(coreField.placeholder)"
                                        @keypress="onlyNumberWithCustom"
                                        @keyup="coreField.mandatory == 1 ? validateLatitudeInput('lat', form.lat) : ''"
                                        @blur="coreField.mandatory == 1 ? validateLatitudeInput('lat', form.lat) : ''" />
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.lat }}</ps-label-caption>
                                </div>

                                <div class="mb-6"
                                    v-for="(coreField, index) in coreFieldFilterSettings.filter((coreField) => coreField.original_field_name === 'lng' && coreField.enable === 1 && coreField.is_delete === 0)"
                                    :key="index">
                                    <ps-label class="text-base">{{ $t(coreField.label_name) }}<span
                                            v-if="coreField.mandatory == 1"
                                            class="text-red-800 font-medium ms-1">*</span></ps-label>
                                    <ps-input type="text" v-model:value="form.lng" :placeholder="$t(coreField.placeholder)"
                                        @keypress="onlyNumberWithCustom"
                                        @keyup="coreField.mandatory == 1 ? validateLongitudeInput('lng', form.lng) : ''"
                                        @blur="coreField.mandatory == 1 ? validateLongitudeInput('lng', form.lng) : ''" />
                                    <ps-label-caption v-if="coreField.mandatory == 1" textColor="text-red-500 "
                                        class="mt-2 block">{{ errors.lng }}</ps-label-caption>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-row justify-end mb-2.5">
                            <ps-button @click="handleCancel()" textSize="text-base" type="reset" class="me-4"
                                colors="text-primary-500" focus="" hover="">{{ $t("core__be_btn_cancel") }}</ps-button>
                            <ps-button class="transition-all duration-300 min-w-3xs" padding="px-7 py-2" rounded="rounded"
                                hover="" focus="">
                                <ps-loading v-if="loading" theme="border-2 border-t-2 border-text-8 border-t-primary-500"
                                    loadingSize="h-5 w-5" />
                                <ps-icon v-if="success" name="check" class="me-1.5 transition-all duration-300" />
                                <span v-if="success" class="transition-all duration-300">{{ $t("core__be_btn_saved")
                                }}</span>
                                <span v-if="!loading && !success" class="">{{ $t("core__be_btn_save") }}</span>
                            </ps-button>
                            <ps-button v-if="paidItemProgressStatus != item.paid_status" type="button"
                                @click="handlePromote(item.id)" class="ms-3 transition-all duration-300 min-w-3xs"
                                padding="px-7 py-2" rounded="rounded" hover="" focus="">
                                <span class="">{{ $t("core__be_btn_promote") }}</span>
                            </ps-button>
                        </div>
                    </form>
                </div>
                <!-- card body end -->
            </div>
        </ps-card>
    </ps-layout>
</template>

<script>
import axios from 'axios';
import { defineComponent, ref, defineAsyncComponent } from 'vue'
import PsLayout from "@/Components/PsLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import CheckBox from "../components/CheckBox.vue";
import PsRadioValue from "@/Components/Core/Radio/PsRadioValue.vue";
import DatePicker from "@/Components/Core/DateTime/DatePicker.vue";
import useValidators from '@/Validation/Validators'
import PsInput from "@/Components/Core/Input/PsInput.vue";
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsButton from "@/Components/Core/Buttons/PsButton.vue";
import PsTextarea from '@/Components/Core/Textarea/PsTextarea.vue';
import PsLabelHeader6 from "@/Components/Core/Label/PsLabelHeader6.vue";
import PsBreadcrumb2 from "@/Components/Core/Breadcrumbs/PsBreadcrumb2.vue";
import PsDropdown from "@/Components/Core/Dropdown/PsDropdown.vue";
import PsDropdownSelect from "@/Components/Core/Dropdown/PsDropdownSelect.vue";
import PsCard from "@/Components/Core/Card/PsCard.vue";
import PsLabelCaption from "@/Components/Core/Label/PsLabelCaption.vue";
import PsVideoUpload from "@/Components/Core/Upload/PsVideoUpload.vue";
import PsLabelTitle3 from "@/Components/Core/Label/PsLabelTitle3.vue";
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsLoading from "@/Components/Core/Loading/PsLoading.vue";
import PsCheckboxValue from "@/Components/Core/Checkbox/PsCheckboxValue.vue";
import PsActionModal from '@/Components/Core/Modals/PsActionModal.vue';
import PsImageIconModal from '@/Components/Core/Modals/PsImageIconModal.vue';
import PsVideoReplaceModal from '@/Components/Core/Modals/PsVideoReplaceModal.vue';
import PsDangerDialog from "@/Components/Core/Dialog/PsDangerDialog.vue";
import PsImageUpload from "@/Components/Core/Upload/PsImageUpload.vue";
const GoogleMapPinPicker = defineAsyncComponent(() => import('@/Components/Core/Map/GoogleMapPinPicker.vue'));
import { trans } from 'laravel-vue-i18n';
import Dropzone from "@/Components/Core/Dropzone/Dropzone.vue";
import PsTooltip from '@/Components/Core/Tooltips/PsTooltip.vue';
import PsConst from '@templateCore/object/constant/ps_constants';


import vueFilePond, { setOptions } from "vue-filepond";

import "filepond/dist/filepond.min.css";
// Import FilePond plugins
// Please note that you need to install these plugins separately
// Import image preview plugin styles
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
// Import the plugin code
import FilePondPluginFilePoster from 'filepond-plugin-file-poster';
// Import the plugin styles
import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css';
// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginFilePoster
);


import OpenStreetMapPinPicker from '../../../../../../resources/js/Components/Core/Map/OpenStreetMapPinPicker.vue';
const OpenStreetMap = defineAsyncComponent(() => import('@/Components/Core/Map/OpenStreetMapPinPicker.vue'));


export default defineComponent({
    name: "Edit",
    components: {
        Head,
        CheckBox,
        DatePicker,
        PsInput,
        PsRadioValue,
        PsLabel,
        PsButton,
        PsTextarea,
        PsLabelHeader6,
        PsDropdown,
        PsDropdownSelect,
        PsCard,
        PsBreadcrumb2,
        PsLabelCaption,
        PsImageUpload,
        GoogleMapPinPicker,
        OpenStreetMap,
        PsIcon,
        PsLoading,
        PsCheckboxValue,
        PsActionModal,
        PsImageIconModal,
        PsVideoReplaceModal,
        PsDangerDialog,
        PsVideoUpload,
        PsLabelTitle3,
        FilePond,
        Dropzone,
        OpenStreetMapPinPicker,
        PsTooltip
    },
    layout: PsLayout,
    props: [
        'vendorSetting',
        'authUser',
        'vendor_list',
        'errors',
        'coreFieldFilterSettings',
        'currencies',
        'categories',
        'subcategories',
        'townships',
        'cities',
        'shops',
        'item',
        'customizeHeaders',
        'customizeDetails',
        'paidItemProgressStatus',
        'backendSettings',
        'validation',
        'systemConfig',
        "selected_price_type",
        "item_price_range",
    ],
    data() {
        return {
            location_city: {},
            category: {},
            // myFiles:[],
        }
    },
    setup(props) {
        const loading = ref(false);
        const success = ref(false);
        const title = ref();
        const category_id = ref();
        const currency_id = ref();
        const location_city_id = ref();
        const location_township_id = ref();
        const price = ref();
        const percent = ref();
        const ps_action_modal = ref();
        const ps_image_icon_modal = ref();
        const ps_video_replace_modal = ref();
        const ps_danger_dialog = ref();
        let myFiles = ref([]);

        function checkPriceFormat(data) {
            // alert(data);
            if (props.selected_price_type == PsConst.PRICE_RANGE) {

                const floatValue = parseFloat(data);
                const intValue = parseInt(floatValue);
                if (intValue > 5) {
                    return 5
                }
                if (intValue < 1) {
                    return 1
                }
                return intValue;
            }
            if (props.selected_price_type == PsConst.NORMAL_PRICE) {
                return data;
            }
            if (props.selected_price_type == PsConst.NO_PRICE) {
                return data;
            }
        }

        function checkPercent(data) {

            if (props.selected_price_type == PsConst.PRICE_RANGE || props.selected_price_type == PsConst.NO_PRICE ) {
                return 0;
            }
            if (props.selected_price_type == PsConst.NORMAL_PRICE){

                return data;
            }
        }


        let form = useForm(
            {
                id: props.item.id,
                title: props.item.title,
                original_price: checkPriceFormat(props.item.original_price),
                price: props.item.price,
                percent: checkPercent(props.item.percent),
                // location_city_id: props.item.location_city_id,
                // location_township_id: props.item.location_township_id,
                // currency_id: props.item.currency_id,
                // category_id: props.item.category_id,
                // subcategory_id: props.item.subcategory_id,
                location_city_id: props.cities.find(element => element.id == props.item.location_city_id) ? props.item.location_city_id : '',
                location_township_id: props.townships.find(element => element.id == props.item.location_township_id) ? props.item.location_township_id : '',
                currency_id: props.currencies.find(element => element.id == props.item.currency_id) ? props.item.currency_id : '',
                category_id: props.categories.find(element => element.id == props.item.category_id) ? props.item.category_id : '',
                subcategory_id: props.subcategories.find(element => element.id == props.item.subcategory_id) ? props.item.subcategory_id : '',
                shop_id: props.item.shop_id,
                lat: props.item.lat,
                lng: props.item.lng,
                ordering: props.item.ordering,
                description: props.item.description,
                search_tag: props.item.search_tag,
                phone: props.item.phone,
                status: props.item.status == 1 ? true : false,
                cover: '',
                video: '',
                video_icon: '',
                is_available: true,
                is_discount: false,
                product_relation: {},
                item_image: props.item.cover,
                images: [],
                extra_caption: [],
                "_method": "put"
            }
        )

        // Client Side Validation
        const { isEmpty, isPrice, isNum } = useValidators();

        const validateEmptyInput = (fieldName, fieldValue, errorMessage = '') => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue, errorMessage) : '';
            // if(fieldName == 'title'){
            //     title.value.isError = (props.errors.title == '') ? false: true;
            // }
            // if(fieldName == 'category_id'){
            //     category_id.value.isError = (props.errors.category_id == '') ? false: true;
            // }
            // if(fieldName == 'currency_id'){
            //     currency_id.value.isError = (props.errors.currency_id == '') ? false: true;
            // }
            // if(fieldName == 'location_city_id'){
            //     location_city_id.value.isError = (props.errors.location_city_id == '') ? false: true;
            // }
            // if(fieldName == 'location_township_id'){
            //     location_township_id.value.isError = (props.errors.location_township_id == '') ? false: true;
            // }
        }

        const validateNumberInput = (fieldName, fieldValue, errorMessage1 = '', errorMessage2 = '') => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue, errorMessage1) : isNum(fieldName, fieldValue, errorMessage2);
        }

        const validatePriceInput = (fieldName, fieldValue) => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : isPrice(fieldName, fieldValue);

            // if(fieldName == 'price'){
            //     price.value.isError = (props.errors.price == '') ? false: true;
            // }
            // if(fieldName == 'original_price'){
            //     original_price.value.isError = (props.errors.original_price == '') ? false: true;
            // }
        }

        // for custom number ps-input validate at keypress
        const onlyNumberWithCustom = ($e) => {
            let keyCode = ($e.keyCode ? $e.keyCode : $e.which);
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46 && keyCode !== 45 && keyCode !== 44) { // 46 is dot, 45 is dash, 44 is comma
                $e.preventDefault();
            }
        }

        const validateLatitudeInput = (fieldName, fieldValue) => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : isLatitude(fieldName, fieldValue);
            // if(fieldName == 'lat'){
            //     lat.value.isError = (props.errors.lat == '') ? false: true;
            // }
        }

        const validateLongitudeInput = (fieldName, fieldValue) => {
            props.errors[fieldName] = !fieldValue ? isEmpty(fieldName, fieldValue) : isLongitude(fieldName, fieldValue);
            // if(fieldName == 'lng'){
            //     lng.value.isError = (props.errors.lng == '') ? false: true;
            // }
        }

        function setCoordinates(location) {
            form.lat = location.lat;
            form.lng = location.lng;
        }

        function handleSubmit(id) {
            this.$inertia.post(route('item.update', id), form, {
                forceFormData: true,
                onBefore: () => { loading.value = true },
                onSuccess: () => {
                    loading.value = false;
                    success.value = true;
                    setTimeout(() => {
                        success.value = false;
                    }, 2000)
                },
                onError: () => {
                    // title.value.isError = (props.errors.title == '') ? false: true;
                    // category_id.value.isError = (props.errors.category_id == '') ? false: true;
                    // currency_id.value.isError = (props.errors.currency_id == '') ? false: true;
                    // location_city_id.value.isError = (props.errors.location_city_id == '') ? false: true;
                    // location_township_id.value.isError = (props.errors.location_township_id == '') ? false: true;
                    // price.value.isError = (props.errors.price == '') ? false: true;
                    loading.value = false;
                },
            });
        }

        function updateCoordinates(location) {
            form.lat = location.latLng.lat();
            form.lng = location.latLng.lng();
        }

        function replaceImageClicked(id, uploadType = null) {
            ps_action_modal.value.openModal(trans('conf_modal_label'),
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
                                uploadType: uploadType,
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

        function replaceIconClicked(id, uploadType = null) {
            ps_action_modal.value.openModal(trans('conf_modal_label'),
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
                                uploadType: uploadType,
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

        function replaceVideoClicked(id) {
            ps_action_modal.value.openModal(trans('conf_modal_label'),
                trans('core__be_replace_video_label'),
                trans('core__be_remove_video_label'),
                'videoCamera',
                'trash',
                '24',
                '24',
                () => {
                    ps_video_replace_modal.value.openModal(
                        trans('core__be_upload_video'),
                        'cloudUpload',
                        (imageFile) => {
                            let imageForm = useForm({
                                video: imageFile,
                                "_method": "put"
                            })

                            this.$inertia.post(route("video.replace", id), imageForm);
                        });
                },
                () => {
                    ps_danger_dialog.value.openModal(
                        trans('core__be_remove_label'),
                        trans('core__be_are_u_sure_remove_video'),
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
                !props.validation.includes('video')
            );
        }

        function handleCustomFieldError(custom_field_header) {
            custom_field_header.mandatory === 1 ? validateEmptyInput(custom_field_header.name, form.product_relation[custom_field_header.core_keys_id]) : ''
        }

        function handleCustomFieldNumberError(custom_field_header) {
            custom_field_header.mandatory === 1 ? validateNumberInput(custom_field_header.name, form.product_relation[custom_field_header.core_keys_id], 'The ' + custom_field_header.name.toLowerCase() + ' field is required.', 'The ' + custom_field_header.name.toLowerCase() + ' field only have number.') : ''
        }

        const handleUnitPrice = () => {
            let unit_price = form.original_price
            if (form.percent > 0) {
                unit_price = unit_price - (unit_price * (form.percent / 100))
            }
            form.price = unit_price
        }
        // alert(usePage().props.uploadUrl);

        function handleFilePondInit() {
            setOptions({
                credits: false,
                server: {
                    url: '',
                    process: {
                        url: '/admin/item/upload-multi',
                        method: 'POST',
                        withCredentials: false,
                        headers: {
                            'X-CSRF-TOKEN': usePage().props.csrf,
                        },
                        timeout: 7000,
                        onload: null,
                        onerror: null,
                        ondata: null,
                    },

                }
            });
            if (form.item_image) {
                myFiles.value = [];
                let arr = form.item_image ? form.item_image.split('|') : [];

                for (let i = 0; i < arr.length; i++) {

                    myFiles.value.push({
                        source: usePage().props.uploadUrl + '/' + arr[i],
                        options: {
                            type: 'local',
                            metadata: {
                                poster: usePage().props.uploadUrl + '/' + arr[i],

                            },


                        }
                    })
                }

            }
            else {
                myFiles.value = [];
            }
            // FilePond instance methods are available on `this.$refs.pond`
        }

        const handleFilePondLoad = (error, file) => {
            // console.log(file.filename);
            // form.item_image = file.serverId;

            addFormImage(file.serverId);
            return file.serverId;
        };

        function addFormImage(image) {
            let arr = form.item_image ? form.item_image.split('|') : [];
            arr.push(image);
            form.item_image = arr.join('|');
            // console.log(form.item_image);
        }

        function removeFormImage(image) {

            let arr = form.item_image ? form.item_image.split('|') : [];
            arr.remove(image);
            form.item_image = arr.join('|');
            // console.log(form.item_image);
        }

        function handleFilePondRemove(error, file) {
            // axios.post('/admin/item/remove-multi',{
            //     image:file.serverId
            // });
            let filename = file.serverId.split("/").pop();

            // alert(filename);
            // console.log(filename);
            removeFormImage(filename);
            // form.gallery.push({id: file.id, serverId: file.serverId});
        };

        return {
            handleUnitPrice,
            replaceVideoClicked,
            validateLatitudeInput,
            validateLongitudeInput,
            handleCustomFieldNumberError,
            handleCustomFieldError,
            validateEmptyInput,
            validateNumberInput,
            validatePriceInput,
            onlyNumberWithCustom,
            handleSubmit,
            replaceIconClicked,
            success,
            loading,
            title,
            category_id,
            currency_id,
            location_city_id,
            location_township_id,
            price,
            percent,
            form,
            updateCoordinates,
            replaceImageClicked,
            ps_action_modal,
            ps_image_icon_modal,
            ps_video_replace_modal,
            ps_danger_dialog,
            myFiles,
            handleFilePondInit,
            handleFilePondLoad,
            handleFilePondRemove,
            setCoordinates,
            PsConst
        }
    },
    created() {
        this.customizeHeaders.map((customizeHeader, index) => {
            this.item.item_relation.map((value) => {
                if (customizeHeader.core_keys_id === value.core_keys_id) {
                    let data = (value.value);
                    this.form.product_relation[customizeHeader.core_keys_id] = data === '1' ? true : (data === '0' ? false : data)
                }
            });
        })
    },
    computed: {
        breadcrumb() {
            return [
                {
                    label: trans('core__be_dashboard_label'),
                    url: route('admin.index')
                },
                {
                    label: trans('item_module'),
                    url: route('item.index'),
                },
                {
                    label: trans('core__be_edit_item_lbl'),
                    color: "text-primary-500"
                }
            ]
        }
    },
    methods: {
        handleMultiSelect({ data, id }) {
            this.form.product_relation[id] = data.toString();
        },
        toLocationTownship() {
            this.$inertia.get(route('township.create'));
        },
        handleCancel() {
            this.$inertia.get(route('item.index'));
        },
        handlePromote(id) {
            this.$inertia.get(route('item.promote', id));
        },
        removeImage(value) {

            if (value != undefined || value != null) {
                var index = this.form.images.indexOf(value);
                this.form.images.splice(index, 1);

                // this.$inertia.post(route("item.removeMulti"), {
                //     image:value,
                //     edit_mode:0,
                // }, {

                // });



                axios.post(route('item.removeMulti'), {
                    image: value,
                    edit_mode: 1,
                    img_parent_id: this.item.id,
                })
                    .then(response => { }).catch(error => { })
            }
        },
        pushImage(value) {
            // console.log(value);
            this.form.images.push(value)
            // console.log(this.files)
        },
        caption(value) {

            let flag = false;

            this.form.extra_caption.forEach(el => {
                if (el.name == value.name) {
                    el.value = value.value;

                    flag = true;

                    return false;
                }
            })

            if (!flag) {
                this.form.extra_caption.push({
                    name: value.name,
                    value: value.value,
                });
            }

            // var name = value.name;
            // var value1 = value.value;
            // this.form.extra_caption.push({name:value.name , value:value.value});

            // var obj = {name:value.name , value:value.value};


            // if(this.form.extra_caption.includes(value.name)){
            //     alert(true);
            // }

            //     if(this.form.extra_caption.indexOf(obj.name) === -1){
            //         this.form.extra_caption.push(obj);
            //     }

            //     this.form.extra_caption= this.form.extra_caption.filter((o, i) =>
            //         this.form.extra_caption.findIndex(obj => obj.name == o.name) == i
            //     );




            // console.log(this.form.extra_caption);
        },
        // handleFilePondInit() {



        //   setOptions({
        //         credits: false,
        //         server: {
        //             url: '/admin/item/upload-multi',

        //             headers: {
        //                 'X-CSRF-TOKEN':  usePage().props.csrf,
        //             }
        //         }
        //     });
        //     if(this.form.item_image)
        //     {
        //         this.myFiles=[{
        //             source: usePage().props.uploadUrl +'/'+this.form.item_image,
        //             // options: {"type":"local"}
        //             // options:{
        //             //     type:'local',
        //             //     metadata:{
        //             //         poster: usePage().props.uploadUrl + '/'+this.form.item_image
        //             //     }
        //             // }
        //         }]
        //     }

        //     else{
        //         this.myFiles=[];
        //     }


        //     // FilePond instance methods are available on `this.$refs.pond`
        //     },

        // handleFilePondLoad(response){

        //     //form.item_image.push(response);

        //     // console.log(response);
        //     this.form.item_image = response;
        // }
    },
})
Array.prototype.remove = function () {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
</script>
