<template>
    <ps-modal ref="psmodal" maxWidth="560px" bodyHeight="max-h-full" line="hidden" :isClickOut='false' theme=" px-6 py-6 rounded-lg shadow-xl" class=' z-50 h-56 bg-white'>
        <template #title>
            <div class="px-2 w-full flex flex-row justify-between">
                <ps-label class="text-lg font-semibold">{{$t('core__be_language_string_label')}}</ps-label>
                 <ps-icon @click="closeModal()" name="cross" class="me-1 font-semibold" theme="text-secondary-400" />
            </div>
        </template>
        <template #body>
            <div class="w-full flex flex-col mt-4 mb-4">
                <!-- card body start -->
                <div class="px-2 mt-6 h-76 overflow-y-scroll">
                    <div >
                        <div class="w=full after:flex flex-col items-start justify-start space-y-6">
                            <div v-if="!isCreateUI">
                                <ps-label>{{$t('core__be_key_label')}}<span class="text-red-800 font-medium ms-1">*</span></ps-label>
                                <ps-input type="text" :disabled="true" v-model:value="form.key" :placeholder="$t('core__be_key_placeholder')"/>
                            </div>

                            <div v-for="languageString in languageStrings.data" :key="languageString.id">
                                <div v-if="languageString.language != null">
                                    <ps-label class="text-base mb-2">{{languageString.language.name}}<span class="text-red-800 font-medium ms-1">*</span>
                                    </ps-label> 
                                </div>
                                
                                <ps-input type="text" v-model:value="languageString.value" :placeholder="$t('core__be_value_placeholder')"/>
                            </div>

                           

                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
             <div class="w-full flex flex-row justify-end mb-2.5">
                <ps-button @click="handleCancel()" textSize="text-base" type="reset" class="me-4" colors="text-primary-500" focus="" hover="">{{ $t('core__be_btn_cancel') }}</ps-button>
                <ps-button @click="handleSubmit()" class="transition-all duration-300 min-w-3xs" padding="px-7 py-2" rounded="rounded" hover="" focus="" >
                    <ps-loading v-if="loading" theme="border-2 border-t-2 border-text-8 border-t-primary-500"  loadingSize="h-5 w-5" />
                    <ps-icon v-if="success" name="check" w="20" h="20" class="me-1.5 transition-all duration-300" />
                    <span v-if="success" class="transition-all duration-300">{{ $t('core__be_btn_saved') }}</span>
                    <span v-if="!loading && !success" class="" > {{ $t('core__be_btn_save') }} </span>
                </ps-button>
            </div>
        </template>
    </ps-modal>
</template>

<script lang="ts">
import { defineComponent,ref,reactive } from 'vue';
import PsModal from '@/Components/Core/Modals/PsModal.vue';
import PsLabel from '@/Components/Core/Label/PsLabel.vue';
import PsButton from '@/Components/Core/Buttons/PsButton.vue';
import PsToggle from '@/Components/Core/Toggle/PsToggle.vue';
import PsIcon from "@/Components/Core/Icons/PsIcon.vue";
import PsInput from "@/Components/Core/Input/PsInput.vue";
import PsLoading from "@/Components/Core/Loading/PsLoading.vue";

import { useForm } from '@inertiajs/vue3';

// import { trans } from 'laravel-vue-i18n';

export default defineComponent({
    name : "LanguageUpdateModal",
    components : {
        PsModal,
        PsLabel,
        PsButton,
        PsToggle,
        PsIcon,
        PsInput,
        PsLoading
    },
    setup() {
        const psmodal = ref();
        const languageStrings = reactive({data : []});
        const loading = ref(false);
        const success = ref(false);
        const isCreateUI = ref(false);
        let form = useForm({
            key: "",
            values: [],
        })

        let saveClicked: Function;

        function handleCancel(){
            psmodal.value.toggle(false);
        }

        function openModal(v,updateStr : Function,isCreate = false) {
            isCreateUI.value = isCreate;
            saveClicked = updateStr;
            if(!isCreate && form.key == ""){
                form.key = v;

                axios.post(route('language_string.getLanguageString',form))
                .then(res => {
                    languageStrings.data = res.data;
                    psmodal.value.toggle(true);
                })
                .catch(error => {
                });   
            }else{
                psmodal.value.toggle(true);
            }
        }

        function handleSubmit(){
            psmodal.value.toggle(false);
            for(let i=0;i<languageStrings.data.length;i++){
                form.values.push({value : languageStrings.data[i].value,id : languageStrings.data[i].id, language_id : languageStrings.data[i].language.id, symbol : languageStrings.data[i].language.symbol})
            }
            

            saveClicked(form);

            psmodal.value.toggle(false);

            // this.$inertia.post(route('language_string.updateLanguageStrings'), form, {
            //     forceFormData: true,
            // onBefore: () => {loading.value = true},
            // onSuccess: () => {
            //     loading.value = false;
            //     success.value = true;
            //     setTimeout(()=>{
            //         success.value = false;
            //         psmodal.value.toggle(false);
            //         window.location.reload();
            //     },1000)
            // },
            // onError: () => {
            //     loading.value = false;
            // },
            // });
        }
        function closeModal(){
            psmodal.value.toggle(false);
        }
        return {
            loading, 
            success,
            psmodal,
            openModal,
            form,
            languageStrings,
            handleCancel,
            handleSubmit,
            closeModal,
            isCreateUI
            

        }
    },
})
</script>
