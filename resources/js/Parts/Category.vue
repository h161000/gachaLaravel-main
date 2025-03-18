<template>

    <div class="w-full px-2 pt-4 pb-2 text-neutral-700 md:text-base text-sm">
        <div class="w-full mx-auto overflow-x-auto text-center rounded-md shadow-[0_2px_6px_rgba(0,0,0,0.2)] bg-white flex no-scrollbar font-['mplus2'] text-xs md:text-base divide-white divide-x">
            <Link v-for="(category, key) in category_share.categories.data" :href="current_route+'?cat_id='+category.id" class="flex-1" as="button">
                <p class="h-full w-full px-2 py-2 rounded-sm whitespace-nowrap bg-theme-hover hover:text-white" :class="{ 'bg-theme text-white': category.id==category_share.cat_id}">
                    {{category.title}}
                </p>
            </Link>
        </div>
    </div>
<!-- <div class="lg:w-[1024px] w-full mx-auto text-neutral-300 md:text-base text-sm overflow-x-auto py-3 px-3">
        <div class="w-fit flex justify-center flex-shrink-0 gap-2">
            <Link v-for="(category, key) in category_share.categories.data" :href="current_route+'?cat_id='+category.id" class="inline-block border border-[#0ea5e9] rounded-full p-0.5 text-center" as="button">
                <span class="flex justify-center items-center rounded-full py-1 md:px-4 px-2 w-fit" :class="{ 'bg-gradient-to-b from-[#00B1FF] hover:from-[#53ccff] to-[#53ccff] hover:to-[#00B1FF] items-center text-white': category.id==category_share.cat_id}">
                    {{category.title}}
                </span>
            </Link>
        </div>
    </div> -->
    <!-- <div class="w-full pt-2">
        <div class="lg:w-[1024px] w-full mx-auto whitespace-nowrap md:px-4 px-2 text-left overflow-x-auto overflow-y-hidden flex gap-3">
            <Link v-for="(category, key) in category_share.categories.data" :href="current_route+'?cat_id='+category.id" class="inline-block px-3 py-2 border-b-[3px] border-solid" :class="{'border-gray-200 text-gray-300':category.id==category_share.cat_id ^ is_admin==true, 'text-gray-500 border-gray-600':category.id!=category_share.cat_id ^ is_admin==true }">
                {{category.title}}
            </Link>
        </div>
    </div> -->
         
</template>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';


export default {
    components: {Link},
    data(){
        return {
            category_share: usePage().props.value.category_share,
            current_route: "",
        }  
    },
    mounted() {
        let url_str = window.location.href;
        this.current_route = url_str.split("?")[0];
    },
}
</script>