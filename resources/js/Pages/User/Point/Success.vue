<template>
    <Head title="ポイント購入完了" />

    <UserLayout>
        <div class="pt-6 md:px-2 px-4 text-neutral-300">
            <h1 class="mb-2 text-lg font-bold">ポイント購入完了</h1>
            <hr class="mb-8" />
        
            <div class="pt-4 mb-4 text-sm text-center">
                購入は完了しました。<br/>
                購入ページに
            </div>
            <div  class="text-center">
                <Link :href="route('user.point')" class="inline-flex items-center px-16 py-2.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-800 active:bg-red-800 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"> リダイレクトする </Link>
            </div>
        </div>

    </UserLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, UserLayout, Link},
    props: {
        errors: Object,
        auth: Object,
        category_share:Object,
        points:Object,
    }, 
    methods : {
        redirection() {
            window.location.href = route('user.point');
        },
    },
    mounted() {
        setTimeout(this.redirection, 5000);
    }
}
</script>