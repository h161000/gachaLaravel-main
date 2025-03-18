<template>
    <div v-if="banners && banners.length > 0" class="md:w-[768px] w-full mx-auto py-2 border-t border-b">
        <carousel :items-to-show="1" :autoplay="3000" :wrapAround="banners.length > 1" class="w-full">
            <template v-for="banner in banners">
                <slide class="w-full max-h-[100vh]">
                    <img class="w-full cursor-pointer h-full object-contain" :src="banner.image" @click="openInNewTab(banner.link_url)"/>
                </slide>
            </template>

        </carousel>
    </div>

    <!--
    <div v-if="is_gacha" class="md:w-[768px] mx-auto md:px-0 px-1 py-0 justify-between" style="display: grid;gap: 10px;grid-auto-flow: column;grid-template-columns: 1fr 1fr;">
        <Link :href="url_gacha" class="font-bold h-12 text-base text-center py-2.5" :class="is_gacha==1?'text-white bg-red-500' : 'text-black bg-gray-200'">ガチャ</Link>
        <Link :href="url_dp" class="font-bold h-12 text-base text-center py-2.5" :class="is_gacha!=1? 'text-white bg-red-500' : 'text-black bg-gray-200'">TP交換所</Link>
    </div>
    -->
</template>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

export default {
    components: {
        Link,
        Carousel,
        Slide,
        Pagination,
        Navigation
    },
    props: {
        
        // is_gacha: Number,
    }, 
    data(){
        return {
            banners: []
        }  
    },
    mounted() {
        this.banners = usePage().props.value.banners;
    },
    methods: {
        openInNewTab : (url) => {
            window.open(url, '_blank').focus();
        }
    }
}
</script>