<template>
    <Head title="マイページ" />

    <UserLayout>

        <div class="m-3 p-3 text-center h-[400px] border border-neutral-200 rounded-md">
            このページにアクセスする権限がありません。
        </div>
    </UserLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, UserLayout, Link},
    props: {
        errors: Object,
        auth: Object,
    },
    data(){
        return {
            catagries: ['sdfads', 'abc', 'sdfads', 'sdfads'],
        }  
    },
}
</script>