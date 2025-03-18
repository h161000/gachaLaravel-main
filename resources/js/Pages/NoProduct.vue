<template>
    <Head title="マイページ" />

    <UserLayout>

        <div class="m-3 px-3 pt-10 text-center h-[400px] text-neutral-600">
            <div v-html="text" class="whitespace-pre-line"> </div>
            

            <div class="mt-8 mb-8 text-center">
                <Link :href="route('main')" class="inline-block items-center m-1 p-0.5 border border-sky-400 bg-sky-400 rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-500 active:bg-sky-500 focus:outline-none focus:border-neutral-900 focus:shadow-outline-neutral transition ease-in-out duration-150">
                    <span class="px-4 py-2 border rounded-full flex">トップページへ</span>
                </Link>
            </div>
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
        text: String,
    },
}
</script>