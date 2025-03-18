<template>
    <Head title="獲得した商品一覧" />

    <UserLayout>
        <div class="w-full">  
            <div class="md:px-6 px-4 py-2">
                <h1 class="mb-2 py-4 text-base md:text-lg text-center font-bold underline underline-offset-8 text-neutral-600">
                    獲得した商品一覧</h1>
                <div class="w-full px-2 pb-2 text-neutral-700 text-sm">
                    <div
                        class="w-full mx-auto overflow-x-auto text-center rounded-md shadow-[0_2px_6px_rgba(0,0,0,0.2)] bg-white flex no-scrollbar font-['mplus2'] text-sm md:text-base divide-[#53ccff] divide-x">
                        <Link v-for="(item, key) in main_tab"
                            :href="item.route_url" class="flex-1" as="button">
                        <p class="h-full w-full px-2 py-2 rounded-sm whitespace-nowrap bg-theme-hover hover:text-white"
                            :class="{ 'bg-theme text-white': item.is_active }">
                            {{ item.title }}
                        </p>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="md:px-8 px-6 grid sm:grid-cols-2 gap-2">
                <div v-if="products.data.length" v-for="(item, key) in products.data" class="px-3 py-2 bg-[#53ccff11] hover:bg-[#53ccff33] border border-[#53ccff] rounded-lg">
                    <div class="flex items-start gap-2">
                        
                        <label :for="'checkbox' + item.id"
                            class="cursor-pointer flex text-sm items-center gap-2 w-max flex-1">
                            <img :src="item.image" class="w-20 inline-block object-contain" />

                            <div class="h-full flex flex-col justify-evenly text-neutral-600">
                                <div class="text-sm">{{ item.name }}</div>
                                <div class="text-sm">{{ item.rare }}</div>
                                <div v-if="item.is_lost_product != 2"
                                    class="text-xs flex bg-red-500 justify-center items-center rounded-full py-1 px-2 text-white w-fit gap-1">
                                    <img src="/images/icon_coin.png" class="h-4 w-4" />
                                    <span>{{ item.point }}&nbsp;PT</span>
                                </div>
                                <!-- <div class="text-sm">追跡番号: {{item.status_product}}</div> -->
                            </div>

                        </label>
                    </div>

                </div>
                <div v-else >
                    商品はありません。
                </div>
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
        gacha: Object,
        category_share:Object,
        products: Object,
    },
    data() {
        return {
            main_tab : [
                {title:"未選択", route_url: route('user.products'), is_active:false},
                {title:"発送待ち", route_url: route('user.products.wait'), is_active:false},
                {title:"発送済み", route_url: route('user.products.delivered'), is_active:true},
            ],
        }
    },
}
</script>