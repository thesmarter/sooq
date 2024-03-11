<template>
    <div class='flex flex-col justify-center items-center sm:mt-32 lg:mt-36 mt-28 w-mobile sm:w-median lg:w-large mx-auto'>
        <!-- Item entry title -->
        <div class="mb-10">
            <ps-label v-if="itemId == '0'" class="font-semibold text-xl lg:text-3xl">{{
                $t("core__fe_item_entry__listing_entry") }}
            </ps-label>
            <ps-label v-else class="font-semibold text-xl lg:text-3xl">{{ $t("core__fe_item_entry__update_entry") }}
            </ps-label>
        </div>
        <!-- End Item entry title -->
    </div>

    <div class='flex flex-col py-6 ps-5 pe-6 sm:ps-16 md:ps-8 sm:pe-6 md:pe-8 lg:pe-7 lg:ps-8 w-mobile sm:w-median lg:w-large mx-auto border-2 rounded-md mb-6' v-if="appInfoStore?.isVendorSettingOn() && customFieldStore.customField.data?.vendorList.length > 0">
        <ps-label class="text-base " :textColor="itemId == '0' ? 'text-feSecondary-800 dark:text-feSecondary-300' : 'text-feSecondary-300'" >{{ $t('core_fe__upload_as_vendor') }}</ps-label>
        <div class=" mb-4" >
            <ps-dropdown align="left" :disabled="itemId != '0'" class='lg:mt-2 mt-1  w-full' >
                <template #select>
                    <button :disabled="itemId != '0'"   class="w-full cursor-pointer flex flex-row justify-between px-4 py-1.5 items-center rounded-md border border-feSecondary-200">
                        <div v-if="defaultProfileId == ''" class="">
                            <ps-label class="text-sm  ms-2" :textColor="itemId == '0' ? 'text-feSecondary-800 dark:text-feSecondary-300' : 'text-feSecondary-300'" > {{ $t('core_fe_select_profile') }} </ps-label>
                        </div>
                        <div v-else-if="defaultProfileType == 'user'" class="flex flex-row">
                            <img v-lazy=" { src: $page.props.thumb1xUrl + '/' + userStore.user?.data?.userCoverPhoto, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_profile.png' }"
                                        class="w-6 h-6  rounded-full "  width="12px" height="12px" />
                            <ps-label class="text-sm  ms-2" :textColor="itemId == '0' ? 'text-feSecondary-800 dark:text-feSecondary-300' : 'text-feSecondary-300'" > {{ paramHolder1.vendorName }} </ps-label>
                        </div>
                        <div v-else-if="defaultProfileType == 'vendor'" class="flex flex-row">
                            <img v-lazy=" { src: $page.props.thumb1xUrl + '/' + customFieldStore.customField.data?.vendorList.filter((vendor) => vendor.id == defaultProfileId)[0]?.logo.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_vendor_shop.png' }"
                                        class="w-6 h-6  rounded-full"  width="12px" height="12px" />
                            <ps-label class="text-sm  ms-2" :textColor="itemId == '0' ? 'text-feSecondary-800 dark:text-feSecondary-300' : 'text-feSecondary-300'" > {{ paramHolder1.vendorName }} </ps-label>
                        </div>

                        <ps-icon name="downChervon" />

                    </button>


                    <!-- <ps-dropdown-select :showCenter="true" :disabled="itemId != '0'" :selectedValue="paramHolder1.vendorName" :placeholder="$t('core_fe_select_vendor')"/> -->

                </template>
                <template #list>
                    <div class="rounded-md shadow-xs w-80 lg:w-96">
                        <div class="pt-2 z-30">
                            <div >
                                <div
                                    class="w-full flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                    @click="chooseProfile(userStore.user?.data?.userId, userStore.user?.data?.userName, 'user')">
                                    <img
                                    v-lazy=" { src: $page.props.thumb1xUrl + '/' + userStore.user?.data?.userCoverPhoto, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_profile.png' }"
                                        class="w-8 h-8  rounded-full ms-2"  width="12px" height="12px" />
                                    <ps-label class="ms-2"
                                        :class="userStore.user?.data?.userName == paramHolder1.vendorName ? ' font-bold' : ''">
                                        {{ userStore.user?.data?.userName }} </ps-label>
                                </div>
                                <div v-for="vendor in customFieldStore.customField.data?.vendorList"
                                    :key="vendor.id"
                                    class="w-full flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                    @click="chooseProfile(vendor.id, vendor.name, 'vendor')">

                                    <img
                                    v-lazy=" { src: $page.props.thumb1xUrl + '/' + vendor?.logo?.imgPath, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_vendor_shop.png' }"
                                        class="w-8 h-8 rounded-full ms-2" width="12px" height="12px" />
                                    <ps-label class="ms-2"
                                        :class="vendor.id == paramHolder1.vendorId ? ' font-bold' : ''">
                                        {{ vendor.name }} </ps-label>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </ps-dropdown>
        </div>
        <div class="mt-1">
            <div class="flex flex-row">
                <div class="me-2 flex items-center">
                    <input :disabled="itemId != '0'" type="checkbox" class="rounded border" value="0"
                        v-model="vendorAutoChoose" :class="itemId == '0' ? 'text-fePrimary-500' : 'text-feSecondary-300'"/>
                    <ps-label class="ms-2" :textColor="itemId == '0' ? 'text-feSecondary-800 dark:text-feSecondary-300' : 'text-feSecondary-300'" >{{ $t('core_fe__always_choose_this_profile') }}</ps-label>
                </div>
            </div>
        </div>
    </div>

    <div class='flex flex-col w-mobile sm:w-median lg:w-large mx-auto border-2 rounded-md mb-6' v-if="itemId == '0' || dataReady == true">

        <div class="w-full grid  grid-cols-12 gap-4 sm:gap-3.5 lg:gap-4 ">
            <div v-if="itemId == '0'" class="col-span-12  mt-8 lg:px-8  md:px-8 md:col-start-1 sm:col-start-2 px-6 w-full">
                <!-- for category dropdown -->
                <div class="w-full me-6 sm:me-2 mb-4 sm:mb-0" @click="showDialog"
                    v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'category_id' && coreField.isVisible === '1')"
                    :key="index">
                    <ps-label class="text-base">{{ $t(coreField.labelName) }}<span v-if="coreField.mandatory = 1"
                        class="text-feError-800 font-medium ms-1">*</span></ps-label>
                    <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full' @onClick="loadCategory">
                        <template #select>
                            <ps-dropdown-select :showCenter="true" :selectedValue="paramHolder1.catName" :placeholder="categoryName"/>
                         </template>
                    </ps-dropdown>
                </div>
                <!-- end category -->
            </div>
            <div v-else class="col-span-12  mt-8 lg:px-8  md:px-8 md:col-start-1 sm:col-start-2 px-6 w-full">
                <!-- for category dropdown -->
                <div class="w-full me-6 sm:me-2 mb-4 sm:mb-0"
                    v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'category_id' && coreField.isVisible === '1')"
                    :key="index">
                    <ps-label class="text-base">{{ $t(coreField.labelName) }}<span v-if="coreField.mandatory = 1"
                        class="text-feError-800 font-medium ms-1">*</span></ps-label>
                    <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full' :disabled="true">
                        <template #select>
                            <ps-dropdown-select :disabled="true" :showCenter="true" :selectedValue="paramHolder1.catName" :placeholder="categoryName"/>
                         </template>
                    </ps-dropdown>
                </div>
                <!-- end category -->
            </div>
        </div>

        <!-- Start Choose Photo and Video -->
        <div class="w-full grid lg:grid-cols-12 sm:grid-cols-8 grid-cols-4  gap-4 sm:gap-3.5 lg:gap-4 mt-2">

            <!-- First Screen for photo -->
            <div class="col-span-12 mt-4 lg:px-8  md:px-8 md:col-start-1 sm:col-start-2 px-6 w-full">
                <!-- Start Select Item Photo Horizontal -->
                <div class="flex flex-auto">
                    <ps-label class="font-medium text-sm lg:text-base"> {{ $t("item_entry__photo_title") }} </ps-label>
                    <span style="font-size: 17px; color: red;">*</span>
                </div>
                <!-- End Select Item Photo Horizontal -->
            </div>
            <div class=" col-span-12 mt-4 lg:px-8  md:px-8 md:col-start-1 sm:col-start-2 px-6 w-full" v-if="galleryLoad">

                <Dropzone :dir="$page.props.dir" @clicked="pushImage" @removeImage="removeImage" @caption="caption" @maxfilesexceeded="maxUpload"
                    ref="dropzone_ref" @queue-complete="queueComplete" @file-length="getFileCount"
                    :images="itemId == 0 ? '' : gallery.data" :edit_mode="itemId == 0 ? 0 : 1"
                    :item_id="itemId == 0 ? 0 : itemId"
                    :max_image_upload="appInfoStore?.appInfo?.data?.psAppSetting?.maxImgUploadOfItem"
                    :autoProcessQueue="autoProcessQueue" @newOrder="imageOrder" />

                <ps-label class="lg:mt-2 mt-1  w-full text-xs " textColor="text-feError-500" v-if="validation.imageStatus"> {{
                    $t("item_entry__field_is_reqiured",{'attribute':'Image'}) }} </ps-label>
                <ps-label class="lg:mt-2 mt-1  w-full text-xs " textColor="text-feError-500" v-if="validation.maxImgExceed"> {{
                    $t("item_entry_image_upload_exceed") + ' '
                    + appInfoStore?.appInfo?.data?.psAppSetting?.maxImgUploadOfItem }} </ps-label>

            </div>


            <!-- End First Screen for photo -->
            <!-- Start for video -->
            <div v-if="appInfoStore?.appInfo?.data?.mobileSetting.is_show_item_video == '1'"
                class="col-span-12 mt-4 lg:px-8  md:px-8 md:col-start-1 sm:col-start-2 px-6 w-full">
                <div class="flex flex-auto">
                    <ps-label class="text-sm lg:text-base mt-14 sm:mt-0 relative"> {{ $t("item_entry__video") }} {{
                        $t("item_entry__must_be_less_than") }} {{ videoDurationString }} ) </ps-label>
                </div>

                <div class="flex">
                    <div class="mt-2 w-20 h-20 relative cursor-pointer" for="upload-video">
                        <input type="file" size=1 max=1 accept=".mov,.mp4" @change="onVideoSelected" ref="video"
                            id="upload-video" style="display: none;" />
                        <div @click="onVideoClick"
                            class="bg-fePrimary-50 w-20 h-20 rounded-2xl flex items-center justify-center absolute">
                            <img v-if="previewVideo.data == ''" alt="Placeholder" width="300px" height="300px"
                                class="rounded-xl w-20 h-20 object-cover "
                                :src="getVideoUrl() == '' ? $page.props.sysImageUrl + '/default_photo.png' : getVideoUrl()" />
                            <img v-else v-for='video in previewVideo.data' alt="Placeholder" width="300px" height="300px"
                                class="rounded-xl w-20 h-20 object-cover " :key="video" v-lazy="video" />
                        </div>
                        <div @click="videoDelete()" v-if="previewVideo.data != '' || getVideoUrl() != ''"
                            class="cursor-pointer relative mt-14 ms-11 bg-fePrimary-50 rounded-full dark:bg-feAchromatic-800  py-1.5 px-2.5">
                            <ps-icon name="trash-alt" class=" text-feSecondary-500 dark:text-feAchromatic-50 lg:text-base text-sm"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Choose Photo -->

        <!-- Start Input Field for md .. -->
        <div class="w-full grid  grid-cols-12 gap-4 sm:gap-3.5 lg:gap-4 ">
            <!-- Start Left Screen -->
            <div class="col-span-12  mt-4  lg:px-8  md:px-8 md:col-start-1 sm:col-start-2 px-6 w-full">
                <div class=" w-full">


                    <!-- title-->
                    <div class=" mb-4"
                        v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'title' && coreField.isVisible === '1')"
                        :key="index">
                        <ps-label class="text-base">{{ $t(coreField.labelName) }} <span v-if="coreField.mandatory = 1"
                                class="text-feError-800 font-medium ms-1">*</span></ps-label>
                        <ps-input ref="title" type="text" v-model:value="paramHolder1.title" class="dark:bg-transparent"
                            :placeholder="$t(coreField.placeholder)" @keypress="validateTitle" />
                        <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                            v-if="validation.titleStatus"> {{ $t("item_entry__title_required_error") }} </ps-label>
                    </div>
                    <!-- end title -->
                    <div class="flex flex-col sm:flex-row   mb-4">

                        <!-- for subcategory dropdown -->
                        <div class="w-full" v-if="appInfoStore?.appInfo?.data?.mobileSetting.is_show_subcategory == '1'">
                            <div class="  mb-4"
                                v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'subcategory_id' && coreField.isVisible === '1')"
                                :key="index">
                                <ps-label class="text-base">{{ $t(coreField.labelName) }}
                                    <!-- <span v-if="coreField.mandatory=1" class="text-feError-800 font-medium ms-1">*</span> -->
                                </ps-label>
                                <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full'
                                    @onClick="resetSubCategory(paramHolder1.catId)">
                                    <template #select>
                                        <ps-dropdown-select :showCenter="true" :selectedValue="paramHolder1.subCatName" :placeholder="$t(coreField.placeholder)"/>
                                    </template>
                                    <template #filter>
                                        <div class="w-56">
                                            <ps-input-with-right-icon class="rounded-xl flex-1 "
                                                @keyup.enter="filterSubCatUpdate(subCatKeyword)"
                                                v-model:value="subCatKeyword"
                                                v-bind:placeholder="$t('item_list__search_subcat')">
                                                <template #icon>
                                                    <ps-icon textColor="text-feSecondary-400 dark:text-feAchromatic-500"
                                                        name="search" class='cursor-pointer'
                                                        @click="filterSubCatUpdate(subCatKeyword)" />
                                                </template>
                                            </ps-input-with-right-icon>
                                        </div>
                                    </template>
                                    <template #list>
                                        <div class="rounded-md shadow-xs w-56">
                                            <div class="pt-2 z-30">
                                                <div v-if="subCategoryStore.subCategoryList.data == null">
                                                    <div
                                                        v-if="paramHolder1.catId == '' && subCategoryStore.loading.value == false">
                                                        <ps-label class='p-2 flex' textColor="text-feError-500"> {{
                                                            $t("item_entry__select_category_first") }}</ps-label>
                                                    </div>
                                                    <div v-else-if="subCategoryStore.loading.value == true">
                                                        <ps-label class='p-2 flex'> {{
                                                                $t("item_entry__loading") }} </ps-label>
                                                    </div>
                                                    <div v-else>
                                                        <ps-label class='p-2 flex'> {{
                                                                $t("list__no_result") }} </ps-label>
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <div v-for="selectSubcategory in subCategoryStore.subCategoryList.data"
                                                        :key="selectSubcategory.id"
                                                        class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                        @click="subCategoryFilterClicked(selectSubcategory)">
                                                        <ps-label class="ms-2"
                                                            :class="selectSubcategory.id == paramHolder1.subCatId ? ' font-bold' : ''">
                                                            {{ selectSubcategory.name }} </ps-label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                    <template #loadmore>

                                        <div class="mb-2 w-56">

                                            <div v-if="subCategoryStore.subCategoryList.data != null
                                                && subCategoryStore.loading.value == true" class='mt-4 ms-4 flex'>
                                                <ps-label> {{ $t("item_entry__loading") }} </ps-label>
                                            </div>

                                            <ps-label v-else-if="paramHolder1.catId != '' && !subCategoryStore.isNoMoreRecord.value"
                                                class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                                @click="loadSubCategory(paramHolder1.catId)"> {{ $t("item_entry__load_more")
                                                }} </ps-label>
                                        </div>

                                    </template>
                                </ps-dropdown>
                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.subCatStatus"> {{ $t("item_entry__sub_category_required_error") }}
                                </ps-label>
                            </div>
                        </div>
                        <!-- end subcategory -->
                    </div>

                    <!-- contact number  -->

                    <div class=" mb-6" v-if="phoneShow != 0"
                        v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'phone' && coreField.isVisible === '1')"
                        :key="index">
                        <ps-label class="text-base">{{ $t(coreField.labelName) }}<span v-if="coreField.mandatory == 1"
                                class="text-feError-800 font-medium ms-1">*</span></ps-label>
                        <div v-if="phoneStatus" class="mb-1 flex flex-col" v-for="(phone, index) in phoneShow" :key="phone">
                            <div class="flex w-full mt-2">

                                <ps-dropdown align="left" class='' @onClick="loadPhone">
                                    <template #filter>
                                        <div class="w-56">
                                            <ps-input-with-right-icon class="rounded-xl flex-1 "
                                                @keyup.enter="filterPhoneUpdate(phoneKeyword)" v-model:value="phoneKeyword"
                                                v-bind:placeholder="$t('phone_code_by_country')">
                                                <template #icon>
                                                    <ps-icon textColor="text-feSecondary-400 dark:text-feAchromatic-500"
                                                        name="search" class='cursor-pointer'
                                                        @click="filterPhoneUpdate(phoneKeyword)" />
                                                </template>
                                            </ps-input-with-right-icon>
                                        </div>
                                    </template>
                                    <template #select>

                                        <ps-dropdown-select :showCenter="true" :selectedValue="phoneList[index].code" />

                                    </template>
                                    <template #list>
                                        <div class="rounded-md shadow-xs w-56 ">
                                            <div class="pt-2 z-30 ">
                                                <div v-if="phoneStore.phoneList.data == null">
                                                    <ps-label class='p-2 flex' @click="loadPhone">{{
                                                        $t("item_entry__loading")
                                                    }} </ps-label>
                                                </div>
                                                <div v-else>
                                                    <div v-for="selectData in phoneStore.phoneList.data"
                                                        :key="selectData.id"
                                                        class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                        @click="phoneFilterClicked(selectData, index)">

                                                        <ps-label class="ms-2"
                                                            :class="selectData.country_code == phoneList[index].value ? ' font-bold' : ''">
                                                            {{ selectData.country_code }} </ps-label>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template #loadmore>

                                        <div class="mb-2 w-56">

                                            <div v-if="phoneStore.phoneList.data != null && phoneStore.loading.value == true"
                                                class='mt-4 ms-4 flex'>
                                                <ps-label> {{ $t("item_entry__loading") }}</ps-label>
                                            </div>

                                            <ps-label class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                                v-if="!phoneStore.isNoMoreRecord.value" @click="loadPhone"> {{
                                                    $t("item_entry__load_more") }} </ps-label>
                                        </div>

                                    </template>

                                </ps-dropdown>
                                <ps-input class="w-full dark:bg-transparent" ref="phone" type="text"
                                    v-model:value="phoneList[index]['value']" :placeholder="$t(coreField.placeholder)"
                                    @keypress="validatePhone" />
                                <!-- {{phoneList}} -->
                            </div>
                            <div class="mt-2">
                                <div class="flex flex-row">
                                    <ps-label class="mx-2 text-base cursor-pointer	" textColor="text-fePrimary-500"
                                        v-if="!(phone == 1 && phoneShow == 1)" @click="removePhone(index)">{{ '- '
                                            + $t("core__fe_remove_field") }}</ps-label>

                                    <ps-label @click="addPhone()" textColor="text-fePrimary-500"
                                        v-if="phone < parseInt(appInfoStore.appInfo.data?.mobileSetting?.phone_list_count) && phone == phoneShow"
                                        class="text-base cursor-pointer	">{{ '+ ' + $t("core__fe_add_new_fields")
                                        }}</ps-label>
                                </div>
                            </div>
                        </div>
                        <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                            v-if="validation.contactStatus"> {{ $t("item_entry__contact_required_error") }} </ps-label>
                    </div>

                    <!-- original price-->
                    <div v-if="appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType != 'NO_PRICE'" class="mb-4">

                        <ps-label
                            v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'original_price' && coreField.isVisible === '1')"
                            :key="index" class="text-base">{{ $t(coreField.labelName) }}<span v-if="coreField.mandatory = 1"
                                class="text-feError-800 font-medium ms-1 ">*</span></ps-label>


                        <div v-if="appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType == 'NORMAL_PRICE'" class="flex flex-row items-center  mt-2">
                            <div class=""
                                v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'currency_id' && coreField.isVisible === '1')"
                                :key="index">

                                <ps-dropdown align="left" class='  w-full' @onClick="loadCurrency">
                                    <template #select>
                                        <ps-dropdown-select :showCenter="true"
                                            :selectedValue="paramHolder1.currencyShortForm" />
                                    </template>

                                    <template #list>
                                        <div class="rounded-md shadow-xs w-56 ">
                                            <div class="pt-2 z-30 ">
                                                <div v-if="itemCurrencyStore.itemCurrencyList.data == null">
                                                    <ps-label class='p-2 flex' @click="loadCurrency"> {{
                                                        $t("item_entry__loading")
                                                    }} </ps-label>
                                                </div>
                                                <div v-else>
                                                    <div v-for="itemcurrency in itemCurrencyStore.itemCurrencyList.data"
                                                        :key="itemcurrency.id"
                                                        class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                        @click="currencyFilterClicked(itemcurrency)">
                                                        <ps-label class="ms-2"
                                                            :class="itemcurrency.id == paramHolder1.itemCurrencyId ? ' font-bold' : ''">
                                                            {{ itemcurrency.currencySymbol }} </ps-label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template #loadmore>

                                        <div class="mb-2 w-56">

                                            <div v-if="itemCurrencyStore.itemCurrencyList.data != null
                                                && itemCurrencyStore.loading.value == true" class='mt-4 ms-4 flex'>
                                                <ps-label> {{ $t("item_entry__loading") }} </ps-label>
                                            </div>

                                            <ps-label v-if="!itemCurrencyStore.isNoMoreRecord.value"
                                                class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                                @click="loadCurrency"> {{ $t("item_entry__load_more") }} </ps-label>
                                        </div>

                                    </template>
                                </ps-dropdown>
                                <ps-label class="  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.itemCurrencyStatus"> {{
                                        $t("item_entry__currency_symbol_required_error") }}
                                </ps-label>
                            </div>

                            <div class="w-full"
                                v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'original_price' && coreField.isVisible === '1')"
                                :key="index">

                                <ps-input ref="price" class="dark:bg-transparent" type="text"
                                    v-model:value="paramHolder1.price" :placeholder="$t(coreField.placeholder)"
                                    @keypress="validatePrice" />
                            </div>
                        </div>

                        <div v-if="appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType == 'PRICE_RANGE'" class="flex-row  ">
                            <div class=""
                                v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'currency_id' && coreField.isVisible === '1')"
                                :key="index">

                                <ps-dropdown  class='lg:mt-2 mt-1  w-full' >
                                    <template #select>
                                        <ps-dropdown-select :showCenter="true"

                                            :selectedValue="paramHolder1.price == '' ? '' : price_range.filter((price) => price.id == paramHolder1.price)[0].value"

                                            />
                                    </template>

                                    <template #list>
                                        <div class="rounded-md shadow-xs w-56 ">
                                            <div class="pt-2 z-30 ">

                                                    <div v-for="range in price_range"
                                                        :key="range.id"
                                                        class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                        @click="priceRangeClicked(range)">
                                                        <ps-label class="ms-2"
                                                            :class="range.id == paramHolder1.price ? ' font-bold' : ''">
                                                            {{ range.value }} </ps-label>
                                                    </div>

                                            </div>
                                        </div>
                                    </template>

                                </ps-dropdown>
                                <ps-label class="  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.itemCurrencyStatus"> {{
                                        $t("item_entry__currency_symbol_required_error") }}
                                </ps-label>
                            </div>


                        </div>

                        <ps-label class="mt-1  w-full text-xs" textColor="text-feError-500" v-if="validation.priceStatus"> {{
                            $t("item_entry__price_required_error") }} </ps-label>
                    </div>

                    <!-- percent -->
                    <div v-if="appInfoStore.appInfo.data?.mobileSetting?.is_show_discount == '1'">
                        <div class=" mb-4"
                            v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'percent' && coreField.isVisible === '1')"
                            :key="index">
                            <ps-label class="text-base">{{ $t(coreField.labelName) }}<span v-if="coreField.mandatory == 1"
                                    class="text-feError-800 font-medium ms-1">*</span></ps-label>
                            <ps-input ref="percent" class="dark:bg-transparent" type="text"
                                v-model:value="paramHolder1.percent" :placeholder="$t(coreField.placeholder)"
                                @keypress="validatePercent" />
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.discountStatus"> {{ $t("item_entry__discount_required_error") }}
                            </ps-label>
                        </div>
                    </div>

                    <!-- quantity -->
                    <div v-for="customFieldHeader in customFieldStore.customField.data?.customList.filter((customizeHeader)=>customizeHeader.coreKeysId == itemQuantityCoreKeysId)" :key="customFieldHeader.id">
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00007' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <ps-input type="text" class="w-full rounded" :placeholder="$t(customFieldHeader.placeholder)"
                                v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                @keypress="validateCustom(customFieldHeader.coreKeysId)"/>
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>
                    </div>

                    <!-- for description-->
                    <div class=" mb-4"
                        v-for="(coreField, index ) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'description' && coreField.isVisible === '1')"
                        :key="index">
                        <ps-label class="text-base">{{ $t(coreField.labelName) }} <span v-if="coreField.mandatory == 1"
                                class="text-feError-800 font-medium ms-1">*</span></ps-label>
                        <ps-textarea rows="4" v-model:value="paramHolder1.description"
                            :placeholder="$t(coreField.placeholder)" @keypress="validateDescription" />
                        <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                            v-if="validation.descriptionStatus"> {{ $t("item_entry__description_required_error") }}
                        </ps-label>
                    </div>





                    <div v-for="customFieldHeader in customFieldStore.customField.data?.customList.filter((customizeHeader)=>customizeHeader.coreKeysId != itemQuantityCoreKeysId)"
                        :key="customFieldHeader.id">
                        <!-- dropdown-->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00001' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == 1">*</span></ps-label>
                            <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full'
                                @onClick="loadCustomField(customFieldHeader.coreKeysId)">
                                <template #select>

                                    <ps-dropdown-select :showCenter="true" :selectedValue="customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data?.filter((customField) => customField.id === form.product_relation[customFieldHeader.coreKeysId])[0]?.name"
                                        :placeholder="$t(customFieldHeader.placeholder)"/>

                                </template>
                                <template #list>
                                    <div class="rounded-md shadow-xs w-56 ">
                                        <div class="pt-2 z-30 ">
                                            <div
                                                v-if="customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data == null">
                                                <ps-label class='p-2 flex'
                                                    @click="loadCustomField(customFieldHeader.coreKeysId)">{{
                                                        $t("item_entry__loading") }} </ps-label>
                                            </div>
                                            <div v-else>
                                                <div v-for="selectData in customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data"
                                                    :key="selectData.coreKeysId"
                                                    class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                    @click="selectCustomDropdown(customFieldHeader.coreKeysId, selectData.id)">

                                                    <ps-label class="ms-2"
                                                        :class="form.product_relation[customFieldHeader.coreKeysId] == selectData.id ? 'font-bold' : ''">
                                                        {{ selectData.name }} </ps-label>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template #loadmore>

                                    <div class="mb-2 w-56">

                                        <div v-if="customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data != null
                                            && customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.loading.value == true"
                                            class='mt-4 ms-4 flex'>
                                            <ps-label> {{ $t("item_entry__loading") }}</ps-label>
                                        </div>

                                        <ps-label class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                            v-if="!customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.isNoMoreRecord.value"
                                            @click="loadCustomField(customFieldHeader.coreKeysId)"> {{
                                                $t("item_entry__load_more") }} </ps-label>
                                    </div>

                                </template>
                            </ps-dropdown>
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>



                        <!-- text-->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00002' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label>{{ $t(customFieldHeader.name) }}<span class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <ps-input type="text" class="w-full dark:bg-transparent rounded"
                                :placeholder="$t(customFieldHeader.placeholder)"
                                v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                @keypress="validateCustom(customFieldHeader.coreKeysId)" />
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- radio-->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00003' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <div class="flex flex-col">
                                <div class="mb-1"
                                    v-for="detail in customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data"
                                    :key="detail.id">
                                    <ps-radio-value-2 color="text-purple-600 border-purple-300"
                                        @change="validateCustom(customFieldHeader.coreKeysId)"
                                        v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                        :title="detail.id.toString()" :title_label="detail.name" />
                                    <ps-label :for="detail.id">{{ detail.attribute }}</ps-label>
                                </div>
                            </div>
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- checkbox-->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00004' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <!-- <ps-label class="mb-1">{{ $t(customFieldHeader.name) }}</ps-label> -->
                            <div class="flex flex-row">
                                <div class="me-2 flex items-center">
                                    <input type="checkbox" class="rounded border" value="0"
                                        v-model="form.product_relation[customFieldHeader.coreKeysId]"
                                        @change="validateCustom(customFieldHeader.coreKeysId)" />
                                    <ps-label class="ms-2">{{ $t(customFieldHeader.name) }}</ps-label>
                                </div>
                            </div>
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- datetime -->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00005' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <div  v-if="date_picker">
                                <date-picker v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                    @change="validateCustom(customFieldHeader.coreKeysId)" :enableTimePicker="true"
                                    :inline="false" :autoApply="false" />
                            </div>
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- textarea -->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00006' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0' && customFieldHeader.coreKeysId != 'ps-itm00009'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <ps-textarea rows="4" :placeholder="$t(customFieldHeader.placeholder)"
                                v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                @keypress="validateCustom(customFieldHeader.coreKeysId)" />
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- number-->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00007' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <ps-input type="number" class="w-full rounded" :placeholder="$t(customFieldHeader.placeholder)"
                                v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                @keypress="validateCustom(customFieldHeader.coreKeysId)" />
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- multi Select-->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00008' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <div class="flex flex-col ">
                                <CheckBox :oldData="form.product_relation[customFieldHeader.coreKeysId]"
                                    @toParent="handleMultiSelect"
                                    :customizeDetails="customizeUiStoreList.data?.filter((customizeDetail) => customizeDetail?.id === customFieldHeader?.coreKeysId)[0]?.provider?.customizeUiList.data"
                                    :customizeHeader="customFieldHeader" />
                            </div>
                            <!-- {{customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customFieldHeader.coreKeysId)[0]?.provider?.customizeUiList.data}} -->
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- Image-->
                        <!-- <div class=" mb-4" v-if="customFieldHeader.uiType.coreKeysId === 'uit00009' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                                <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span class="text-feError-800 font-medium ms-1" v-show="customFieldHeader.mandatory ===1">*</span></ps-label>
                                <div v-if="item.image" class="flex items-end pt-4">
                                    <img
                                    v-lazy=" { src: $page.props.thumb1xUrl + '/' + item.image.img_path, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                        class="w-96 h-48" :alt="$t(core__be_item_photo)" />
                                    <ps-button type="button" @click="replaceImageClicked(item.image.id)" rounded="rounded-full" shadow="drop-shadow-2xl" class="-ms-10 mb-2" colors="bg-feAchromatic-50 text-fePrimary-500 dark:bg-feAchromatic-900" border="border border-1 dark:border-feSecondary-700 border-feSecondary-300" padding="p-1.5" hover="" focus="">
                                        <ps-icon name="pencil-btn"  w="21" h="21" />
                                    </ps-button>
                                    <ps-image-icon-modal ref="ps_image_icon_modal" />
                                    <ps-action-modal ref="ps_action_modal" />
                                    <ps-danger-dialog ref="ps_danger_dialog" />
                                </div>
                                <ps-image-upload v-else uploadType="image" v-model:imageFile="form.product_relation[customFieldHeader.coreKeysId]" />
                                <ps-label textColor="text-feError-500 "  class="lg:mt-2 mt-1  w-full text-xs">{{ product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId] }}</ps-label>
                            </div> -->

                        <!-- time Only -->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00010' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <input type="time" class="w-full rounded"
                                v-model="form.product_relation[customFieldHeader.coreKeysId]"
                                @keypress="validateCustom(customFieldHeader.coreKeysId)" />
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>

                        <!-- date Only -->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00011' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <div  v-if="date_picker">
                                <date-picker v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                    @change="validateCustom(customFieldHeader.coreKeysId)" :inline="false"
                                    :autoApply="false" />
                            </div>
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row   mb-4">
                        <!-- for location city dropdown -->
                        <div class=" mb-4  w-full me-6 sm:me-2 mb-4 sm:mb-0"
                            v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'location_city_id' && coreField.isVisible === '1')"
                            :key="index">
                            <ps-label class="text-base">{{ $t(coreField.labelName) }}<span v-if="coreField.mandatory = 1"
                                    class="text-feError-800 font-medium ms-1">*</span></ps-label>
                            <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full mb-2' @onClick="loadLocation"
                                v-on:keyup.enter="filterKeywordUpate(locationKeyword)">
                                <template #select>
                                    <ps-dropdown-select :showCenter="true" :selectedValue="paramHolder1.itemLocationName" :placeholder="$t(coreField.placeholder)"/>
                                </template>
                                <template #filter>
                                    <div class="w-56">
                                        <ps-input-with-right-icon class="rounded-full flex-1 "
                                            v-model:value="locationKeyword"
                                            v-bind:placeholder="$t('item_entry__search_location')">
                                            <template #icon>
                                                <ps-icon name="search" class='cursor-pointer'
                                                    @click="filterKeywordUpate(locationKeyword)" />
                                            </template>
                                        </ps-input-with-right-icon>
                                    </div>
                                </template>
                                <template #list>
                                    <div class="rounded-md shadow-xs w-56 ">
                                        <div class="pt-2 z-30 ">
                                            <div v-if="itemLocationStore.loading.value == true" class='mt-4 ms-4 mb-4'>
                                                <ps-label @click="loadLocation"> {{ $t("item_entry__loading") }} </ps-label>
                                            </div>

                                            <div v-for="selectData in itemLocationStore.itemLocationList.data"
                                                :key="selectData.id"
                                                class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                @click="locationFilterClicked(selectData)">
                                                <ps-label class="ms-2"
                                                    :class="selectData.id == paramHolder1.itemLocationId ? ' font-bold text-fePrimary-700' : ''">
                                                    {{ selectData.name }} </ps-label>
                                            </div>
                                        </div>

                                    </div>
                                </template>
                                <template #loadmore>

                                    <div class="mb-2 w-56">

                                        <div v-if="itemLocationStore.itemLocationList.data != null
                                            && itemLocationStore.loading.value == true" class='mt-4 ms-4 flex'>
                                            <ps-label>{{ $t("item_entry__loading") }} </ps-label>
                                        </div>

                                        <ps-label v-if="!itemLocationStore.isNoMoreRecord.value"
                                            class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                            @click="loadLocation"> {{ $t("item_entry__load_more") }} </ps-label>
                                    </div>

                                </template>
                            </ps-dropdown>
                            <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                v-if="validation.itemLocationStatus"> {{ $t("item_entry__location_required_error") }}
                            </ps-label>
                        </div>
                        <!-- end location city -->

                        <!-- for location township dropdown -->
                        <div class=" mb-4  w-full " v-if="appInfoStore.appInfo.data?.psAppSetting?.isSubLocation == '1'">
                            <div v-for="(coreField, index) in customFieldStore.customField.data?.coreList.filter((coreField) => coreField.fieldName === 'location_township_id' && coreField.isVisible === '1')"
                                :key="index">
                                <ps-label class="text-base">{{ $t(coreField.labelName) }}
                                    <!-- <span v-if="coreField.mandatory=1" class="text-feError-800 font-medium ms-1">*</span> -->
                                </ps-label>
                                <ps-dropdown align="left" class='lg:mt-2 mt-1  w-full'
                                    @onClick="resetLocationTownship(paramHolder1.itemLocationId)">
                                    <template #select>
                                        <ps-dropdown-select :showCenter="true"
                                            :selectedValue="paramHolder1.locationTownshipName" :placeholder="$t(coreField.placeholder)"/>
                                    </template>
                                    <template #filter>
                                        <div class="w-56">
                                            <ps-input-with-right-icon class="rounded-xl flex-1 "
                                                @keyup.enter="filtersubLocationUpdate(sublocationKeyword)"
                                                v-model:value="sublocationKeyword"
                                                v-bind:placeholder="$t('search_for_large_screem__sub_location')">
                                                <template #icon>
                                                    <ps-icon textColor="text-feSecondary-400 dark:text-feAchromatic-500"
                                                        name="search" class='cursor-pointer'
                                                        @click="filtersubLocationUpdate(sublocationKeyword)" />
                                                </template>
                                            </ps-input-with-right-icon>
                                        </div>
                                    </template>
                                    <template #list>
                                        <div class="rounded-md shadow-xs w-56">
                                            <div class="pt-2 z-30">
                                                <div v-if="locationTownshipProvider.locationTownshipList.data == null">
                                                    <div
                                                        v-if="paramHolder1.itemLocationId == '' && locationTownshipProvider.loading.value == false">
                                                        <ps-label class='p-2 flex' textColor="text-feError-500"> {{
                                                            $t("item_entry__select_location_first") }}</ps-label>
                                                    </div>
                                                    <div v-else-if="locationTownshipProvider.loading.value == true">
                                                        <ps-label class='p-2 flex'> {{ $t("item_entry__loading") }} </ps-label>
                                                    </div>
                                                    <div v-else>
                                                        <ps-label class='p-2 flex'> {{ $t("list__no_result") }} </ps-label>
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <div v-for="locationTownship in locationTownshipProvider.locationTownshipList.data"
                                                        :key="locationTownship.id"
                                                        class="w-56 flex py-4 px-2 hover:bg-fePrimary-50 dark:hover:bg-fePrimary-900 cursor-pointer items-center"
                                                        @click="locationTownshipFilterClicked(locationTownship)">
                                                        <ps-label class="ms-2"
                                                            :class="locationTownship.id == paramHolder1.locationTownshipId ? ' font-bold' : ''">
                                                            {{ locationTownship.townshipName }} </ps-label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                    <template #loadmore>

                                        <div class="mb-2 w-56">

                                            <div v-if="locationTownshipProvider.locationTownshipList.data != null
                                                && locationTownshipProvider.loading.value == true"
                                                class='mt-4 ms-4 flex'>
                                                <ps-label> {{ $t("item_entry__loading") }} </ps-label>
                                            </div>

                                            <ps-label
                                                v-if="paramHolder1.itemLocationId != '' && !locationTownshipProvider.isNoMoreRecord.value"
                                                class="flex mt-4 ms-4 mb-2 underline font-bold cursor-pointer"
                                                @click="loadLocationTownship(paramHolder1.itemLocationId)"> {{
                                                    $t("item_entry__load_more") }} </ps-label>
                                        </div>

                                    </template>
                                </ps-dropdown>

                                <ps-label class="lg:mt-2 mt-1  w-full text-xs" textColor="text-feError-500"
                                    v-if="validation.locationTownshipStatus"> {{
                                        $t("item_entry__location_township_required_error")
                                    }} </ps-label>
                            </div>
                        </div>
                        <!-- end location township -->
                    </div>






                    <div class="flex flex-col justify-start lg:mt-5 mt-4  font-medium text-base lg:text-lg  mb-4">
                        <ps-label class="  font-medium text-base lg:text-lg  mb-2"> {{ $t("item_entry__pick_location") }}
                        </ps-label>

                        <!-- <ps-label-2 class="mb-2"> {{ $t("item_entry__map_caption") }} </ps-label-2> -->
                        <map-with-pin-picker v-if="isReloadOpenStreetMap && appInfoStore?.appInfo?.data?.frontendConfigSetting.googleMap == '1'"
                            class="h-68" :onChange="updateCoordinates" :lat="parseFloat(paramHolder1.latitude)"
                            :lng="parseFloat(paramHolder1.longitude)" />
                        <open-street-map :dir="$page.props.dir"
                            v-if="isReloadOpenStreetMap && appInfoStore?.appInfo?.data?.frontendConfigSetting.googleMap == '0'"
                            class="h-68" :onChange="updateLeafletCoordinates" :lat="parseFloat(paramHolder1.latitude)"
                            :lng="parseFloat(paramHolder1.longitude)" />

                    </div>

                    <div class="mt-2" v-for="customFieldHeader in customFieldStore.customField.data?.customList"
                        :key="customFieldHeader.id">
                        <!-- textarea -->
                        <div class=" mb-4"
                            v-if="customFieldHeader.uiType.coreKeysId === 'uit00006' && customFieldHeader.isVisible === '1' && customFieldHeader.isDelete === '0' && customFieldHeader.coreKeysId == 'ps-itm00009'">
                            <ps-label class="text-base">{{ $t(customFieldHeader.name) }}<span
                                    class="text-feError-800 font-medium ms-1"
                                    v-show="customFieldHeader.mandatory == '1'">*</span></ps-label>
                            <ps-textarea rows="4" :placeholder="$t(customFieldHeader.placeholder)"
                                v-model:value="form.product_relation[customFieldHeader.coreKeysId]"
                                @keypress="validateCustom(customFieldHeader.coreKeysId)" />
                            <ps-label textColor="text-feError-500 " class="lg:mt-2 mt-1  w-full text-xs">{{
                                product_relation_errors && product_relation_errors[customFieldHeader.coreKeysId]
                            }}</ps-label>
                        </div>
                    </div>


                </div>
                <div class="w-full ">
                    <!-- <ps-button class="lg:mt-5 mt-4 mb-5 text-center w-full " textSize="lg:text-sm sm:text-xs text-xs" theme="bg-fePrimary-500 dark:bg-feAccent-500 text-feAchromatic-50 dark:text-feAchromatic-900 py-1" @click="locatorButtonPress"> CurrentLoc </ps-button> -->
                    <ps-button v-if="itemId == '0'" class="lg:mt-5 mt-4 mb-5 text-center w-full "
                        textSize="lg:text-sm sm:text-xs text-xs"
                        theme="bg-fePrimary-500 dark:bg-feAccent-500 text-feAchromatic-50 dark:text-feAchromatic-900 py-1"
                        @click="submitClicked"> {{ $t("core__fe_item_entry__submit") }} </ps-button>
                    <ps-button v-else class="lg:mt-5 mt-4 mb-5 text-center w-full " textSize="lg:text-sm sm:text-xs text-xs"
                        theme="bg-fePrimary-500 dark:bg-feAccent-500 text-feAchromatic-50 dark:text-feAchromatic-900 py-1"
                        @click="submitClicked"> {{ $t("item_entry__update") }} </ps-button>

                    <ps-button textSize="lg:text-sm sm:text-xs text-xs"
                        colors="bg-feAchromatic-50 dark:bg-feAchromatic-900 dark:text-feAchromatic-50 "
                        class="lg:mt-5 mt-4 mb-5 text-center w-full" @click="loginClicked" :disabled="false">
                        {{ $t("core__be_btn_cancel") }} </ps-button>
                </div>
            </div>
            <!-- End Left Screen -->

        </div>
        <!-- End Input Field -->

    </div>
    <!-- {{$page.props.thumb3xUrl}} -->

    <ps-confirm-dialog ref="ps_confirm_dialog" />

    <ps-loading-dialog ref="ps_loading_dialog" />

    <ps-error-dialog ref="ps_error_dialog" />

    <ps-warning-dialog ref="ps_warning_dialog" />

    <limit-item-modal ref="limit_item_modal" />

    <ps-success-dialog2 ref="ps_success_dialog" />
</template>


<script lang="ts">
//Vue
import { reactive, ref, onMounted, onUnmounted, defineAsyncComponent, watch } from 'vue';

import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue'
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsLabelCaption from '@template1/vendor/components/core/label/PsLabelCaption.vue';
import PsLabelCaption2 from '@template1/vendor/components/core/label/PsLabelCaption2.vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsInputWithRightIcon from '@template1/vendor/components/core/input/PsInputWithRightIcon.vue';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';
import PsDropdownSelect from '@template1/vendor/components/core/dropdown/PsDropdownSelect.vue';
import PsTextarea from '@template1/vendor/components/core/textarea/PsTextarea.vue';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import PsSuccessDialog from '@template1/vendor/components/core/dialog/PsSuccessDialog.vue';
import PsSuccessDialog2 from '@template1/vendor/components/core/dialog/PsSuccessDialog2.vue';
import PsConfirmDialog from '@template1/vendor/components/core/dialog/PsConfirmDialog.vue';
import PsWarningDialog from '@template1/vendor/components/core/dialog/PsWarningDialog.vue';
import PsCheckboxValue from '@template1/vendor/components/core/checkbox/PsCheckboxValue.vue';
import LimitItemModal from '@template1/vendor/components/modules/item/LimitItemModal.vue';
import useValidators from '@/Validation/Validators'
import DatePicker from "@template1/vendor/components/core/datetime/DatePicker.vue";
import PsRadioValue2 from "@template1/vendor/components/core/radio/PsRadioValue2.vue";
import CheckBox from "@template1/vendor/components/core/checkbox/CheckBox.vue";

// Models
import { useCategoryStoreState } from '@templateCore/store/modules/category/CategoryStore';
import { useSubCategoryStoreState } from '@templateCore/store/modules/subCategory/SubCategoryStore';
import { useItemCurrencyStoreState } from '@templateCore/store/modules/item/ItemCurrencyStore';
import { useItemLocationStoreState } from '@templateCore/store/modules/itemlocation/ItemLocationStore';
import { useItemLocationTownshipStoreState } from '@templateCore/store/modules/itemLocationTownship/ItemLocationTownshipStore';
import { useProductStore } from '@templateCore/store/modules/item/ProductStore';
import { usePhoneStore } from '@templateCore/store/modules/item/PhoneStore';
import { useGalleryStoreState } from '@templateCore/store/modules/gallery/GalleryStore';
import { useUserStore } from "@templateCore/store/modules/user/UserStore";
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore';
import { useCustomFieldStoreState } from '@templateCore/store/modules/customField/CustomFieldStore';
import { useCustomizeUiStoreState } from '@templateCore/store/modules/customField/CustomizeUiStore';
import Axios from 'axios';

// Params Holders
import ItemEntryParameterHolder from '@templateCore/object/holder/ItemEntryParameterHolder';
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';

import PsConst from '@templateCore/object/constant/ps_constants';
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import LocationParameterHolder from '@templateCore/object/holder/LocationParameterHolder';
import LocationTownshipParameterHolder from '@templateCore/object/holder/LocationTownshipParameterHolder';
import SubCategoryListParameterHolder from '@templateCore/object/holder/SubCategoryListParameterHolder';
import PsStatus from '@templateCore/api/common/PsStatus';
import DefaultPhoto from '@templateCore/object/DefaultPhoto';
import Dropzone from '@template1/vendor/components/core/dropzone/DropZone.vue';
import { useForm } from "@inertiajs/vue3";

//language
import { trans } from 'laravel-vue-i18n';
import PsUtils from '@templateCore/utils/PsUtils';
import { router } from '@inertiajs/vue3';

// import Dropzone from "@/Components/Core/Dropzone/Dropzone1.vue"
import axios from 'axios';
import PsBannerIcon from "@/Components/Core/Banners/PsBannerIcon.vue";
// import PsConfig from '@template1/config/PsConfig';

const MapWithPinPicker = defineAsyncComponent(() => import('@template1/vendor/components/layouts/map/MapWithPinPicker.vue'));
const OpenStreetMap = defineAsyncComponent(() => import('@template1/vendor/components/layouts/map/OpenStreetMap.vue'))

export default {
    name: "ItemEntryView",
    components: {
        PsButton,
        PsLabelCaption,
        PsLabelCaption2,
        PsLabel,
        PsInput,
        PsDropdown,
        PsDropdownSelect,
        PsTextarea,
        MapWithPinPicker,
        PsLoadingDialog,
        PsErrorDialog,
        PsConfirmDialog,
        PsInputWithRightIcon,
        PsIcon,
        PsWarningDialog,
        PsCheckboxValue,
        LimitItemModal,
        OpenStreetMap,
        //    draggable: VueDraggableNext,
        // DraggableContainer,
        DatePicker,
        PsRadioValue2,
        CheckBox,
        Dropzone,
        PsSuccessDialog,
        PsSuccessDialog2,
        PsBannerIcon

        // Draggable,
        // DraggableContainer
    },
    props: ['thumb2xUrl', 'authUser', 'sysImageUrl', 'itemId','dir','categoryId','categoryName'],
    setup(props) {

        // Variables Init
        const itemProvider = useProductStore();
        const phoneStore = usePhoneStore('phoneList');
        const phoneStoredefault = usePhoneStore('default');
        let visible = ref(false)

        const ps_success_dialog = ref();
        const galleryLoad = ref(false);
        const date_picker = ref(false);
        const vendorAutoChoose = ref(false);
        const defaultProfileId = ref(localStorage.defaultProfileId ?? '');
        const defaultProfileType = ref(localStorage.defaultProfileType ?? '');

        const price_range = ref([
            {
                id:"1",
                value:"$"
            },
            {
                id:"2",
                value:"$$"
            },
            {
                id:"3",
                value:"$$$"
            },
            {
                id:"4",
                value:"$$$$"
            },
            {
                id:"5",
                value:"$$$$$"
            },

        ]);

        const appInfoStore = usePsAppInfoStoreState();
        // const itemLocationTownshipStore = useItemLocationTownshipStoreState();
        const subparamHolder = new LocationTownshipParameterHolder();
        const subcatHolder = new SubCategoryListParameterHolder();

        let form = useForm(
            {
                product_relation: {},
                images: [],
                extra_caption: [],
                item_images: {},
            }
        )

        let product_relation_errors = ref({});

        const sublocationKeyword = ref('');
        const subCatKeyword = ref("");
        const phoneKeyword = ref("");
        const isReloadOpenStreetMap = ref(true);

        const validation = ref({
            titleStatus: false,
            catStatus: false,
            subCatStatus: false,
            itemTypeStatus: false,
            conditionOfItemStatus: false,
            brandStatus: false,
            itemPriceTypeStatus: false,
            itemCurrencyStatus: false,
            priceStatus: false,
            dealOptionStatus: false,
            isShopStatus: false,
            itemLocationStatus: false,
            locationTownshipStatus: false,
            discountStatus: false,
            contactStatus: false,
            descriptionStatus: false,
            imageStatus: false,
            maxImgExceed: false,
        });

        const imageCount = ref(1);
        const itemAddressCoreKeysId = "ps-itm00009";
        const itemQuantityCoreKeysId = "ps-itm00010";
        let gallery = ref();
        // const image1 = ref();
        // const image2 = ref();
        const video = ref();
        const previewImages = reactive([{ 'id': 0, 'imgUrl': '' }]);
        const dropzoneImages = ref(0);
        const queueFinish = ref(false);
        const loginUserId = props.authUser ? props.authUser.id : PsConst.NO_LOGIN_USER;

        const phoneList = reactive([{ 'id': 0, 'value': '', 'code': '+1' }]);
        const phoneCountryCide = reactive([{ 'id': 0, 'value': '+1' }]);
        const phoneShow = ref(0);
        const phoneStatus = ref(true);
        const previewVideo = reactive({
            data: [] as any
        });
        let selectedFile = new Array();
        let selectImgIndex = ref(0);

        let selectedFileVideo;
        let selectedFileVideoThumb;

        let imgId = new Array();

        // let isVideoSetting = ref(PsConfig.isEnableVideoSetting);

        const psValueStore = PsValueStore();

        const ps_warning_dialog = ref();

        const dropzone_ref = ref();

        const autoProcessQueue = ref();

        if (loginUserId == null || loginUserId == '' || loginUserId == PsConst.NO_LOGIN_USER) {
            router.get(route('login'));
        }

        const paramHolder1 = ref(new ItemEntryParameterHolder());
        const locationKeyword = ref('');
        const paramHolder = new LocationParameterHolder().getDefaultParameterHolder();



        paramHolder1.value.latitude = psValueStore.locationLat == null || psValueStore.locationLat == '' ? '0' : psValueStore.locationLat;
        paramHolder1.value.longitude = psValueStore.locationLng == null || psValueStore.locationLng == '' ? '0' : psValueStore.locationLng;
        // const route = useRoute();

        // Inject Providers
        const categoryStore = useCategoryStoreState('entry');
        const subCategoryStore = useSubCategoryStoreState('entry');
        const itemLocationStore = useItemLocationStoreState('entry');
        const galleryProvider = useGalleryStoreState('entry');
        galleryProvider.galleryList.data = null;

        const locationTownshipProvider = useItemLocationTownshipStoreState('entry');
        const itemCurrencyStore = useItemCurrencyStoreState('entry');
        const defaultitemCurrencyStore = useItemCurrencyStoreState('default');

        const customFieldStore = useCustomFieldStoreState('entry');
        const customizeUiStoreList = reactive({
            data: [{
                'id': 'default',
                'provider': useCustomizeUiStoreState('default')
            }]
        });

        const userStore = useUserStore();
        // Get Route Data
        const itemId = props.itemId;
        const map_with_pin_picker_modal = ref();
        const ps_loading_dialog = ref();
        const ps_error_dialog = ref();
        const ps_confirm_dialog = ref();
        const limit_item_modal = ref();
        const dropzone_loading = ref(false);

        const dataReady = ref(false);
        let locationPermission = ref(true);
        const videoDurationString = ref('1 min');

        function showDialog(){
            ps_confirm_dialog.value.openModal(
                    trans('category__re_selection'),
                    trans('category__re_selection_description'),
                    trans('ps_confirm_dialog__confirm'),
                    trans('btn_cancel'),
                    () => {
                        router.get(route('fe_item_entry'));
                    },
                    () => {
                        PsUtils.log("Cancel");
                    }
                );
        }

        onMounted(async () => {
            const appInfoParameterHolder = new AppInfoParameterHolder();
            locatorButtonPress();
            appInfoParameterHolder.userId = loginUserId;
            // await appInfoStore.loadAppInfo(appInfoParameterHolder);
            await userStore.loadUser(loginUserId);
            const phoneStoredefault = usePhoneStore('default');

            phoneStoredefault.loadPhoneCountryCode(loginUserId, itemProvider.phoneparamHolder);
            // phoneList[0].code = phoneStoredefault1.phoneList.data[0].country_code

            if (!locationPermission.value) {
                // update map lat and lng according to
                paramHolder1.value.latitude = appInfoStore?.appInfo?.data?.psAppSetting?.latitude.toString();
                paramHolder1.value.longitude = appInfoStore?.appInfo?.data?.psAppSetting?.longitude.toString();
                isReloadOpenStreetMap.value = false;
                form.product_relation[itemAddressCoreKeysId] = "";
                setTimeout(() => {
                    isReloadOpenStreetMap.value = true;
                }, 1000);
            }

            //show payment modal for limit ad post
            if (appInfoStore?.appInfo?.data?.psAppSetting?.isPaidApp == PsConst.ONE && itemId == '0' && userStore.user?.data?.postCount == PsConst.LIMITED && userStore.user?.data?.remainingPost == '0') {
                limit_item_modal.value.openModal();
            }

            if (parseInt(appInfoStore?.appInfo?.data?.psAppSetting?.maxImgUploadOfItem) >= 1) {
                imageCount.value = parseInt(appInfoStore?.appInfo?.data?.psAppSetting?.maxImgUploadOfItem);
            }

            videoDurationString.value = PsUtils.secondToDuration(parseInt(appInfoStore?.appInfo?.data.mobileSetting?.video_duration) / 1000)

            // if( parseInt(appInfoStore.appInfo.data.frontendConfigSetting.imageLimit) >=1 ){
            //     imageCount.value = parseInt(appInfoStore.appInfo.data.frontendConfigSetting.imageLimit);
            // }

            previewImages.shift();
            for (let i = 0; i < imageCount.value; i++) {
                previewImages.push({ 'id': i + 1, 'imgUrl': '' });
                imgId[i] = '';
            }

            // phoneList.shift();
            if (parseInt(appInfoStore.appInfo.data?.mobileSetting?.phone_list_count) < 1) {
                phoneShow.value = 0;
            } else {
                phoneShow.value = 1;
            }

            await customFieldStore.loadCustomFieldList(loginUserId,props.categoryId);

            await defaultitemCurrencyStore.loadItemCurrencyList(loginUserId);

            paramHolder1.value.currencyShortForm = defaultitemCurrencyStore.itemCurrencyList.data[0].currencySymbol
            paramHolder1.value.itemCurrencyId = defaultitemCurrencyStore.itemCurrencyList.data[0].id

            for (const customField of customFieldStore.customField.data?.customList) {
                // for dropdown
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00001') {
                    customizeUiStoreList.data.push({
                        'id': customField.coreKeysId,
                        'provider': useCustomizeUiStoreState(customField.coreKeysId)
                    })
                }

                // for radio
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00003') {
                    customizeUiStoreList.data.push({
                        'id': customField.coreKeysId,
                        'provider': useCustomizeUiStoreState(customField.coreKeysId)
                    });
                    loadCustomField(customField.coreKeysId);
                }

                // for multi select
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00008') {
                    customizeUiStoreList.data.push({
                        'id': customField.coreKeysId,
                        'provider': useCustomizeUiStoreState(customField.coreKeysId)
                    })
                    loadCustomField(customField.coreKeysId);
                }
            }

            videoDurationString.value = PsUtils.secondToDuration(parseInt(appInfoStore?.appInfo?.data.mobileSetting?.video_duration) / 1000)


            if(appInfoStore?.isVendorSettingOn() && localStorage.defaultProfileType && localStorage.defaultProfileType == 'user'){
                paramHolder1.value.vendorId = '';
                paramHolder1.value.vendorName = userStore.user?.data?.userName;
                vendorAutoChoose.value = true;
            }else if(appInfoStore?.isVendorSettingOn() && localStorage.defaultProfileType && localStorage.defaultProfileType == 'vendor' && customFieldStore.customField.data?.vendorList.length > 0){
                let hasaccess = false;

                customFieldStore.customField.data?.vendorList.forEach(function(vendor) {
                    if(localStorage.defaultProfileId && localStorage.defaultProfileId == vendor.id){
                        vendorAutoChoose.value = true;
                        defaultProfileId.value = localStorage.defaultProfileId;
                        defaultProfileType.value = localStorage.defaultProfileType;

                        paramHolder1.value.vendorId = vendor.id;
                        paramHolder1.value.vendorName = vendor.name;

                        hasaccess = true;
                    }
                });

                if(!hasaccess){
                    vendorAutoChoose.value = false;
                    defaultProfileId.value = '';
                    defaultProfileType.value = '';
                    localStorage.defaultProfileId = '';
                    localStorage.defaultProfileType = '';

                    paramHolder1.value.vendorId  = '';
                    paramHolder1.value.vendorName = '';
                }
            }

            if (itemId != '0') {
                // queueFinish.value=false;
                loadItemForEdit();
            }
            else {
                date_picker.value = true;
                galleryLoad.value = true;

                phoneList[0]['code'] = appInfoStore?.appInfo?.data?.defaultPhoneCountryCode?.country_code ?? '+1';
                phoneCountryCide[0]['value'] = appInfoStore?.appInfo?.data?.defaultPhoneCountryCode?.country_code ?? '+1';
            }
        });

        onUnmounted(() => {
            categoryStore.$reset;
            subCategoryStore.$reset;
            itemLocationStore.$reset;
            locationTownshipProvider.$reset;
            itemCurrencyStore.$reset;
            customFieldStore.$reset;
            // galleryProvider.$reset;
            galleryProvider.resetData()

            for (const customField of customFieldStore.customField?.data?.customList) {
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00001') {
                    customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customField.coreKeysId)[0].provider.$reset;
                }
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00003') {
                    customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customField.coreKeysId)[0].provider.$reset;
                }
                if (customField.isVisible == '1' && customField.isDelete == '0' && customField.uiType.coreKeysId == 'uit00008') {
                    customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === customField.coreKeysId)[0].provider.$reset;
                }
            }
        });


        function checkPriceFormat(data) {
            // alert(data);
            if (appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType == "PRICE_RANGE") {

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
            if (appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType == "NORMAL_PRICE") {
                return data;
            }
            if (appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType == "NO_PRICE") {
                return data;
            }
        }

        function locatorButtonPress() {
            // ps_loading_dialog.value.openModal();
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    Axios.get('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + position.coords.latitude + '&lon=' + position.coords.longitude,
                    ).then(async (response) => {
                        paramHolder1.value.latitude = await response.data.lat.toString();
                        paramHolder1.value.longitude = await response.data.lon.toString();
                        form.product_relation[itemAddressCoreKeysId] = response.data.display_name;
                        isReloadOpenStreetMap.value = false;
                        setTimeout(() => {
                            isReloadOpenStreetMap.value = true;
                        }, 1000);


                    }).catch(error => {
                        ps_loading_dialog.value.closeModal();
                        ps_error_dialog.value.openModal(
                            '',
                            error.message);
                        console.log("sese1");
                    });
                },
                    error => {
                        ps_loading_dialog.value.closeModal();
                        console.log(error.message);
                        locationPermission.value = false;
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    });
            } else {
                alert("Your browser doesn't support geolocation API");
            }

        }

        async function loadItemForEdit() {
            // galleryProvider.offset=0;


            const item = await itemProvider.loadItem(itemId, loginUserId);
            if (item.status == PsStatus.SUCCESS) {
                paramHolder1.value.title = item.data.title;
                paramHolder1.value.catId = item.data.catId;
                paramHolder1.value.catName = item.data.category.catName;

                paramHolder1.value.subCatId = item.data.subCatId;
                paramHolder1.value.subCatName = item.data.subCategory.name;
                // paramHolder1.value.itemTypeId = item.data.itemTypeId;
                // paramHolder1.value.itemTypeName = item.data.itemType.name;
                // paramHolder1.value.conditionOfItemId = item.data.conditionOfItemId;
                // paramHolder1.value.conditionOfItemName = item.data.conditionOfItem.name;
                // paramHolder1.value.brand = item.data.brand;
                // paramHolder1.value.itemPriceTypeId = item.data.itemPriceTypeId;
                // paramHolder1.value.itemPriceTypeName = item.data.itemPriceType.name;


                paramHolder1.value.itemCurrencyId = item.data.itemCurrencyId;
                // alert(item.data.itemCurrency.currencySymbol);
                paramHolder1.value.currencyShortForm = item.data.itemCurrency.currencySymbol;

                paramHolder1.value.price=checkPriceFormat(item.data.originalPrice)


                paramHolder1.value.percent = item.data.percent;

                paramHolder1.value.phone = item.data.phone;
                paramHolder1.value.latitude = item.data.lat;
                paramHolder1.value.longitude = item.data.lng;

                // paramHolder1.value.highlightInformation = item.data.highlightInformation;
                paramHolder1.value.description = item.data.description;

                // paramHolder1.value.dealOptionId = item.data.dealOptionId;
                // paramHolder1.value.dealOptionName = item.data.dealOption.name;
                // paramHolder1.value.remark = item.data.dealOptionRemark;

                paramHolder1.value.itemLocationId = item.data.itemLocationId;
                paramHolder1.value.itemLocationName = item.data.itemLocation.name;
                paramHolder1.value.locationTownshipId = item.data.itemLocationTownshipId;
                paramHolder1.value.locationTownshipName = item.data.itemLocationTownship.townshipName;

                if(item.data.vendor?.id == ''){
                    paramHolder1.value.vendorId = '';
                    paramHolder1.value.vendorName = userStore.user?.data?.userName;

                    defaultProfileId.value = userStore.user?.data?.userId;
                    defaultProfileType.value = 'user';
                }else{
                    paramHolder1.value.vendorId = item.data.vendor?.id;
                    paramHolder1.value.vendorName = item.data.vendor?.name;

                    defaultProfileId.value = item.data.vendor?.id;
                    defaultProfileType.value = 'vendor';

                }


                const productRelation = item.data.productRelation;

                if (productRelation != null && productRelation.length > 0) {
                    productRelation.forEach((customField) => {
                        if (customField.uiTypeId == 'uit00004') {
                            if (customField.value == 1) {
                                form.product_relation[customField.coreKeysId] = true;
                            } else {
                                form.product_relation[customField.coreKeysId] = false;
                            }

                        } else {
                            form.product_relation[customField.coreKeysId] = customField.value;
                        }

                        if (customField.uiTypeId == 'uit00001') {
                            loadCustomField(customField.coreKeysId);
                        }

                    });
                }

                gallery.value = await galleryProvider.loadGalleryList(appInfoStore.appInfo.data.mobileSetting.is_show_item_video, loginUserId, itemId, PsConst.ITEM_TYPE, null);
                // console.log(gallery.value.data);

                if (gallery.value.data != null) {
                    const imgList = gallery.value.data;
                    for (let i = 0; i < imageCount.value; i++) {
                        let id = imgList.filter((image) => image.ordering == i + 1)[0]?.imgId;
                        if (id && id != '') {
                            imgId[i] = id;
                        }

                    }

                    form.item_images = gallery.value.data;
                    galleryLoad.value = true;
                    // console.log(form.item_images);

                    // for( let i = 0; i < imgList.length; i++){
                    //     if(imgList[i].imgType == 'item'){
                    //         imgId[i] = imgList[i].imgId;
                    //     }
                    //         // imgId[i] = imgList[i].imgId;
                    // }

                }

                // paramHolder1.value.businessModeBool = item.data.businessMode == '1';

                // paramHolder1.value.address = item.data.address;
                const myArray = paramHolder1.value.phone.split("#");
                if (myArray.length > 0) {
                    phoneList.shift();
                    if (paramHolder1.value.phone == '' && parseInt(appInfoStore.appInfo.data?.mobileSetting?.phone_list_count) < 1) {
                        phoneShow.value = 0
                    }
                    else {

                        phoneShow.value = myArray.length;
                    }
                }

                // alert(myArray.length);
                myArray.forEach((phone, i) => {
                    let tempPhone = phone.split("-");
                    if (tempPhone.length > 1) {
                        phoneList.push({ 'id': i, 'value': tempPhone[1], 'code': tempPhone[0] });
                    }
                    else {
                        phoneList.push({ 'id': i, 'value': tempPhone[0], 'code': phoneStoredefault.phoneList.data[0].country_code });
                    }
                    // phoneCountryCide.push({'id': i,'value' : tempPhone[1]});
                })


                paramHolder1.value.id = item.data.id;
                dataReady.value = true;
                date_picker.value = true;

            }


        }

        function loadLocation() {
            itemLocationStore.loadItemLocationList(loginUserId, paramHolder);
        }

        function updateCoordinates(location) {
            paramHolder1.value.latitude = location.latLng.lat().toString();
            paramHolder1.value.longitude = location.latLng.lng().toString();

        }

        function filterKeywordUpate(value){
            paramHolder.keyword = value
            itemLocationStore.filterKeywordUpate(loginUserId,paramHolder)
        }

        function filtersubLocationUpdate(value) {
            subparamHolder.keyword = value;
            locationTownshipProvider.filtersubLocationUpdate(loginUserId, subparamHolder);
        }

        function filterSubCatUpdate(value) {
            subcatHolder.keyword = value;
            subCategoryStore.filtersubCatUpdate(loginUserId, subcatHolder);
        }

        function filterPhoneUpdate(value) {
            itemProvider.phoneparamHolder.keyword = value;
            // subcatHolder.keyword = value;
            phoneStore.filterPhoneUpdate(loginUserId, itemProvider.phoneparamHolder);
        }

        function updateLeafletCoordinates(location) {

            paramHolder1.value.latitude = location.lat.toString();
            paramHolder1.value.longitude = location.lng.toString();

        }

        function locationFilterClicked(value) {
            subparamHolder.locationId = value.id;
            paramHolder1.value.itemLocationId = value.id;
            paramHolder1.value.itemLocationName = value.name;
            paramHolder1.value.latitude = value.lat.toString();
            paramHolder1.value.longitude = value.lng.toString();
            paramHolder1.value.locationTownshipId = '';
            paramHolder1.value.locationTownshipName = '';
            validation.value.itemLocationStatus = false;
            isReloadOpenStreetMap.value = false;
            form.product_relation[itemAddressCoreKeysId] = "";
            setTimeout(() => {
                isReloadOpenStreetMap.value = true;
            }, 1000);
        }

        function locationTownshipFilterClicked(value) {


            paramHolder1.value.locationTownshipId = value.id;
            paramHolder1.value.locationTownshipName = value.townshipName;
            paramHolder1.value.latitude = value.lat.toString();
            paramHolder1.value.longitude = value.lng.toString();
            validation.value.locationTownshipStatus = false;
            form.product_relation[itemAddressCoreKeysId] = "";
            isReloadOpenStreetMap.value = false;
            setTimeout(() => {
                isReloadOpenStreetMap.value = true;
            }, 1000);
        }


        function loadCategory() {
            categoryStore.loadItemList(loginUserId, categoryStore.paramHolder);
        }
        function loadPhone() {
            phoneStore.loadPhoneCountryCode(loginUserId, itemProvider.phoneparamHolder);
        }
        function loadSubCategory(catId) {
            if (catId != '') {
                subCategoryStore.loadSubCategoryList(catId);
            }

        }

        function resetSubCategory(catId) {
            if (catId != '') {
                subCategoryStore.resetSubCategoryList(catId);
            }

        }

        function loadLocationTownship(locationId) {
            if (locationId != '') {
                locationTownshipProvider.loadItemLocationTownshipList(locationId);
            }
        }

        function resetLocationTownship(locationId) {
            if (locationId != '') {
                locationTownshipProvider.resetItemLocationTownshipList(locationId);
            }
        }

        function loadCurrency() {
            itemCurrencyStore.loadItemCurrencyList(loginUserId);
        }


        function subCategoryFilterClicked(value) {

            paramHolder1.value.subCatId = value.id;
            paramHolder1.value.subCatName = value.name;
            validation.value.subCatStatus = false;

        }

        function phoneFilterClicked(value, index) {


            phoneList[index]['code'] = value.country_code

            // paramHolder1.value.subCatId = value.id;
            // paramHolder1.value.subCatName = value.name;
            // validation.value.subCatStatus = false;

        }

        if(props.categoryId !== undefined && props.categoryName !== undefined){
            categoryFilterClicked();
        }

        function categoryFilterClicked() {

            subcatHolder.catId = props.categoryId;
            paramHolder1.value.catId = props.categoryId;
            paramHolder1.value.catName = props.categoryName;
            validation.value.catStatus = false;
            paramHolder1.value.subCatId = '';
            paramHolder1.value.subCatName = '';
            subCategoryStore.offset = 0;

        }

        function currencyFilterClicked(value) {
            paramHolder1.value.itemCurrencyId = value.id;
            paramHolder1.value.currencyShortForm = value.currencySymbol;
            validation.value.itemCurrencyStatus = false;
        }

        function priceRangeClicked(value) {
            paramHolder1.value.price = value.id;
            validation.value.itemCurrencyStatus = false;
        }

        function updateLocation(lat, lng) {
            if (lat == null || lng == null) {
                return;
            }

            paramHolder1.value.latitude = lat;
            paramHolder1.value.longitude = lng;
        }


        function defaultClick(index) {

            selectImgIndex.value = index;
            let fileUpload = document.getElementById('fileUpload')
            if (fileUpload != null) {
                fileUpload.click()
            }
            // if(selectImgIndex.value == 1 ){
            //     // this.$refs.image1.$refs.input.click()
            //     image1.value.click();
            // }else{
            //     // this.$refs.image2.$refs.input.click()
            //     image2.value.click();
            // }


        }

        function onImageSelected(event) {
            validation.value.imageStatus = false;

            var obj = { 'id': 0, 'imgUrl': '' };
            obj.id = selectImgIndex.value;
            const selectedFiles = event.target.files;

            // for(let i=0; i<selectedFiles.length; i++) {
            const reader = new FileReader
            reader.onload = e => {
                obj.imgUrl = e.target ? e.target.result ? e.target.result.toString() : '' : '';

                previewImages[selectImgIndex.value - 1] = obj;

            }
            reader.readAsDataURL(selectedFiles[0])

            selectedFile[selectImgIndex.value - 1] = selectedFiles[0];

            // ordering = i;

            // }


        }

        function dragClick(value) {
            // console.log(value);
            // console.log(galleryProvider.galleryList.data);
        }


        function onVideoClick() {
            video.value.click();
        }


        async function submitItem() {

            if(itemId == '0'){
                vendorAutoChooseReset();
            }

            dropzone_loading.value=true;

            // limit ad post check
            if (appInfoStore?.appInfo?.data?.psAppSetting?.isPaidApp == PsConst.ONE && userStore.user?.data?.postCount == PsConst.LIMITED && userStore.user?.data?.remainingPost == '0') {
                limit_item_modal.value.openModal();
            }
            else {

                paramHolder1.value.addedUserId = loginUserId;
                ps_loading_dialog.value.openModal();
                ps_loading_dialog.value.message = trans('item_entry__uploading_item');
                let returnData;
                try {
                    returnData = await itemProvider.submitItemEntry(paramHolder1.value, loginUserId);
                } catch (e) {
                    PsUtils.log(e);
                }

                if (returnData != undefined && returnData.status == PsStatus.SUCCESS) {
                    if (returnData.data.id != '') {




                        // for (let i = 0; i < selectedFile.length; i++) {
                        //     if (selectedFile[i] != undefined) {
                        //         ps_loading_dialog.value.setMessage(trans('item_entry__uploading_image') + (parseInt(i) + 1));

                        //         await itemProvider.postItemImageUpload(loginUserId, itemProvider.item.data.id, PsConst.ITEM_TYPE, parseInt(i) + 1, "Rear Left", selectedFile[i], imgId[i]);
                        //     }
                        // }

                        if (selectedFileVideo != undefined && selectedFileVideo != '') {
                            ps_loading_dialog.value.setMessage(trans('item_entry__uploading_video'));
                            await itemProvider.postItemVideoUpload('', itemProvider.item.data.id, selectedFileVideo, loginUserId);
                        }

                        if (selectedFileVideoThumb != undefined && selectedFileVideoThumb != '') {
                            ps_loading_dialog.value.setMessage(trans('item_entry__uploading_video_thumb'));
                            await itemProvider.postItemVideoThumbUpload(itemProvider.item.data.id, selectedFileVideoThumb, loginUserId);
                        }
                        ps_loading_dialog.value.setMessage(trans('item_entry__uploading_image'), 1);
                        await dropzone_ref.value.autoProcessQueuestart(itemProvider.item.data.id);

                        // console.log(queueFinish.value);

                        if (queueFinish.value == true) {
                            ps_loading_dialog.value.closeModal();
                            // ps_success_dialog.value.openModal(trans('ps_success_dialog__success'), trans('item_upload__success_update'), trans('ps_confirm_dialog__yes'), () => {

                            //     router.get(route('dashboard'));
                            // });

                        }



                        if (queueFinish.value == true) {
                            // Redirect
                            if (itemId != '0') {
                                ps_success_dialog.value.openModal(trans('ps_success_dialog__success'), trans('item_upload__success_update'), trans('ps_confirm_dialog__yes'), () => {

                                    router.get(route('dashboard'));
                                });
                            } else {
                                ps_success_dialog.value.openModal(trans('ps_success_dialog__success'), trans('item_upload__success_upload'), trans('ps_confirm_dialog__yes'), () => {

                                    router.get(route('dashboard'));
                                });

                            }
                        }

                    }
                } else {
                    ps_loading_dialog.value.closeModal();
                    ps_error_dialog.value.openModal('', returnData.message);

                }
                //end item upload
            }
        }

        function getImageUrl(index) {

            if (galleryProvider != null
                && galleryProvider.galleryList != null
                && galleryProvider.galleryList.data != null) {
                const imageName = galleryProvider.galleryList.data.filter((image) => image.ordering == index)[0]?.imgPath;

                if (imageName && imageName != '') {
                    return props.thumb2xUrl + '/' + imageName;
                } else {
                    return '';
                }
                // if(galleryProvider.galleryList.data[index-1].imgPath && galleryProvider.galleryList.data[index-1].imgPath != ''){
                //     // const url = galleryProvider.galleryList.data[index-1].imgPath ?? '';
                //     if(galleryProvider.galleryList.data[index-1].imgPath){
                //         return props.thumb2xUrl + '/' + galleryProvider.galleryList.data[index-1].imgPath;
                //     }else{
                //         return '';
                //     }
                // }else{
                //     return '';
                // }

            } else if (previewImages.data != null && previewImages.data.length >= index) {
                if (previewImages.data[index - 1]?.file == '') {
                    if (previewImages.data[index - 1]?.imgUrl) {
                        return props.thumb2xUrl + '/' + previewImages.data[index - 1]?.imgUrl;
                    } else {
                        return '';
                    }
                } else {
                    return previewImages.data[index - 1]?.imgUrl;
                }

            } else {
                return '';
            }

        }
        function getImageId(index) {

            if (galleryProvider != null
                && galleryProvider.galleryList != null
                && galleryProvider.galleryList.data != null) {
                return galleryProvider.galleryList.data?.filter((image) => image.ordering == index)[0]?.imgId;
            } else {
                return '';
            }

        }

        function getVideoUrl() {
            if (itemProvider != null
                && itemProvider.item != null
                && itemProvider.item.data != null
                && itemProvider.item.data.videoThumbnail != null) {
                if (itemProvider.item.data.videoThumbnail.imgPath && itemProvider.item.data.videoThumbnail.imgPath != '') {
                    if (itemProvider.item.data.videoThumbnail.imgPath) {
                        return props.thumb2xUrl + '/' + itemProvider.item.data.videoThumbnail.imgPath;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }

            } else {
                return '';
            }
        }

        watch(queueFinish, (value) => {


            if (dropzone_loading.value == true && queueFinish.value == true) {
                // alert(queueFinish.value);
                // alert(close);
                ps_loading_dialog.value.closeModal();
                // router.get(route('dashboard'));


                ps_success_dialog.value.openModal(trans('ps_success_dialog__success'), trans('item_upload__success_update'), trans('ps_confirm_dialog__yes'), () => {

                    router.get(route('dashboard'));
                });




            }
        })
        function getVideoId() {
            if (itemProvider != null
                && itemProvider.item != null
                && itemProvider.item.data != null
                && itemProvider.item.data.video != null) {
                return itemProvider.item.data.video.imgId;
            } else {
                return '';
            }
        }
        function getVideoThumbnailId() {
            if (itemProvider != null
                && itemProvider.item != null
                && itemProvider.item.data != null
                && itemProvider.item.data.videoThumbnail != null) {
                return itemProvider.item.data.videoThumbnail.imgId;
            } else {
                return '';
            }
        }

        function onVideoSelected(event) {

            const selectedFiles = event.target.files;

            if (selectedFiles && selectedFiles.length > 1) {
                window.alert("Over 1 Video");
            } else {
                previewVideo.data = [];
                if (selectedFiles && selectedFiles[0]) {
                    const fileReader = new FileReader
                    fileReader.onload = () => {
                        const file = selectedFiles[0];
                        const blob = new Blob([fileReader.result ?? ''], { type: file.type });
                        const url = URL.createObjectURL(blob);
                        const video = document.createElement('video');
                        video.oncanplay = function () {

                            if (video.duration > parseInt(appInfoStore?.appInfo?.data.mobileSetting?.video_duration) / 1000) {
                                selectedFileVideo = undefined;
                                ps_warning_dialog.value.openModal('Video Upload!', "Video must be less than" + videoDurationString.value);
                                return false;
                            } else {

                                const timeupdate = function () {
                                    if (snapImage()) {
                                        video.removeEventListener('timeupdate', timeupdate);
                                        video.pause();
                                    }
                                };
                                video.addEventListener('loadeddata', function () {
                                    if (snapImage()) {
                                        video.removeEventListener('timeupdate', timeupdate);
                                    }
                                });
                                const snapImage = function () {
                                    const canvas = document.createElement('canvas') as HTMLCanvasElement;
                                    canvas.width = video.videoWidth;
                                    canvas.height = video.videoHeight;
                                    canvas.getContext('2d')!.drawImage(video, 0, 0, canvas.width, canvas.height);
                                    const image = canvas.toDataURL();

                                    const fileData = dataURLtoFile(image);

                                    const success = image.length > 100000;
                                    if (success) {
                                        previewVideo.data.push(image);
                                        URL.revokeObjectURL(url);
                                    }
                                    selectedFileVideoThumb = fileData;
                                    return success;

                                };
                                video.addEventListener('timeupdate', timeupdate);

                                selectedFileVideo = selectedFiles[0];

                            }
                        }

                        video.preload = 'metadata';
                        video.src = url;
                        // Load video in Safari / IE11
                        video.muted = true;
                        // video.playsInline = true;
                        video.play();


                    };
                    fileReader.readAsArrayBuffer(selectedFiles[0]);
                }
            }
        }

        function dataURLtoFile(dataurl) {
            let arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            const imgExt = mime.split('/');
            const fileName = Date.now() + '.' + imgExt[1];
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], fileName, { type: mime });
        }

        function validateTitle(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validation.value.titleStatus = false;
            } else {
                validation.value.titleStatus = true;
            }
        }
        function validateDescription(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validation.value.descriptionStatus = false;
            } else {
                validation.value.descriptionStatus = true;
            }
        }

        function validatePhone(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validation.value.contactStatus = false;
            } else {
                validation.value.contactStatus = true;
            }
        }

        function validateBrand(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validation.value.brandStatus = false;
            } else {
                validation.value.brandStatus = true;
            }
        }

        function validatePrice(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validation.value.priceStatus = false;
            } else {
                validation.value.priceStatus = true;
            }
        }

        function validateCustom(id) {
            product_relation_errors.value[id] = '';
        }

        function validatePercent(e) {
            const pattern = /^\/$|(\/[a-zA-Z_0-9-]+)+$/;
            const res = pattern.test(e.target.value);
            if (!res) {
                validation.value.discountStatus = false;
            } else {
                validation.value.discountStatus = true;
            }
        }

        async function submitClicked() {


            let coreKeysIds = Object.keys(form.product_relation);
            let productRelationArr = [];
            coreKeysIds.forEach((coreKeysId) => {
                if(!productRelationArr.includes(coreKeysId)){
                    productRelationArr.push({
                    "core_keys_id": coreKeysId,
                    "value": form.product_relation[coreKeysId]
                });
                }
                // paramHolder1.value.productRelation.push({
                //     "core_keys_id": coreKeysId,
                //     "value": form.product_relation[coreKeysId]
                // })
            });
            paramHolder1.value.productRelation = productRelationArr;
            console.log(paramHolder1.value.productRelation);

            paramHolder1.value.phone = '';

            phoneList.forEach((phone, index) => {
                if (phone.value != '') {
                    paramHolder1.value.phone = paramHolder1.value.phone + phoneList[index].code + '-' + phone.value + '#';
                }

            })

            if (paramHolder1.value.phone != '' && paramHolder1.value.phone.endsWith("#")) {
                paramHolder1.value.phone = paramHolder1.value.phone.slice(0, -1);
            }

            if (paramHolder1.value.lat == '' && paramHolder1.value.lng == '') {
                paramHolder1.value.lat = appInfoStore?.appInfo?.data.psAppSetting?.lat;
                paramHolder1.value.lng = appInfoStore?.appInfo?.data.psAppSetting?.lng;
            }

            if(appInfoStore?.appInfo?.data.psAppSetting?.SelectedPriceType == PsConst.NO_PRICE){
                paramHolder1.value.price = '0';
            }


            paramHolder1.value.imgCaption = form.extra_caption;
            // console.log(paramHolder1.value)

            // if(paramHolder1.value.itemCurrencyId == ''){
            //     // alert("here cuu");

            //     console.log(defaultCurrency);
            //     paramHolder1.value.itemCurrencyId = defaultitemCurrencyStore.itemCurrencyList.data.filter((currency) => currency.currency_symbol == paramHolder1.value.currencyShortForm)[0].id
            // }


            let errorStatus = true;

            // if (previewImages[0].imgUrl == '' && getImageUrl(1) == '') {
            //     validation.value.imageStatus = true;
            //     errorStatus = false;
            // }

            //dropzone image
            if (dropzoneImages.value == 0) {
                validation.value.imageStatus = true;
                errorStatus = false;
            }

            // alert(dropzone_ref.value.currentFiles());

            if (dropzone_ref.value.currentFiles() == 0) {
                validation.value.imageStatus = true;
                errorStatus = false;
            }

            customFieldStore.customField.data?.coreList.forEach((coreField) => {

                if (coreField.fieldName === 'title' && (paramHolder1.value.title == '' || paramHolder1.value.title == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.titleStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'category_id' && (paramHolder1.value.catId == '' || paramHolder1.value.catId == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.catStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'subcategory_id' && (paramHolder1.value.subCatId == '' || paramHolder1.value.subCatId == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.subCatStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'original_price' && (paramHolder1.value.price == '' || paramHolder1.value.price == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.priceStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'percent' && (paramHolder1.value.percent == '' || paramHolder1.value.percent == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.discountStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'location_city_id' && (paramHolder1.value.itemLocationId == '' || paramHolder1.value.itemLocationId == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.itemLocationStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'location_township_id' && (paramHolder1.value.locationTownshipId == '' || paramHolder1.value.locationTownshipId == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.locationTownshipStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'currency_id' && (paramHolder1.value.itemCurrencyId == '' || paramHolder1.value.itemCurrencyId == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    // alert("here");
                    validation.value.itemCurrencyStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'phone' && (paramHolder1.value.phone == '' || paramHolder1.value.phone == undefined) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.contactStatus = true;
                    errorStatus = false;
                }

                if (coreField.fieldName === 'description' && (paramHolder1.value.description == '' || paramHolder1.value.description == undefined ) && coreField.isVisible == '1' && coreField.mandatory == 1) {
                    validation.value.descriptionStatus = true;
                    errorStatus = false;
                }



            });
            customFieldStore.customField.data?.customList.forEach((customField) => {
                if ((form.product_relation[customField.coreKeysId] == '' || form.product_relation[customField.coreKeysId] == undefined) && customField.isVisible == '1' && customField.mandatory == '1' && customField.isDelete == '0') {
                    if(customField.coreKeysId != itemQuantityCoreKeysId){
                        product_relation_errors.value[customField.coreKeysId] = trans('item_entry__field_is_reqiured',{'attribute':customField.name});
                        errorStatus = false;
                    }
                }

                if(itemQuantityCoreKeysId in form.product_relation){
                    if(isNaN(form.product_relation[itemQuantityCoreKeysId])){
                        product_relation_errors.value[itemQuantityCoreKeysId] = customField.name + " field must be number.";
                        errorStatus = false;
                    }
                }

                if(form.product_relation[itemQuantityCoreKeysId] == 0 || form.product_relation[itemQuantityCoreKeysId] < 0 || !form.product_relation[itemQuantityCoreKeysId]){
                    product_relation_errors.value[itemQuantityCoreKeysId] = customField.name + " field is required.";
                    errorStatus = false;
                }
            });
            // End
            if (errorStatus != true) {
                window.scrollTo(0, 0);
                return;
            }

            if(appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType == "NORMAL_PRICE"){
                if(!(parseFloat(paramHolder1.value.price) > 0)){
                    ps_error_dialog.value.openModal('', trans('core__fe_enter_valid_price'));
                    return;
                }
            }

            submitItem();

        }
        function imageDelete(value) {

            if (previewImages[value - 1].imgUrl == '') {
                ps_confirm_dialog.value.openModal(
                    trans('ps_confirm_dialog__confirm'),
                    trans('ps_confirm_dialog__are_you_confirm'),
                    trans('ps_confirm_dialog__yes'),
                    trans('ps_confirm_dialog__no'),
                    () => {
                        imgId[value - 1] = '';
                        doDeleteImage(getImageId(value));
                    },
                    () => {
                        PsUtils.log("Cancel");
                    }
                );
            } else {
                previewImages[value - 1].imgUrl = '';
            }
        }

        function imageOrder(value) {
            paramHolder1.value.imgOrder = value;

        }

        function removeImage(value) {



            if (value != undefined || value != null) {
                var index = form.images.indexOf(value);
                form.images.splice(index, 1);

                // this.$inertia.post(route("item.removeMulti"), {
                //     image:value,
                //     edit_mode:0,
                // }, {

                // });



                axios.post(route('item.removeMulti'), {
                    image: value,
                    edit_mode: 1,
                    img_parent_id: itemId,
                })
                    .then(response => {

                    }).catch(error => { })
            }
            validation.value.maxImgExceed = false
        }
        function maxUpload() {
            validation.value.maxImgExceed = true
        }
        function pushImage(value) {

            // console.log(value);
            validation.value.maxImgExceed = false
            form.images.push(value)
            // console.log(this.files)
        }
        function caption(value) {




            if (itemId != '0') {
                // alert(value);


                // console.log(value);

                let flag = false;

                form.extra_caption.forEach(el => {
                    if (el.name == value.name) {
                        el.value = value.value;

                        flag = true;

                        return false;
                    }
                })

                if (!flag) {
                    form.extra_caption.push({
                        name: value.name,
                        value: value.value,
                    });

                    // paramHolder1.value.imgCaption.push({
                    //     name:value.name,
                    //     value:value.value,
                    // });
                }

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
        }

        function getFileCount(value) {
            // alert(value);
            dropzoneImages.value = value;
        }

        function queueComplete(value) {
            // alert(value);
            queueFinish.value = value;
        }
        function videoDelete() {

            if (previewVideo.data == '') {
                ps_confirm_dialog.value.openModal(
                    trans('ps_confirm_dialog__confirm'),
                    trans('ps_confirm_dialog__are_you_confirm'),
                    trans('ps_confirm_dialog__yes'),
                    trans('ps_confirm_dialog__no'),
                    () => {
                        doDeleteVideo(getVideoId(), getVideoThumbnailId());
                    },
                    () => {
                        PsUtils.log("Cancel");
                    }
                );
            } else {
                previewVideo.data = [];
            }


        }
        async function doDeleteImage(value) {
            ps_loading_dialog.value.openModal();

            const status = await galleryProvider.deleteImage(value, loginUserId);
            PsUtils.log(status);
            await galleryProvider.resetGalleryList(loginUserId, itemId, PsConst.ITEM_TYPE);
            ps_loading_dialog.value.closeModal();
        }
        async function doDeleteVideo(video, icon) {
            ps_loading_dialog.value.openModal();
            const status = await galleryProvider.deleteVideo(video, loginUserId);
            await galleryProvider.deleteVideo(icon, loginUserId);
            PsUtils.log(status);
            itemProvider.item.data.videoThumbnail.imgPath = null;
            ps_loading_dialog.value.closeModal();
        }

        function loadCustomField(coreKeysId) {
            customizeUiStoreList.data.filter((customizeDetail) => customizeDetail.id === coreKeysId)[0]?.provider?.loadCustomFieldList('1', coreKeysId)
        }

        function handleMultiSelect({ data, id }) {
            form.product_relation[id] = data.toString();
            validateCustom(id);
        }

        function selectCustomDropdown(coreKeysId, id) {
            form.product_relation[coreKeysId] = id;
            validateCustom(coreKeysId);
        }

        function removePhone(i) {

            phoneStatus.value = false;
            phoneShow.value = phoneShow.value - 1;
            phoneList.splice(i, 1);

            // setTimeout(() => {
            phoneStatus.value = true;
            // }, 100);

        }

        function addPhone() {
            phoneShow.value = phoneShow.value + 1;
            // phoneStoredefault.loadPhoneCountryCode(loginUserId, itemProvider.phoneparamHolder);
            phoneList.push({ 'id': phoneShow.value, 'value': '', 'code': phoneStoredefault.phoneList.data[0].country_code });

            // phoneCountryCide.push({'id': phoneShow.value,'value' : phoneStoredefault.phoneList.data[0].country_code});

        }

        function vendorAutoChooseReset(){
            if(vendorAutoChoose.value){
                localStorage.defaultProfileId = defaultProfileId.value;
                localStorage.defaultProfileType = defaultProfileType.value;
            }else{
                localStorage.defaultProfileId = '';
                localStorage.defaultProfileType = '';
            }
        }

        function chooseProfile(id, name, type){
            defaultProfileId.value = id;
            defaultProfileType.value = type;

            if(type == 'vendor'){
                paramHolder1.value.vendorId = id;
                paramHolder1.value.vendorName = name;
            }else{
                paramHolder1.value.vendorId = '';
                paramHolder1.value.vendorName = name;
            }
        }


        return {
            locatorButtonPress,
            dropzone_ref,
            isReloadOpenStreetMap,
            phoneStatus,
            removePhone,
            addPhone,
            phoneShow,
            phoneList,
            itemId,
            dataReady,
            paramHolder1,
            categoryStore,
            subCategoryStore,
            userStore,
            locationTownshipProvider,
            itemCurrencyStore,
            appInfoStore, loadPhone,
            autoProcessQueue,
            getFileCount,
            maxUpload,
            imageOrder,
            price_range,
            showDialog,

            ps_warning_dialog,
            map_with_pin_picker_modal,
            ps_loading_dialog,
            ps_error_dialog,
            ps_confirm_dialog,
            locationKeyword,
            sublocationKeyword,
            itemLocationStore,
            loadLocation,

            filterKeywordUpate,
            locationFilterClicked,
            categoryFilterClicked,
            subCategoryFilterClicked,
            locationTownshipFilterClicked,
            currencyFilterClicked,
            submitClicked,
            updateLocation,
            updateCoordinates,
            limit_item_modal, phoneCountryCide, filterPhoneUpdate,

            // onDefaultSelected,
            onImageSelected,
            onVideoSelected,
            defaultClick,
            loadCategory,
            loadSubCategory,
            resetSubCategory,
            loadLocationTownship,
            resetLocationTownship,
            loadCurrency,
            video,
            // isVideoSetting,
            // imageInput,
            // image1,
            // image2,
            onVideoClick,
            checkPriceFormat,

            imageCount,
            previewImages,
            previewVideo,
            phoneFilterClicked,
            priceRangeClicked,

            // onImg2Selected,
            // onImg3Selected,
            // onImg4Selected,
            // onImg5Selected,

            getImageUrl,
            getVideoUrl,
            galleryProvider,
            submitItem,
            itemProvider,
            phoneStore,
            validation,
            validateTitle,
            validateBrand,
            validatePrice,
            validatePercent,
            imageDelete,
            videoDelete,
            PsConst,
            filtersubLocationUpdate,
            subCatKeyword,
            phoneKeyword,
            filterSubCatUpdate,
            // PsConfig,
            updateLeafletCoordinates,
            dragClick,
            gallery,
            // twoObject,
            customFieldStore,
            customizeUiStoreList,
            form,
            product_relation_errors,
            loadCustomField,
            videoDurationString,
            handleMultiSelect,
            validateDescription,
            validatePhone,
            validateCustom,
            selectCustomDropdown,
            ps_success_dialog,
            removeImage,
            pushImage,
            caption,
            galleryLoad,
            dropzoneImages,
            queueComplete,
            queueFinish,
            visible,
            date_picker,
            vendorAutoChoose,
            vendorAutoChooseReset,
            chooseProfile,
            defaultProfileId,
            defaultProfileType,
            itemQuantityCoreKeysId
        }
    }
}

</script>
