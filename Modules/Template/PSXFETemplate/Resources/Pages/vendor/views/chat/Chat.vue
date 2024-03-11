<template>
    <Head :title="$t('chat__title')"/>
    <ps-content-container class="h-screen">

        <template #content>
            <div class="relative flex flex-col " style="height:90vh" >

                <div class='lg:mt-32 mt-28 h-48 flex flex-col' >
                    <!-- breadcrumb start -->
                    <ps-breadcrumb-2 :items="breadcrumb"></ps-breadcrumb-2>
                    <!-- breadcrumb end -->
                    <div class='flex flex-wrap items-center mb-2 mt-2 p-2 bg-feAchromatic-50  rounded dark:bg-feAchromatic-900'>
                        <!-- <div class=" ms-4 mb-4 flex items-center" >
                            <ps-route-link :to="{name : 'fe_chat_list'}">
                            <ps-label class="hover:underline cursor-pointer font-medium" > {{ $t("chat__chat_list") }} </ps-label>
                            </ps-route-link>
                            <ps-icon name="rightArrow" class='mx-2'/>
                            <ps-label > {{ $t("chat__chat") }} </ps-label>
                        </div> -->

                        <ps-route-link :to="{
                            name: 'fe_other_profile',
                            query: { userId: userStore.user?.data?.userId }}"
                            class="w-16">
                            <div class="flex flex-row content-end items-center cursor-pointer">
                                <img alt="Placeholder"  width="300px" height="200px" class="w-12 h-12 object-cover bg-feAchromatic-50 rounded-full me-2"
                                v-lazy=" { src: $page.props.thumb1xUrl + '/' +userStore.user?.data?.userCoverPhoto, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                >
                            </div>
                        </ps-route-link>

                        <div class="grow">
                            <ps-label class="font-medium text-base"> {{userStore.user?.data?.userName }} </ps-label>
                            <div v-if="isOtherUserOnline" class="flex flex-row items-center">
                                <ps-icon name='dot' w="10" h="10" class=' text-feSuccess-300 me-2'> </ps-icon>
                                <ps-label> {{ $t("chat__online") }} </ps-label>
                            </div>
                            <div v-else class="flex flex-row items-center">
                                <ps-icon name='dot' w="10" h="10" class=' text-feAchromatic-300 me-2'> </ps-icon>
                                <ps-label> {{ $t("chat__offline") }} </ps-label>
                            </div>
                        </div>

                        <!-- <div v-if="chatFlag == PsConst.CHAT_FROM_SELLER && showOffer" class="flex flex-col content-end items-center">
                            <div  >
                                <ps-label class="select-none px-4 py-3 my-auto bg-fePrimary-500 dark:bg-feAccent-500 text-median font-medium text-feAchromatic-50 dark:text-textDare rounded-xl lg:rounded-2xl  cursor-pointer"
                                textColor="text-feAchromatic-50 dark:text-feAchromatic-900"> {{ $t("chat__make") }} <br/> {{ $t("chat__offer") }}</ps-label>
                            </div>
                        </div> -->
                        <!-- divider -->
                        <div class="w-52"></div>
                        <!-- divider -->

                        <div v-if="appInfoStore.appInfo.data?.psAppSetting?.SelectedPriceType != PsConst.PRICE_RANGE">
                            <div v-if="appInfoStore.appInfo.data?.psAppSetting?.SelectedChatType == PsConst.CHAT_AND_OFFER">
                            <ps-button @click.prevent="makeOfferClicked" rounded="rounded" class="mt-2 sm:mt-0" :disabled="(isSoldOut != '1' && showOffer) ? false : true" v-if="chatFlag == PsConst.CHAT_FROM_SELLER">{{$t('chat__make_an_offer')}}</ps-button>
                             </div>
                        </div>
                        <div v-if="appInfoStore.appInfo.data?.psAppSetting?.SelectedChatType == PsConst.CHAT_AND_APPOINTMENT">
                        <ps-button @click.prevent="makeOfferClicked" rounded="rounded" class="mt-2 sm:mt-0" :disabled="(isSoldOut != '1' && showOffer) ? false : true" v-if="chatFlag == PsConst.CHAT_FROM_SELLER">{{$t('chat_book')}}</ps-button>
                        </div>
                    </div>
                    <!-- {{ itemName.split(' ').join('-').toLowerCase() }} -->
                    <div v-if="dataReady" class='flex sm:flex-row justify-between bg-feAchromatic-50 p-2 rounded dark:bg-feAchromatic-900'>
                        <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: itemId }}">
                            <div class='flex flex-row cursor-pointer'>
                                <img alt="Placeholder" class="w-24 h-16 object-cover bg-feAchromatic-50 rounded-md"
                                v-lazy=" { src: $page.props.thumb2xUrl + '/' +itemImageName, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                >
                            </div>
                        </ps-route-link>

                        <div class='grow grid grid-col-4 sm:flex sm:justify-between ms-4'>
                            <div class="col-span-2">
                                <ps-label textColor="font-semibold text-fePrimary-500" class="text-xs sm:text-xl"> {{ itemName }} </ps-label>
                                <ps-label class="font-normal text-lg"> {{ itemCategory }} </ps-label>
                                <ps-label class='font-normal text-xs' textColor="text-feAchromatic-500"> {{ currency }} {{ formatPrice(itemPrice) }}   </ps-label>
                            </div>
                            <div class="col-span-2 sm:hidden flex items-center justify-end">
                                <ps-route-link :to="{ name: 'fe_item_detail', query: { item_id: itemId }}"
                                class="block sm:hidden">
                                    <ps-icon name="doubleRightArrow" ></ps-icon>
                                </ps-route-link>
                            </div>

                            <div class="col-span-4 flex items-center me-2">
                                <ps-button :class="isSoldOut == '1' ? '': 'hidden'" class="h-[20px] sm:h-[28px]" rounded="rounded" :hover="false">{{ $t('chat__mark_sold_out') }}</ps-button>
                                <ps-route-link :to="{ name: 'fe_item_detail',query: {item_id: itemId}}"
                                class="hidden sm:block">
                                    <ps-icon name="doubleRightArrow" ></ps-icon>
                                </ps-route-link>
                            </div>
                        </div>
                    </div>

                </div>


                <!-- <ps-line class="mt-2"/> -->

                <div ref="messages" v-on:scroll="handleScroll" id="messages"
                    class="px-4 flex flex-col-reverse  overflow-y-auto mt-14 sm:mt-3 h-full scrolling-touch">
                    <div v-for="chat in chats.data" :key="chat.id ?? ''">
                        <div class="chat" v-if='chat.message != "" && chat.message != null '>
                            <!-- Chat Date UI -->
                            <div v-if='chat.type == PsConst.CHAT_TYPE_DATE'>
                                <div class="flex w-full justify-center">
                                    <span class="px-4 py-2 mt-2 mb-1 rounded-lg inline-block font-medium ">
                                        <ps-label>
                                             {{
                                                 chat.message
                                              }} </ps-label>
                                    </span>
                                </div>
                            </div>


                            <!-- Chat Normal Text UI -->
                            <div v-else-if="chat.type == PsConst.CHAT_TYPE_TEXT">

                                <!-- Chat Offer UI -->
                                <div v-if="chat.offerStatus == PsConst.CHAT_STATUS_OFFER">
                                    <!-- Send by me UI -->
                                    <div v-if='chat.sendByUserId == loginUserId' class='w-full flex justify-end ps-1/3 flex-row items-end'>
                                        <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50"> {{chat.timeString}} </ps-label>
                                        <span class="px-2 py-4 mt-2 mb-1 rounded-md inline-block rounded-bl-none bg-feInfo-500 ">
                                            <ps-label v-if="isBooking" class="text-center " textColor="text-feAchromatic-50Dark" > {{ $t("chat_book_make_booking") }} <br/>{{ itemPrice == '0' ? $t("item_price__free") : chat.message}} </ps-label>
                                            <ps-label v-else class="text-center " textColor="text-feAchromatic-50Dark" > {{ $t("chat__send_offer") }} <br/>{{ itemPrice == '0' ? $t("item_price__free") : chat.message}} </ps-label>
                                        </span>
                                    </div>

                                    <!-- Receive Message UI -->
                                    <div v-else class="pe-1/3 flex flex-col">
                                        <div  class=" flex flex-row items-end">
                                            <span class="px-2 py-4 mt-2 mb-1 rounded-md inline-block rounded-br-none bg-feInfo-500 ">
                                                <ps-label v-if="isBooking"  textColor="text-feAchromatic-50" > {{ $t("chat_book_received_booking") }} <br/>{{ itemPrice == '0' ? $t("item_price__free") : chat.message}}</ps-label>
                                                <ps-label v-else textColor="text-feAchromatic-50" > {{ $t("chat__receive_offer") }} <br/>{{ itemPrice == '0' ? $t("item_price__free") : chat.message}}</ps-label>
                                            </span>
                                            <br class="sm:hidden">
                                            <ps-label class="ms-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50"> {{chat.timeString}} </ps-label>
                                        </div>
                                    </div>

                                </div>
                                <!-- End Chat Offer UI -->

                                <!-- Reject Offer -->
                                <div v-else-if="chat.offerStatus == PsConst.CHAT_STATUS_REJECT">

                                    <!-- Send by me UI -->
                                    <div v-if='chat.sendByUserId == loginUserId' class='w-full flex justify-end ps-1/3 flex-row items-end'>
                                        <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50"> {{chat.timeString}} </ps-label>
                                        <span class="px-4 py-2 mt-2 mb-1 rounded-lg inline-block rounded-bl-none bg-feError-500 text-feAchromatic-50">
                                            <ps-label v-if="isBooking" class="text-center" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50" > {{ $t("chat_book_reject") }}</ps-label>
                                            <ps-label v-else class="text-center" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50" > {{ $t("chat__offer_rejected_item") }}</ps-label>
                                        </span>
                                    </div>

                                    <!-- Receive Message UI -->
                                    <div v-else class="pe-1/3 flex flex-col">
                                        <div  class=" flex flex-row items-end">
                                            <span class="px-4 py-2 mt-2 mb-1 rounded-lg inline-block rounded-br-none bg-feError-500 ">
                                                <ps-label v-if="isBooking"  textColor="text-feAchromatic-50">  {{ $t("chat_book_reject") }}</ps-label>
                                                <ps-label v-else textColor="text-feAchromatic-50">  {{ $t("chat__offer_rejected_item") }}</ps-label>
                                            </span>
                                            <ps-label class="ms-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50"> {{chat.timeString}} </ps-label>
                                        </div>
                                    </div>

                                </div>
                                <!-- End Reject Offer -->

                                <!-- Chat Accept offer UI -->

                                <div v-else-if="chat.offerStatus == PsConst.CHAT_STATUS_ACCEPT ">

                                    <div v-if='chat.isSold && chat.isUserBought'>

                                    </div>

                                    <div v-else>
                                        <!-- Send by me UI -->
                                        <div v-if='chat.sendByUserId == loginUserId' class="w-full flex flex-col justify-end items-end">
                                            <div  class=' flex flex-row items-end mb-2'>
                                                <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                                <span class="px-2 py-4 rounded-md inline-block rounded-bl-none bg-feSuccess-500">
                                                    <ps-label v-if="isBooking" class="text-center" textColor="text-feAchromatic-50"  >  {{ $t("chat_book_accept") }}</ps-label>
                                                    <ps-label v-else class="text-center  " textColor="text-feAchromatic-50"  >  {{ $t("chat__offer_accepted_item") }}</ps-label>
                                                </span>
                                            </div>

                                            <!-- Item pick up message -->
                                            <div  class=' flex flex-row items-end mb-2' v-if="chat.isUserBought">
                                                <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                                <span class="px-2 py-4 rounded-md inline-block rounded-bl-none bg-feInfo-500">
                                                    <ps-label v-if="isBooking" class="text-center  " textColor="text-feAchromatic-50"  >  {{ $t("chat_book_appoint_done") }}</ps-label>
                                                    <ps-label v-else class="text-center  " textColor="text-feAchromatic-50"  >  {{ $t("chat__offer_item_pickup") }}</ps-label>
                                                </span>
                                            </div>
                                            <!-- Item pick up message -->

                                             <!-- Item out message -->
                                             <div  class=' flex flex-row items-end mb-2' v-if="isSoldOut == '1'">
                                                <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                                <span class="px-2 py-4 rounded-md inline-block rounded-bl-none bg-feError-500">
                                                    <ps-label class="text-center  " textColor="text-feAchromatic-50"  >  {{ $t("chat__offer_item_soldout") }}</ps-label>
                                                </span>
                                            </div>
                                            <!-- Item soldout message -->

                                            <div v-if="chat.offerStatus == PsConst.CHAT_STATUS_ACCEPT && chat.isUserBought == false" class="flex justify-center w-full mt-5">
                                                <!-- <ps-label @click="isUserBought(chat)" class="p-2 flex flex-shrink-0 bg-fePrimary-500 dark:bg-feAccent-500 rounded-lg cursor-pointer " textColor="text-feAchromatic-50 dark:textDark "> {{ $t("chat__user_has_a_pickup") }} </ps-label> -->
                                                <ps-button v-if="isBooking" @click="appointmentDone(chat)" colors="bg-feAchromatic-50 text-fePrimary-500 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" focus="focus:text-feAchromatic-50 focus:bg-fePrimary-500" border="border border-feAchromatic-300 dark:border-feAchromatic-500">
                                                    {{ $t("chat_book_appoint_done") }}
                                                </ps-button>
                                                <ps-button v-else @click="userPickup(chat)" colors="bg-feAchromatic-50 text-fePrimary-500 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" focus="focus:text-feAchromatic-50 focus:bg-fePrimary-500" border="border border-feAchromatic-300 dark:border-feAchromatic-500">
                                                    {{ $t("chat__user_has_a_pickup") }}
                                                </ps-button>
                                            </div>
                                        </div>

                                        <!-- Receive Message UI -->
                                        <div v-else class="pe-1/3 flex flex-col">

                                            <!-- Item accept Message -->
                                            <div  class=" flex flex-row items-end mb-2">
                                                <span class="px-2 py-4 rounded-md inline-block rounded-br-none bg-feSuccess-500 ">
                                                    <ps-label v-if="isBooking" textColor="text-feAchromatic-50"  > {{ $t("chat_book_accept") }}</ps-label>
                                                    <ps-label v-else textColor="text-feAchromatic-50"  > {{ $t("chat__offer_accepted_item") }}</ps-label>
                                                </span>
                                                <ps-label class="ms-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50"> {{chat.timeString}} </ps-label>
                                            </div>
                                            <!-- Item accept message -->

                                            <!-- Item pickup message -->
                                            <div  class=' flex flex-row items-end mb-2' v-if="chat.isUserBought">
                                                <span class="px-2 py-4 rounded-md inline-block rounded-br-none bg-feInfo-500">
                                                    <ps-label v-if="isBooking" class="text-center  " textColor="text-feAchromatic-50"  >  {{ $t("chat_book_appoint_done") }}</ps-label>
                                                    <ps-label v-else class="text-center  " textColor="text-feAchromatic-50"  >  {{ $t("chat__offer_item_pickup") }}</ps-label>
                                                </span>
                                                <ps-label class="ms-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                            </div>
                                            <!-- Item pickup message -->

                                            <!-- Item soldout message -->
                                            <div  class=' flex flex-row items-end mb-2' v-if="isSoldOut == '1'">
                                                <span class="px-2 py-4 rounded-md inline-block rounded-br-none bg-feError-500">
                                                    <ps-label class="text-center  " textColor="text-feAchromatic-50"  >  {{ $t("chat__offer_item_soldout") }}</ps-label>
                                                </span>
                                                <ps-label class="ms-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                            </div>
                                            <!-- Item soldout message -->

                                        </div>
                                    </div>

                                </div>
                                <!-- End Chat Accept offer UI -->

                                <!-- Chat Normal Text UI -->
                                <div v-else>
                                    <!-- Send by me UI -->
                                    <div v-if='chat.sendByUserId == loginUserId' class='w-full flex justify-end ps-1/3 flex-row items-end'>
                                        <!-- <ps-label-caption-2 @click="chatDelete(chat.id)" class="me-2 mb-2 del cursor-pointer bg-feError-500 p-1 rounded text-feAchromatic-50"> Delete </ps-label-caption-2> -->
                                        <div class="">
                                            <ps-label-caption-2 @click="showDeleteDropDown(chat.id)" :class="deleteChatId == chat.id ? '' : 'del'" class="cursor-pointer rounded float-right" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400">
                                                <ps-icon name="verticalThreeDot" class="text-feAchromatic-50Dark"></ps-icon>
                                            </ps-label-caption-2>

                                            <div class="relative">
                                                <div @click="deleteConfirm(chat)" class="bg-feAchromatic-50 shadow-sm relative top-4 right-2 px-1  rounded border transform cursor-pointer text-sm dark:bg-feAchromatic-900 dark:text-feAchromatic-50" v-if="deleteChatId == chat.id">
                                                {{ $t('chat__delete_messages') }}
                                                </div>
                                            </div>

                                            <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                        </div>
                                        <span class="p-4 mt-2 mb-1 rounded-md inline-block rounded-bl-none bg-feSecondary-100 dark:bg-feAchromatic-800">
                                            <ps-label class="text-sm"> {{chat.message}} </ps-label>
                                        </span>
                                    </div>

                                    <!-- Receive Message UI -->
                                    <div v-else class="pe-1/3 flex flex-row items-end">
                                        <span class="p-4 mt-2 mb-1 rounded-md inline-block rounded-br-none  bg-fePrimary-500">
                                            <ps-label textColor="text-feAchromatic-50" class="text-sm"> {{chat.message}} </ps-label>
                                        </span>
                                        <ps-label class="ms-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                    </div>
                                </div>
                            </div>

                            <!-- Is User Bought -->
                            <div v-else-if="chat.type == PsConst.CHAT_TYPE_BOUGHT">

                                <!-- Send by me UI -->
                                <div  class="mt-5 mb-2 grid grid-cols-4 gap-x-4 sm:flex sm:flex-row sm:justify-center  rtl:space-x-reverse sm:space-x-3">
                                    <!-- <div  class='flex  flex-row '>

                                        <span class="px-4 py-2 mt-2 mb-1 rounded-lg inline-block bg-fePrimary-50 dark:bg-feAchromatic-800 ">
                                            <ps-label class="text-center " > {{ $t("chat__item_bought") }} <br/>{{itemPrice == '0' ? $t("item_price__free") : chat.message}} </ps-label>
                                        </span>
                                    </div> -->
                                    <div class="col-span-2">
                                        <ps-button @click="clickGiveReview()" colors="bg-feAchromatic-50 text-fePrimary-500 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" border="border border-feAchromatic-300 dark:border-feAchromatic-500">
                                            {{ $t("chat__leave_a_review") }}
                                        </ps-button>
                                    </div>
                                    <div v-if='chat.sendByUserId == loginUserId && chat.offerStatus == PsConst.CHAT_STATUS_IS_USER_BOUGHT' class="col-span-2" >
                                        <!-- <ps-label @click="markAsSoldClick(chat)" class="p-2 flex flex-shrink-0 bg-fePrimary-500 dark:bg-feAccent-500 cursor-pointer rounded-lg" textColor="text-feAchromatic-50 dark:text-feAchromatic-900"> {{ $t("chat__mark_as_sold") }} </ps-label> -->
                                        <ps-button @click="markAsSoldClick(chat)" class="w-full" colors="bg-fePrimary-500 text-feAchromatic-50 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" border="border border-feAchromatic-300 dark:border-feAchromatic-500">
                                            {{ $t("chat__mark_sold_out") }}
                                        </ps-button>
                                    </div>
                                </div>

                            </div>
                            <!-- sold -->
                            <div v-else-if="chat.type == PsConst.CHAT_TYPE_SOLD">

                                <!-- Send by me UI -->
                                <div  class="w-full mt-5 flex justify-center items-center">

                                    <div >
                                        <ps-button @click="clickGiveReview()" class=" h-9" colors="bg-feAchromatic-50 text-fePrimary-500 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" border="border border-feAchromatic-300 dark:border-feAchromatic-500">
                                            {{ $t("chat__leave_a_review") }}
                                        </ps-button>
                                    </div>

                                </div>

                            </div>

                            <!-- Chat Offer UI -->
                            <div v-else-if="chat.type == PsConst.CHAT_TYPE_OFFER">
                                <!-- Send by me UI -->
                                <div v-if='chat.sendByUserId == loginUserId' class='w-full flex justify-end ps-1/3 flex-row items-end'>
                                    <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                    <span class="px-2 py-4 mt-2 mb-1 rounded-md inline-block rounded-bl-none bg-feInfo-500 ">
                                        <ps-label v-if="isBooking" class="text-center " textColor="text-feAchromatic-50Dark" > {{ $t("chat_book_make_booking") }} <br/>{{itemPrice == '0' ? $t("item_price__free") : chat.message}} </ps-label>
                                        <ps-label v-else class="text-center " textColor="text-feAchromatic-50Dark" > {{ $t("chat__send_offer") }} <br/>{{itemPrice == '0' ? $t("item_price__free") : chat.message}} </ps-label>
                                    </span>
                                </div>

                                <!-- Receive Message UI -->
                                <div v-else class="flex flex-col">
                                    <div  class="flex flex-row items-end">
                                        <span class="px-2 py-4 mt-2 mb-1 rounded-md block rounded-br-none bg-feInfo-500 ">
                                            <ps-label v-if="isBooking" class=" dark:text-feAchromatic-800" textColor="text-feAchromatic-50" > {{ $t("chat_book_received_booking") }} <br/>{{itemPrice == '0' ? $t("item_price__free") : chat.message}}</ps-label>
                                            <ps-label v-else class=" dark:text-feAchromatic-800" textColor="text-feAchromatic-50" > {{ $t("chat__receive_offer") }} <br/>{{itemPrice == '0' ? $t("item_price__free") : chat.message}}</ps-label>
                                        </span>
                                        <ps-label class="ms-2 mb-2 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-50" > {{chat.timeString}} </ps-label>
                                    </div>
                                    <div v-if="chat.offerStatus == PsConst.CHAT_STATUS_OFFER" class="mt-5 mb-2 grid grid-cols-4 gap-4 sm:flex sm:flex-row sm:justify-center  rtl:space-x-reverse sm:space-x-4">
                                        <!-- <ps-label @click="acceptOffer(chat)" class="p-2 bg-feSuccess-600 hover:bg-feSuccess-700 cursor-pointer rounded-lg text-feAchromatic-50" textColor="text-feAchromatic-50"> {{ $t("chat__accept") }} </ps-label>
                                        <ps-label @click="rejectOffer(chat)" class="p-2 bg-feAchromatic-600 hover:bg-feAchromatic-700 cursor-pointer rounded-lg text-feAchromatic-50" textColor="text-feAchromatic-50"> {{ $t("chat__reject") }} </ps-label> -->
                                        <ps-button @click="acceptOffer(chat)" class="col-span-2 sm:w-[140px] h-9" textSize="font-normal text-xxs lg:text-sm" colors="bg-feSuccess-600 cursor-pointer text-feAchromatic-50" rounded="rounded" hover="hover:bg-feSuccess-700" focus="focus:bg-feSuccess-700"> {{ $t("chat__accept") }}</ps-button>
                                        <ps-button @click="rejectOffer(chat)" class="col-span-2 sm:w-[140px] h-9" textSize="font-normal text-xxs lg:text-sm" colors="text-fePrimary-500 dark:bg-feAchromatic-800 dark:text-feAchromatic-200 hover:text-feAchromatic-50" focus="focus:text-feAchromatic-50 focus:bg-fePrimary-500" border="border border-feAchromatic-300 dark:border-feAchromatic-500" rounded="rounded"> {{ $t("chat__reject") }} </ps-button>
                                    </div>
                                </div>

                            </div>
                            <!-- End Chat Offer UI -->

                            <!-- Image UI -->
                            <div v-else-if="chat.type == PsConst.CHAT_TYPE_IMAGE">

                                <!-- Send by me -->
                                <div v-if='chat.sendByUserId == loginUserId' class='w-full flex justify-end ps-1/3 flex-row items-end h-48 mt-4'>
                                    <div class="relative">
                                        <ps-label-caption-2 @click="showDeleteDropDown(chat.id)" :class="deleteChatId == chat.id ? '' : 'del'" class="cursor-pointer rounded float-right" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400">
                                                <ps-icon name="verticalThreeDot" class="text-feAchromatic-50Dark"></ps-icon>
                                        </ps-label-caption-2>

                                        <!-- <div v-if="deleteChatId == chat.id"></div> -->

                                        <div @click="deleteConfirm(chat,chat.message)" class="bg-feAchromatic-50 shadow-sm absolute top-4 right-2 p-1 rounded border cursor-pointer transform" v-if="deleteChatId == chat.id">
                                            {{ $t('chat__delete_messages') }}
                                        </div>

                                        <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                    </div>
                                    <img @click="imageView(chat.message)"  width="300px" height="200px" alt="Placeholder" class="cursor-pointer w-auto h-48 p-2 object-cover  rounded-md"
                                    v-lazy=" { src: $page.props.thumb3xUrl + '/' +chat.message, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                    >
                                </div>

                                <!-- Receive -->
                                <div v-else class="pe-1/3 flex flex-row items-end h-48 mt-4">
                                    <img @click="imageView(chat.message)"  width="300px" height="200px" alt="Placeholder" class="cursor-pointer w-auto h-48 p-2 object-cover rounded-md"
                                    v-lazy=" { src: $page.props.thumb3xUrl + '/' +chat.message, loading: $page.props.sysImageUrl+'/loading_gif.gif', error: $page.props.sysImageUrl+'/default_photo.png' }"
                                    >
                                    <ps-label class="me-2 mb-1 text-xs font-normal" textColor="text-feAchromatic-50Dark dark:text-feAchromatic-400"> {{chat.timeString}} </ps-label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send Message -->
                <div class="mt-2 mb-0 sm:mb-0 absolute inset-y-full w-full">
                    <div class="relative flex pt-1 w-full">
                        <div for="upload-image1" @click="onImageClick">
                        <input class="py-2" type="file" size=1 max=1 accept=".jpg,.jpeg,.png"  @change="onImageSelected($event)" ref="image" id="upload-image1" style="display: none;" :ordering="1" />
                            <button type="button"
                            class="inline-flex items-center justify-center rounded-full h-10 w-10
                            transition duration-500 ease-in-out">
                                <ps-icon name="camera" textColor="text-fePrimary-500 dark:text-fePrimary-500"></ps-icon>
                            </button>
                        </div>
                        <div class="grow relative">
                            <ps-input v-bind:placeholder="$t('chat__type_message')" @keyup.enter="sendNormalMessage(inputMessageController)"  v-model:value="inputMessageController" class="mt-1 "
                            theme="form-input dark:bg-feAchromatic-800 text-feSecondary-500 dark:text-feAchromatic-50"/>

                            <button type="button" @click.prevent="sendNormalMessage(inputMessageController)" class="absolute right-3 top-3 ">
                                <ps-icon name="send" textColor="text-fePrimary-500 dark:text-fePrimary-500"></ps-icon>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </ps-content-container>

    <ps-loading-dialog ref="ps_loading_dialog" />
    <ps-confirm-dialog-2 ref="ps_confirm_dialog" />
    <ps-error-dialog ref="ps_error_dialog" />
    <ps-success-dialog ref="ps_success_dialog" />
    <chat-image-detail ref='chat_image' />
    <offer-modal ref="offer_modal" :price="itemPrice" @submit="submitOffer" />
    <booking-modal ref="booking_modal" :price="itemPrice" @submit="submitBooking" />
    <review-modal ref="review_modal"  />

</template>

<script lang="ts">
import { Head } from '@inertiajs/vue3';
import firebaseApp from 'firebase/app';
import "firebase/auth";
import 'firebase/database';
import { onMounted, onUnmounted, reactive, ref } from 'vue';
import PsInput from '@template1/vendor/components/core/input/PsInput.vue';
import PsLine from '@template1/vendor/components/core/line/PsLine.vue';
import PsContentContainer from "@template1/vendor/components/layouts/container/PsContentContainer.vue";
import PsBreadcrumb2 from "@template1/vendor/components/core/breadcrumbs/PsBreadcrumb2.vue";
import PsButton from '@template1/vendor/components/core/buttons/PsButton.vue';
import PsDropdown from '@template1/vendor/components/core/dropdown/PsDropdown.vue';

// import { useRoute } from 'vue-router'
import PsUtils from '@templateCore/utils/PsUtils';
import PsIcon from '@template1/vendor/components/core/icons/PsIcon.vue';
import PsLabel from '@template1/vendor/components/core/label/PsLabel.vue';
import PsLabelTitle3 from '@template1/vendor/components/core/label/PsLabelTitle3.vue';
import PsRouteLink from '@template1/vendor/components/core/link/PsRouteLink.vue';
import PsLabelCaption2 from '@template1/vendor/components/core/label/PsLabelCaption2.vue';
import PsConst from '@templateCore/object/constant/ps_constants';
import { useNotiStoreState } from "@templateCore/store/modules/noti/NotificationStore";
import { PsValueStore } from '@templateCore/store/modules/core/PsValueStore';
import PsLoadingDialog from '@template1/vendor/components/core/dialog/PsLoadingDialog.vue';
import PsConfirmDialog2 from '@template1/vendor/components/core/dialog/PsConfirmDialog2.vue';
import PsErrorDialog from '@template1/vendor/components/core/dialog/PsErrorDialog.vue';
import PsSuccessDialog from '@template1/vendor/components/core/dialog/PsSuccessDialog.vue';
import ChatImageDetail from '@template1/vendor/components/modules/chat/ChatImageDetail.vue'
import OfferModal from '@template1/vendor/components/modules/chat/OfferModal.vue';
import BookingModal from '@template1/vendor/components/modules/chat/BookingModal.vue';
import ReviewModal from '@template1/vendor/components/modules/review/ReviewModal.vue';
import { useGalleryStoreState } from '@templateCore/store/modules/gallery/GalleryStore';
import PsStatus from '@templateCore/api/common/PsStatus';
import { useUserStore } from '@templateCore/store/modules/user/UserStore';
import GetChatHistoryParameterHolder from '@templateCore/object/holder/GetChatHistoryParameterHolder';
import MakeOfferParameterHolder from '@templateCore/object/holder/MakeOfferParameterHolder';
import { useGetChatHistoryStoreState } from '@templateCore/store/modules/chat/GetChatHistoryStore'
import { usePsAppInfoStoreState } from '@templateCore/store/modules/appinfo/AppInfoStore'
import AppInfoParameterHolder from '@templateCore/object/holder/AppInfoParameterHolder';
import { useChatHistoryListStoreState } from "@templateCore/store/modules/chat/ChatHistoryListStore";
import { useOfferStoreState } from '@templateCore/store/modules/offer/OfferStore';
import ChatParameterHolder from '@templateCore/object/holder/ChatParameterHolder';
import ResetUnReadMessageHolder from '@templateCore/object/holder/ResetUnReadMessageHolder';
import ChatMessage from '@templateCore/object/ChatMessage';
import IsUserBoughtParameterHolder from '@templateCore/object/holder/IsUserBoughtParameterHolder';
import format from 'number-format.js';
import { trans } from 'laravel-vue-i18n';
import { router } from '@inertiajs/vue3';
import PsFrontendLayout from '@template1/vendor/components/layouts/container/PsFrontendLayout.vue';
import { useProductStore } from "@templateCore/store/modules/item/ProductStore";
import { useUserUnReadMessageStore } from "@templateCore/store/modules/chat/UserUnreadMessageStore";
import UserUnReadMessageParameterHolder from '@templateCore/object/holder/UserUnReadMessageParameterHolder';
import moment from 'moment';

export default {
    name: "ChatView",
    components :{
        PsLabel,
        PsInput,
        PsLine,
        PsContentContainer,
        PsIcon,
        PsLabelTitle3,
        PsLabelCaption2,
        PsLoadingDialog,
        OfferModal,
        BookingModal,
        PsConfirmDialog2,
        PsErrorDialog,
        PsSuccessDialog,
        ChatImageDetail,
        PsRouteLink,
        ReviewModal,
        PsBreadcrumb2,
        PsButton,
        PsDropdown,
        Head
    },
    props: ['query'],
    layout: PsFrontendLayout,

    setup(props) {

        const psValueStore = PsValueStore();
        const loginUserId = psValueStore.getLoginUserId();
        const dataReady = ref(false);

        if(loginUserId == null || loginUserId == '' || loginUserId == PsConst.NO_LOGIN_USER) {
            router.get(route('login'));
        }
         const userUnreadHolder = new UserUnReadMessageParameterHolder();

        userUnreadHolder.userId = loginUserId;
        userUnreadHolder.deviceToken = localStorage.deviceToken;

        const productStore = useProductStore();
        const userunreadmsgStore = useUserUnReadMessageStore();


        // Get Data
        const buyerUserId = props.query?.buyer_user_id?.toString() ?? '';
        const sellerUserId = props.query?.seller_user_id?.toString() ?? '';
        const itemName = ref('');
        const itemCategory = ref('');
        const itemId = props.query?.item_id?.toString() ?? '';
        const itemImageName = ref('');
        let itemPrice = ref('');

        const currency = ref('');
        const chatFlag = props.query?.chat_flag?.toString() ?? PsConst.CHAT_FROM_BUYER;
        const isSoldOut = ref('0');
        // function formatPrice(value) {
        //     if(value.toString() == '0') {
        //         return trans('item_price__free');
        //     }else {
        //         return format(appInfoStore.appInfo.data.mobileSetting.price_format, parseFloat(value))
        //     }
        //     return value;
        // }

        const appInfoParameterHolder = new AppInfoParameterHolder();
        appInfoParameterHolder.userId = loginUserId;
        const appInfoStore = usePsAppInfoStoreState();
        const isBooking = ref(false);
        if(appInfoStore?.appInfo?.data?.psAppSetting.SelectedChatType == PsConst.CHAT_AND_APPOINTMENT){
            isBooking.value = true;
        }
        async function loadAppInfo(){
            //await appInfoStore.loadAppInfo(appInfoParameterHolder);
            // itemPrice = format(appInfoStore.appInfo.data.mobileSetting.price_format, parseFloat(itemPrice)).toString();
            // itemPrice = parseFloat(itemPrice).toString();
        }
        loadAppInfo();

        // Init Provider
        const chatHistoryProvider = useGetChatHistoryStoreState();
        const chatHistoryListStore = useChatHistoryListStoreState();
        const galleryStore = useGalleryStoreState();
        const notiStore = useNotiStoreState();
        const offerProvider = useOfferStoreState();
        const chatHistoryHolder = new GetChatHistoryParameterHolder();
        const holder = new MakeOfferParameterHolder();
        const chatHolder = new ChatParameterHolder().ChatParameterHolder();
        const userBoughtHolder = new IsUserBoughtParameterHolder();
        const resetHolder = new ResetUnReadMessageHolder();

        // Prepare Data
        const ps_loading_dialog = ref();
        const ps_confirm_dialog = ref();
        const ps_success_dialog = ref();
        const ps_error_dialog = ref();
        const chat_image = ref();
        const offer_modal = ref();
        const booking_modal = ref();
        const review_modal =ref();
        const showOffer = ref(false);
        const image = ref();
        const isOtherUserOnline = ref(false);
        const sessionId = PsUtils.sortinUserId(sellerUserId, buyerUserId);
        const dataRef = firebaseApp.database().ref('Message/' + sessionId);
        const chatRef = firebaseApp.database().ref('Current_Chat_With');
        const userRef = firebaseApp.database().ref('User_Presence');
        let otherUserId;

        let deleteChatId = ref('');

        if(sellerUserId == loginUserId) {
            otherUserId = buyerUserId;
        }else {
            otherUserId = sellerUserId;
        }
        const userStore = useUserStore('chat');
        // Load User By ID List
        userStore.loadUser(otherUserId);

        //reset unread message
        resetHolder.itemId = itemId;
        resetHolder.buyerUserId = buyerUserId;
        resetHolder.sellerUserId = sellerUserId;
        if(chatFlag == PsConst.CHAT_FROM_SELLER) {
            resetHolder.type = PsConst.CHAT_TO_BUYER;
        } else {
            resetHolder.type = PsConst.CHAT_TO_SELLER;
        }



        // Init Refs
        const inputMessageController = ref('');
        const chats = reactive(
            {
                data : [] as ChatMessage[]
            }
        );

        // Functions
        async function loadChatHistory(){
            chatHistoryHolder.itemId = itemId;
            chatHistoryHolder.buyerUserId = buyerUserId;
            chatHistoryHolder.sellerUserId =sellerUserId;

            await chatHistoryProvider.loadChatHistory(chatHistoryHolder, loginUserId);
            showOffer.value = true;
            if(chatHistoryProvider.chatHistory?.data?.isOffer == '1') {
                showOffer.value = false;
            }
        }

        function handleScroll (evt) {

            if (evt.target.scrollTop < 50) {
                PsUtils.log("load more......");
            }
        }

        function getCurrentDateTimeStamp() {
            const startDate = new Date().getTime();
            return PsUtils.getTimeStampDividedByOneThousand(parseInt(startDate.toString(), 10));
        }

        async function sendNormalMessage(message){

            chatHolder.id = '';
            chatHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
            chatHolder.isSold = false;
            chatHolder.isUserBought = false;
            chatHolder.itemId =itemId;
            chatHolder.message = message;
            chatHolder.offerStatus = PsConst.CHAT_STATUS_NULL;
            chatHolder.sendByUserId = loginUserId;
            chatHolder.sessionId = sessionId;
            chatHolder.type = PsConst.CHAT_TYPE_TEXT;

            await sendMessage(chatHolder,true);
        }

        async function sendMessage(
            chatHolder : ChatParameterHolder ,isSaveInBE = false) {
            let _refKey;
            if( chatHolder.id == ''){
                _refKey = dataRef.push().key ?? "";

            }else{
                _refKey = chatHolder.id;
            }
            const senderId = chatHolder.sendByUserId;
            const data = {
                addedDate: chatHolder.addedDateTimeStamp,
                id: _refKey,
                isSold : chatHolder.isSold,
                isUserBought : chatHolder.isUserBought,
                itemId : chatHolder.itemId,
                message: chatHolder.message,
                offerStatus : chatHolder.offerStatus,
                sendByUserId : chatHolder.sendByUserId,
                sessionId: chatHolder.sessionId,
                type : chatHolder.type
            };
            const chat = {
                itemId : chatHolder.itemId,
                receiverId : otherUserId,
                senderId : senderId
            };

            const chat_user_presence = {
                userId : senderId,
                userName : 'Tester'
            };

            dataRef.child(_refKey).set(data);
            chatRef.child(senderId).set(chat);
            userRef.child(senderId).set(chat_user_presence);

            let isUserOnlineFlag = "0"; // Default offline
            if(isOtherUserOnline.value) {
                isUserOnlineFlag = "1";
            }

            if(isSaveInBE){
                if(chatFlag == PsConst.CHAT_FROM_SELLER) {
                    chatHistoryProvider.postChatHistory(itemId,  buyerUserId, sellerUserId, PsConst.CHAT_TO_SELLER, isUserOnlineFlag ,chatHolder.message, loginUserId);
                }else {
                    chatHistoryProvider.postChatHistory(itemId,  buyerUserId, sellerUserId, PsConst.CHAT_TO_BUYER, isUserOnlineFlag, chatHolder.message, loginUserId );
                }
            }

            inputMessageController.value = '';

            scrollToBottom(700);

        }
        let el;
        function scrollToBottom(time) {

            setTimeout(function(){
                if(el != null) {

                    el.scrollTop = el.scrollHeight + 400
                }else {
                    PsUtils.log("it null");
                }
            }, time );


        }

        function formatPrice(value) {
            if(value.toString() == '0') {
                return trans('item_price__free');
            }else {
                return format(appInfoStore?.appInfo?.data?.mobileSetting?.price_format, value)
            }
        }

        onMounted( async () => {
            const chat_user_presence = {
                userId : loginUserId,
                userName : 'Tester'
            };
            userRef.child(loginUserId).set(chat_user_presence);
            await chatHistoryListStore.resetUnreadMessageCount(loginUserId,resetHolder);
            userunreadmsgStore.postUserUnreadMessageCount(userUnreadHolder);
            await loadChatHistory();
            await productStore.loadItem(itemId,loginUserId);
            itemName.value = productStore.item.data?.title;
            itemCategory.value = productStore.item.data?.category?.catName;
            itemImageName.value = productStore.item.data?.defaultPhoto?.imgPath;
            itemPrice.value = productStore.item.data?.price;
            currency.value = productStore.item.data?.itemCurrency?.currencySymbol;
            isSoldOut.value = productStore.item.data?.isSoldOut;
            dataReady.value= true;

            window.addEventListener('scroll', handleScroll);
            el = document.getElementById('messages')
            dataRef.orderByChild('itemId').equalTo(itemId).on('value', (snapshot) => {
            chats.data = [];
            const data = [] as ChatMessage[];
            let lastDateTime = '';
            let tempShowOffer = showOffer.value;
            snapshot.forEach((doc) => {

                const item = doc.val()
                item.key = doc.key

                if(item.offerStatus == PsConst.CHAT_STATUS_REJECT || item.offerStatus == PsConst.CHAT_STATUS_IS_USER_BOUGHT) {
                    tempShowOffer = true;
                }else if(item.offerStatus == PsConst.CHAT_STATUS_SOLD){
                    tempShowOffer = false;
                    isSoldOut.value = '1';
                }else{
                    tempShowOffer = showOffer.value;
                }
                if(item != null) {
                    item.dateTimeString = PsUtils.timeStampToDateString(item.addedDate);
                    if(item.dateTimeString != null
                        && item.dateTimeString != ''
                        && item.dateTimeString.includes(' ')) {
                            const dateTimeArr = item.dateTimeString.split(' ');
                            item.dateString = dateTimeArr[0];
                            item.timeString = dateTimeArr[1];

                            if(lastDateTime == '' || lastDateTime != item.dateString) {
                                lastDateTime = item.dateString;

                                const date : any = {};
                                date.message = item.dateString;
                                date.type = PsConst.CHAT_TYPE_DATE;
                                data.push(date);
                            }

                    }
                    let chatMessage = new ChatMessage();
                    chatMessage = chatMessage.fromMap(item);

                    data.push(chatMessage);

                }

            });
            showOffer.value = tempShowOffer;
            chats.data = data.reverse();
            scrollToBottom(1500);

            userRef.child(otherUserId).on('value', (snapshot) => {

                if(snapshot.val() != null){

                    const tmpUserId = snapshot.val().userId;
                    if(tmpUserId == otherUserId) {
                        isOtherUserOnline.value = true;
                    }else {
                        isOtherUserOnline.value = false;
                    }
                }else {
                    isOtherUserOnline.value = false;
                }

            });
            });

        });
        onUnmounted(() => {
            chatRef.child(loginUserId).remove();
            userRef.child(loginUserId).remove();
            window.removeEventListener('scroll', handleScroll);
        });

        function onImageClick() {
            image.value.click();

        }

        async function onImageSelected(event) {
            const selectedFiles = event.target.files;

            if(selectedFiles && selectedFiles.length > 1) {
                PsUtils.log(selectedFiles.length);
            }else {
                let selectedFile;
                for(let i=0; i<selectedFiles.length; i++) {

                    selectedFile = selectedFiles[i];
                }

                 if(selectedFile != undefined){
                    ps_loading_dialog.value.openModal();
                    ps_loading_dialog.value.setMessage(trans('chat__uploading_image'));
                    let chatImageType;
                    if(loginUserId == sellerUserId){
                        chatImageType = PsConst.CHAT_TO_BUYER;
                    }
                    else{
                        chatImageType = PsConst.CHAT_TO_SELLER;
                    }
                    let isUserOnlineFlag = "0";
                     if(isOtherUserOnline.value) {
                        isUserOnlineFlag = "1";
                    }
                    const returnData = await galleryStore.postChatImageUpload( loginUserId,
                                                                sellerUserId,
                                                                buyerUserId,
                                                                itemId,
                                                                chatImageType,
                                                                isUserOnlineFlag,
                                                                selectedFile);

                    if(returnData.status == PsStatus.SUCCESS) {
                        chatHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
                        chatHolder.isSold = false;
                        chatHolder.isUserBought = false;
                        chatHolder.itemId = itemId;
                        chatHolder.message = returnData.data.imgPath;
                        chatHolder.offerStatus = PsConst.CHAT_STATUS_NULL;
                        chatHolder.sendByUserId = loginUserId;
                        chatHolder.sessionId = sessionId;
                        chatHolder.type =  PsConst.CHAT_TYPE_IMAGE;

                        await sendMessage(chatHolder,true);

                    }
                    else{
                        PsUtils.log('error');
                    }
                    ps_loading_dialog.value.closeModal();


                }
            }
        }

        function makeOfferClicked(){
            if (appInfoStore?.appInfo?.data?.psAppSetting.SelectedChatType == PsConst.CHAT_AND_APPOINTMENT) {
                booking_modal.value.openModal(
                itemName.value,
                itemCategory.value,
                itemImageName.value,
                currency.value,
                itemPrice.value);
            } else {
                offer_modal.value.openModal(
                itemName.value,
                itemCategory.value,
                itemImageName.value,
                currency.value,
                itemPrice.value);
            }

        }

        async function submitBooking(datetime){
            let isUserOnlineFlag = "0"; // Default offline
            if(isOtherUserOnline.value) {
                isUserOnlineFlag = "1";
            }

            holder.itemId = itemId;
            holder.buyerUserId = buyerUserId;
            holder.sellerUserId = sellerUserId;
            holder.negoPrice = datetime;
            holder.type = PsConst.CHAT_TO_SELLER;
            holder.isUserOnline = isUserOnlineFlag;
            holder.message = datetime;

            ps_loading_dialog.value.openModal();
            const item = await offerProvider.makeOffer(buyerUserId, holder);

            if(item.status == PsStatus.ERROR) {
                PsUtils.log(item.message);
            }
            else{
                chatHolder.id = '';
                chatHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
                chatHolder.isSold = false;
                chatHolder.isUserBought = false;
                chatHolder.itemId = itemId;
                chatHolder.message = datetime;
                chatHolder.offerStatus = PsConst.CHAT_STATUS_OFFER;
                chatHolder.sendByUserId = loginUserId;
                chatHolder.sessionId = sessionId;
                chatHolder.type = PsConst.CHAT_TYPE_OFFER;

                await sendMessage(chatHolder);
            }
            await loadChatHistory();
            ps_loading_dialog.value.closeModal();
        }

        async function submitOffer(negoPrice,currency){
            let isUserOnlineFlag = "0"; // Default offline
            if(isOtherUserOnline.value) {
                isUserOnlineFlag = "1";
            }
            // alert(negoPrice);

            holder.itemId = itemId;
            holder.buyerUserId = buyerUserId;
            holder.sellerUserId = sellerUserId;
            holder.negoPrice = negoPrice;
            holder.type = PsConst.CHAT_TO_SELLER;
            holder.isUserOnline = isUserOnlineFlag;
            holder.message = negoPrice;

            ps_loading_dialog.value.openModal();
            const item = await offerProvider.makeOffer(buyerUserId, holder);

            if(item.status == PsStatus.ERROR) {
                PsUtils.log(item.message);
            }
            else{
                chatHolder.id = '';
                chatHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
                chatHolder.isSold = false;
                chatHolder.isUserBought = false;
                chatHolder.itemId = itemId;
                chatHolder.message = currency + " " + negoPrice;
                chatHolder.offerStatus = PsConst.CHAT_STATUS_OFFER;
                chatHolder.sendByUserId = loginUserId;
                chatHolder.sessionId = sessionId;
                chatHolder.type = PsConst.CHAT_TYPE_OFFER;

                await sendMessage(chatHolder);
            }
            await loadChatHistory();
            ps_loading_dialog.value.closeModal();
        }

        function acceptOffer(chat) {
            ps_confirm_dialog.value.openModal(
                trans('chat__confirm_title'),
                trans('chat__confirm_to_accept_dialog'),
                trans('chat__confirm'),
                trans('chat__cancel'),
                () => {
                    doAcceptOffer(chat);
                },
                () => {
                    PsUtils.log("cancel");
                }
            );
        }

        async function doAcceptOffer( chat){
           let isUserOnlineFlag = "0"; // Default offline
            if(isOtherUserOnline.value) {
                isUserOnlineFlag = "1";
            }

            holder.itemId = itemId;
            holder.buyerUserId = buyerUserId;
            holder.sellerUserId = sellerUserId;
            holder.negoPrice = chat.message;
            holder.type = PsConst.CHAT_TO_BUYER;
            holder.isUserOnline = isUserOnlineFlag;
            holder.message = chat.message;

            ps_loading_dialog.value.openModal();
            const item = await offerProvider.acceptOffer(loginUserId, holder);

            if(item.status == PsStatus.ERROR) {
                PsUtils.log(item.message);
            }
            else{
                chatHolder.id = chat.id;
                chatHolder.isSold = false;
                chatHolder.isUserBought = false;
                chatHolder.itemId = chat.itemId;
                chatHolder.message = chat.message;
                chatHolder.offerStatus = PsConst.CHAT_STATUS_OFFER;
                chatHolder.sendByUserId = chat.sendByUserId;
                chatHolder.sessionId = chat.sessionId;
                chatHolder.type = PsConst.CHAT_TYPE_ACCEPT;
                chatHolder.addedDateTimeStamp = chat.addedDate;

                await sendMessage(chatHolder);  //update message

                const chatAcceptHolder = new ChatParameterHolder().ChatAcceptParameterHolder();
                chatAcceptHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
                chatAcceptHolder.isSold = false;
                chatAcceptHolder.isUserBought = false;
                chatAcceptHolder.itemId = itemId;
                chatAcceptHolder.message = chat.message;
                chatAcceptHolder.offerStatus = PsConst.CHAT_STATUS_ACCEPT;
                chatAcceptHolder.type = PsConst.CHAT_TYPE_ACCEPT;
                chatAcceptHolder.sendByUserId = loginUserId;
                chatAcceptHolder.sessionId = sessionId;
                await sendMessage(chatAcceptHolder);  //accept message
            }
            await loadChatHistory();
            ps_loading_dialog.value.closeModal();
        }

        async function isUserBought( chat){
            let isUserOnlineFlag = "0"; // Default offline
            if(isOtherUserOnline.value) {
                isUserOnlineFlag = "1";
            }

            userBoughtHolder.itemId = itemId;
            userBoughtHolder.buyerUserId = buyerUserId;
            userBoughtHolder.sellerUserId = sellerUserId;
            userBoughtHolder.isUserOnline = isUserOnlineFlag;

            ps_loading_dialog.value.openModal();
            const item = await offerProvider.isUserBought(loginUserId, userBoughtHolder);

            if(item.status == PsStatus.ERROR) {
                PsUtils.log(item.message);
            }
            else{

                chatHolder.addedDateTimeStamp = chat.addedDate;
                chatHolder.isSold = false;
                chatHolder.isUserBought = true;
                chatHolder.id = chat.id;
                chatHolder.itemId = chat.itemId;
                chatHolder.message = chat.message;
                chatHolder.offerStatus = PsConst.CHAT_STATUS_ACCEPT;
                chatHolder.sendByUserId = chat.sendByUserId;
                chatHolder.sessionId = chat.sessionId;
                chatHolder.type = PsConst.CHAT_TYPE_TEXT;
                await sendMessage(chatHolder);  //update message

                const chatMessageHolder = new ChatParameterHolder();
                chatMessageHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
                chatMessageHolder.isSold = false;
                chatMessageHolder.isUserBought = true;
                chatMessageHolder.itemId = itemId;
                chatMessageHolder.message = chat.message;
                chatMessageHolder.offerStatus = PsConst.CHAT_STATUS_IS_USER_BOUGHT;
                chatMessageHolder.sendByUserId = loginUserId;
                chatMessageHolder.sessionId = sessionId;
                chatMessageHolder.type = PsConst.CHAT_TYPE_BOUGHT;


                await sendMessage(chatMessageHolder);  //accept message
            }
            await loadChatHistory();
            ps_loading_dialog.value.closeModal();
        }

        function markAsSoldClick(chat){
            ps_confirm_dialog.value.openModal(
                trans('item_detail__mark_as_sold_out'),
                trans('item_detail__confirm_to_sold_out'),
                trans('chat__confirm'),
                trans('chat__cancel'),
                () => {
                    markAsSold(chat);
                },
                () => {
                    PsUtils.log("Cancel");
                }
                );
        }

        async function markAsSold( chat){

            userBoughtHolder.itemId = itemId;
            userBoughtHolder.buyerUserId = buyerUserId;
            userBoughtHolder.sellerUserId = sellerUserId;

            ps_loading_dialog.value.openModal();
            const item = await offerProvider.markAsSold(loginUserId, userBoughtHolder);

            if(item.status == PsStatus.ERROR) {
                PsUtils.log(item.message);
            }
            else{
                chatHolder.id = chat.id;
                chatHolder.isSold = true;
                chatHolder.isUserBought = true;
                chatHolder.itemId = chat.itemId;
                chatHolder.message = chat.message;
                chatHolder.offerStatus = PsConst.CHAT_STATUS_ACCEPT;
                chatHolder.sendByUserId = chat.sendByUserId;
                chatHolder.sessionId = chat.sessionId;
                chatHolder.type = PsConst.CHAT_TYPE_TEXT;
                chatHolder.addedDateTimeStamp = chat.addedDate;

                await sendMessage(chatHolder);  //update message

                const chatMessageHolder = new ChatParameterHolder();
                chatMessageHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
                chatMessageHolder.isSold = true;
                chatMessageHolder.isUserBought = true;
                chatMessageHolder.itemId = itemId;
                chatMessageHolder.message = chat.message;
                chatMessageHolder.offerStatus = PsConst.CHAT_STATUS_SOLD;
                chatMessageHolder.sendByUserId = loginUserId;
                chatMessageHolder.sessionId = sessionId;
                chatMessageHolder.type = PsConst.CHAT_TYPE_SOLD;

                await sendMessage(chatMessageHolder);  //accept message
            }
            await loadChatHistory();
            ps_loading_dialog.value.closeModal();
        }

        function rejectOffer(chat) {
            ps_confirm_dialog.value.openModal(
                trans('chat__reject_title'),
                trans('chat__confirm_to_reject_dialog'),
                trans('chat__confirm'),
                trans('chat__cancel'),
                () => {

                    doRejectOffer(chat);
                },
                () => {
                   PsUtils.log("cancel");

                }
            );
        }

        function userPickup(chat){
            ps_confirm_dialog.value.openModal(
                trans('chat__user_pickup_title'),
                trans('chat__confirm_to_user_pickup_dialog'),
                trans('chat__confirm'),
                trans('chat__cancel'),
                () => {

                    isUserBought(chat);
                },
                () => {
                   PsUtils.log("cancel");

                }
            );
        }

        function appointmentDone(chat){
            ps_confirm_dialog.value.openModal(
                trans('chat_book_appoint_done'),
                trans('chat__confirm_to_user_appointment_dialog'),
                trans('chat__confirm'),
                trans('chat__cancel'),
                () => {

                    isUserBought(chat);
                },
                () => {
                   PsUtils.log("cancel");

                }
            );
        }

        function deleteConfirm(chat,fileName = ''){
            ps_confirm_dialog.value.openModal(
                trans('chat__delete_message_title'),
                trans('chat__confirm_to_delete_dialog'),
                trans('chat__remove'),
                trans('chat__cancel'),
                () => {
                    if(fileName != ''){
                        chatDelete(chat.id);
                    }
                    else{
                        chatImageDelete(chat.id,fileName);
                    }
                },
                () => {
                   PsUtils.log("cancel");
                   deleteChatId.value = '';
                }
            );
        }



        async function doRejectOffer( chat){

            let isUserOnlineFlag = "0"; // Default offline
            if(isOtherUserOnline.value) {
                isUserOnlineFlag = "1";
            }

            holder.itemId = itemId;
            holder.buyerUserId = buyerUserId;
            holder.sellerUserId = sellerUserId;
            holder.negoPrice = "0";
            holder.type = PsConst.CHAT_TO_BUYER;
            holder.isUserOnline = isUserOnlineFlag;
            holder.message = chat.message;

            ps_loading_dialog.value.openModal();
            const item = await offerProvider.makeOffer(loginUserId, holder);

            if(item.status == PsStatus.ERROR) {
                PsUtils.log(item.message);
            }
            else{
                chatHolder.id = chat.id;
                chatHolder.isSold = false;
                chatHolder.isUserBought = false;
                chatHolder.itemId = chat.itemId;
                chatHolder.message = chat.message;
                chatHolder.offerStatus = PsConst.CHAT_STATUS_OFFER;
                chatHolder.sendByUserId = chat.sendByUserId;
                chatHolder.sessionId = chat.sessionId;
                chatHolder.type = PsConst.CHAT_TYPE_REJECT;
                chatHolder.addedDateTimeStamp = chat.addedDate;
                await sendMessage(chatHolder);  //update message

                const chatRejectHolder = new ChatParameterHolder().ChatAcceptParameterHolder();

                chatRejectHolder.addedDateTimeStamp = getCurrentDateTimeStamp();
                chatRejectHolder.isSold = false;
                chatRejectHolder.isUserBought = false;
                chatRejectHolder.itemId = itemId;
                chatRejectHolder.message = chat.message;
                chatRejectHolder.offerStatus = PsConst.CHAT_STATUS_REJECT;
                chatRejectHolder.type = PsConst.CHAT_TYPE_REJECT;
                chatRejectHolder.sendByUserId = loginUserId;
                chatRejectHolder.sessionId = sessionId;

                await sendMessage(chatRejectHolder);  //reject message
            }
            await loadChatHistory();
            ps_loading_dialog.value.closeModal();
        }

        function imageView(image){
            chat_image.value.openModal(image);
        }

        function clickGiveReview(){

            review_modal.value.openModal(otherUserId,
                () => {
                     ps_error_dialog.value.openModal(trans('chat__review_error'),);

                },
                () => {
                   ps_success_dialog.value.openModal('',trans('chat__review_sent'),)

                },

            )


        }

        async function chatDelete(chatKey){

            await dataRef.child(chatKey).remove();

        }

        async function chatImageDelete(chatKey, fileName){

            await dataRef.child(chatKey).remove();

            galleryStore.postChatImageDelete(fileName, loginUserId);

        }

        // show delete dropdown
        function showDeleteDropDown(chatId){
            if(deleteChatId.value == ''){
                deleteChatId.value = chatId ?? '';
            }
            else{
                deleteChatId.value = '';
            }
        }

        return {
            dataReady,
            chatHistoryProvider,
            userStore,
            sellerUserId,
            handleScroll,
            chats,
            chatFlag,
            sendMessage,
            inputMessageController,
            itemName,
            itemCategory,
            itemId,
            itemImageName,
            itemPrice,
            currency,
            // condition,
            PsConst,
            loginUserId,
            otherUserId,
            isOtherUserOnline,
            onImageClick,
            image,
            onImageSelected,
            ps_loading_dialog,
            ps_confirm_dialog,
            makeOfferClicked,
            offer_modal,
            booking_modal,
            review_modal,
            submitOffer,
            submitBooking,
            offerProvider,
            acceptOffer,
            rejectOffer,
            sendNormalMessage,
            isUserBought,
            markAsSoldClick,
            imageView,
            chat_image,
            showOffer,
            clickGiveReview,
            ps_error_dialog,
            ps_success_dialog,
            formatPrice,
            isSoldOut,
            chatDelete,
            chatImageDelete,
            userPickup,
            appointmentDone,
            deleteChatId,
            showDeleteDropDown,
            deleteConfirm,
            moment,
            appInfoStore,
            isBooking
        };
    },
    computed: {
        breadcrumb(){
            return [
                {
                label: trans("chat__chat_list"),
                url: route("fe_chat_list"),
                },
                {
                label: "Chat",
                color: "text-fePrimary-500",
                },
            ];
        }
    }

}
</script>
