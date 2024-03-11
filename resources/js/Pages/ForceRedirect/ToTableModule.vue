<template>

    <div  :dir="getDir()" :class="isDarkMode ? 'dark' : ''">
        <div class="min-h-screen flex flex-col justify-center items-center">
            <div class="mb-8 w">
                <version-update />
            </div>
            <div class="mb-1">
                <ps-label-title>{{ $t('your_version_update_is_almost_done') }}</ps-label-title>
            </div>
            <div class="mb-6">
                <ps-label textColor="text-secondary-500 dark:text-secondary-100">{{ $t('pls_import_zip_file_from_psx_builder') }}</ps-label>
            </div>
            <ps-button @click="goToTable()" textSize="text-xs lg:text-sm" class="" > {{ $t('go_to_import') }} </ps-button>
        </div>
    </div>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import PsLabelTitle from "@/Components/Core/Label/PsLabelTitle.vue";
import PsLabel from "@/Components/Core/Label/PsLabel.vue";
import PsButton from "@/Components/Core/Buttons/PsButton.vue";
import VersionUpdate from "@/Components/Svgs/VersionUpdate.vue";
import { trans } from 'laravel-vue-i18n';

export default defineComponent({
    components: {
        PsLabelTitle,
        PsLabel,
        PsButton,
        VersionUpdate
    },
    setup(){
        function getDir(){
            if(localStorage.activeLanguage != null && localStorage.activeLanguage != undefined && localStorage.activeLanguage == 'ar'){
                return 'rtl';
            }else{
                return 'ltr';
            }
        }
        return{
            getDir
        }
    },
    methods: {
        goToTable() {
            this.$inertia.get(route('table.index'));
        }
    },
})
</script>
