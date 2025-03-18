<template>
    <div class="w-full">
    
        <div class="md:w-[768px] w-full mx-auto pt-10 md:px-2 px-4 items-start text-neutral-600 text-sm md:text-base">
            <div class="md:w-[768px] w-full text-center mx-auto pb-2 md:px-2 px-4 flex flex-wrap gap-x-20 gap-y-1 justify-around">
                <Link :href="route('main.terms_conditions')" class="text-center whitespace-nowrap hover:text-white">利用規約</Link>
                <Link :href="route('main.privacy_police')" class="text-center whitespace-nowrap hover:text-white">プライバシーポリシー</Link>
                <Link :href="route('main.notation_commercial')" class="text-center whitespace-nowrap hover:text-white">特定商取引法に基づく表記</Link>        
            </div>
            <hr class="border-neutral-600"/>
            <div class="md:w-[768px] w-full text-center mx-auto pt-1 pb-5 md:px-2 px-4">
                <span class="text-sm md:text-base font-semibold mr-3">Raffle-pro </span> 
                <span class="text-xs md:text-sm">© {{year}} Raffle-pro all rights reserved</span>
            </div>
        </div>
        
    </div>
</template> 




<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';

export default {
    components: {Link},
    data () {
        return {
            category_share: usePage().props.value.category_share,
            url_org: "",
            year : "",
        };
    },
    mounted() {
        let url_str = window.location.href;
        let current_route = url_str.split("?")[0];
        if (current_route.indexOf("/dp") !== -1) {
            this.url_org = route('main.dp') + "?cat_id=";
        } else {
            this.url_org = route('main') + "?cat_id=";
        }

        const d = new Date();
        this.year = d.getFullYear();
    },
}
</script>

