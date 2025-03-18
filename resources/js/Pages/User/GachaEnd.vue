<template>
    <Head title="マイページ" />

    <UserLayout>
        <div class="pt-6 md:px-2 px-4 text-neutral-600 text-center">  
            <h1 class="mb-2 text-base md:text-lg font-bold">ガチャ完了</h1>
            <hr class="mb-8 border-neutral-500" />

            <div v-if="point" class="mb-6 text-center text-sm md:text-base">
                計{{number_products}}点の商品を計{{point}}PTに交換しました。<br/>
                その他の商品は「獲得した商品一覧」に送りしています。
            </div>
            <div v-else class="mb-6 text-center text-sm md:text-base">
                全ての商品を「獲得した商品一覧」にお送りしました。
            </div>

            <div class="mb-8 text-center text-sm md:text-base">
                <h3 class="font-bold mb-2">もう一度ガチャる！</h3>
                <GachaButtons :gacha="gachas.data[0]"/>
            </div>

            <div class="mb-12 text-center text-xs md:text-sm flex flex-wrap justify-center gap-2" >
                <div class="text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit">
                    <Link :href="route('main')" class="inline-block items-center px-5 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-white uppercase tracking-widest transition ease-in-out duration-150">
                        ガチャ選択ページへ
                    </Link>
                </div>

                <div class="text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit">
                    <Link :href="route('user.products')" class="inline-block items-center px-5 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-white uppercase tracking-widest transition ease-in-out duration-150">
                        獲得した商品一覧へ
                    </Link>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import GachaButtons from '@/Parts/GachaButtons.vue';

export default {
    components: {Head, UserLayout, Link, GachaButtons}, 
    props: {
        errors: Object,
        auth: Object,
        number_products: Number,
        point: Number,
        gachas: Object,
    },
}
</script>