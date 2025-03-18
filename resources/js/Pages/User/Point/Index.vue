<template>
    <Head title="ポイントを購入する" />

    <UserLayout>
        <div class="absolute top-0 bottom-0 left-0 right-0">

        </div>
        <div class="pt-6 md:px-4 px-2 text-neutral-600">  
            <h1 class="mb-6 font-bold text-center text-lg underline underline-offset-8">&nbsp;&nbsp;&nbsp;ポイントを購入する&nbsp;&nbsp;&nbsp;</h1>
            <!-- <h2 class="text-sm font-semibold mb-1" style="text-align: center; margin-bottom: 1.5rem;">※リロード後にポイント反映されます。</h2> -->
            <div class="grid md:grid-cols-4 sm:grid-cols-3 grid-cols-2 md:px-9 px-3 justify-center md:gap-6 sm:gap-4 gap-2 pb-8">
                <template v-for="(point, key) in points.data">
                    
                    <a :href="route(purchase_uri, {id: point.id})" class="cursor-pointer border-2 border-solid border-[#0ea5e9] bg-white/30 text-center relative rounded-lg shadow-md overflow-hidden hover:shadow-[0_5px_10px_5px_#53ccffaa]">
                        <div class="text-center flex justify-center object-contain aspect-[1.2]">
                            <img class="inline-block" :src="point.image"/> 
                        </div>
                            
                        <div class="flex p-1 px-2 justify-between items-center rounded-b-lg h-12 relative bg-gradient-to-r from-[#00B1FF] to-[#53ccff]" style="z-index: 1;">
                            <span class="text-xs flex items-center justify-center text-white font-bold ">
                                {{point.title}}
                            </span>
                            
                            
                            <button class="rounded-full py-0.5 h-fit text-neutral-600 bg-white px-3 font-bold text-xs" >
                                ¥&nbsp;{{point.amount_str}}
                            </button>
                        </div>
                    </a>
                </template>
            </div>
        </div>

    </UserLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

export default {
    components: {Head, UserLayout, Link},
    data() {
        return {
            is_busy: false,
            is_admin: false,
            purchase_uri: 'user.point.purchase',
        }
    },
    props: {
        errors: Object,
        auth: Object,
        category_share:Object,
        points:Object,
    },
    methods : {
        checkout(id) {
            this.is_busy = true;
            const post_data = { id: id };
            axios.post(route('user.point.checkouturl'), post_data)
            .then(response=>{ 
                this.is_busy = false;
                if(response.status == 201 || response.status == 200) {
                    
                    if(response.data.status) {
                        console.log(response.data.status);
                        window.location.href = response.data.url;
                    } else {
                        alert('サーバーが混み合っております。少し時間をおいて再度お試しください。');        
                    }
                } else {
                    alert('サーバーが混み合っております。少し時間をおいて再度お試しください。');
                }
            }).catch(error=> {
                this.is_busy = false;
                // alert('サーバーが混み合っております。少し時間をおいて再度お試しください。');
            });
        }
    },
    mounted() {
        if (this.auth.user) {
            if (this.auth.user.type==1) {
                this.is_admin = true;
                this.purchase_uri = 'test.user.point.purchase';
            }
        }
    }
}
</script>