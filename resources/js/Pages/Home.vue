<template>
    <Head >
        <title>ネットオリパ・オンラインオリパ</title>
    </Head>

    <UserLayout>
        <div class="px-4 py-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <template v-for="(gacha, key) in gachas.data">
                <GachaCard v-if="gacha.count_rest" :gacha="gacha"/>
            </template> 
            <template v-for="(gacha, key) in gachas.data">
                <GachaCard v-if="!gacha.count_rest" :gacha="gacha"/>
            </template>
        </div>
        
    </UserLayout>  
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Branch from '@/Parts/Branch.vue';
import GachaCard from '@/Parts/GachaCard.vue';

export default {
    components: {Head, UserLayout, Link, Branch, GachaCard},
    props: {
        errors: Object,
        auth: Object,
        gachas:Object,
        category_share:Object,
    },
}
</script>