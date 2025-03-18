<template>
    <Head title="ガチャ" />

    <UserLayout :hide_footer="true">
        <main class="w-full flex-1">
            <div class="w-full h-full overflow-y-auto">
                <div class="md:w-[768px] w-full mx-auto">
                    <div class="mx-auto relative" style="max-width:430px; ">
                        <div class="flex p-6 fixed w-full max-w-[430px] justify-end">
                            <button class="rounded-full py-0.5 w-fit px-4 text-sm border border-gray-400 bg-white items-center flex">
                                <span class="text-sm">残&nbsp;&nbsp;</span>
                                <span class="font-semibold text-xl">{{format_number(gachas.data[0].count_rest)}}</span> 
                                <span class="text-sm">/{{format_number(gachas.data[0].count_card)}}</span>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            <!-- </button>
                            <button class="rounded-full py-1 text-white w-[135px] text-sm border-2 border-white bg-sky-500"> -->
                                <img src="/images/icon_coin.png" class="w-5 h-5"/>&nbsp;&nbsp;
                                <span class="font-semibold text-2xl">{{format_number(gachas.data[0].point)}}</span> 
                                <span>PT</span>
                            </button>
                        </div>
                        <img :src="gachas.data[0].image" class="block w-full"/>
                    </div>
                </div>
            </div>
            
        </main>

        
    </UserLayout>
    <div class="fixed w-full bottom-0 z-20" v-if="(gachas.data[0].count_rest>0 && gachas.data[0].productCount!=0)">
        <GachaButtons :gacha="gachas.data[0]"/>
    </div>
</template>

<script>
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Footer from '@/Parts/Footer.vue';
import GachaButtons from '@/Parts/GachaButtons.vue';


export default {
    components: {Head, UserLayout, Footer, Link, GachaButtons},
    props: {
        errors: Object,
        gachas: Object,
        category_share:Object,
    },
    data() {
        return {
          
        }
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1,');
        }, 
    },
}
</script>

