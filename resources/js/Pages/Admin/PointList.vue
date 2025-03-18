<template>
    <Head title="ポイント設定" />

    <AdminLayout> 
        
        <div class="pt-6 md:px-2 px-4">  
            <h1 class="mb-2 text-lg font-bold">ポイント設定</h1>
            <hr class="mb-8" />
            <div class="flex flex-wrap justify-evenly">
                <div v-for="(point, key) in points.data" class="mb-4 bg-white text-center relative rounded-lg shadow-md mx-2 flex flex-col" style="width:200px;">
                    <div class="flex justify-between z-[5]">
                        <Link :href="route('admin.point.edit', {'id':point.id}) + category_share.cat_route_appendix" class="rounded px-4 py-2 bg-green-500 text-xs text-neutral-50">
                        編集する
                        </Link>

                        <button @click="destroyPoint(point.id)" class="rounded px-3 py-2 bg-red-500 text-xs text-neutral-50">
                            削除
                        </button>
                    </div>
                    <div class="text-center pt-2 px-1 -my-4 flex justify-center">
                        <img class="inline-block object-contain" :src="point.image"/> 
                    </div>
                    <div class="z-[5] text-sm px-3 flex items-end justify-center font-bold">
                        {{point.title}}
                    </div>
                    <div class="z-[5] pt-1 pb-2">
                        <button class="rounded-full py-0.5 bg-neutral-800 text-neutral-100 w-[100px] text-xs" >
                            ¥ {{point.amount_str}}
                        </button>
                    </div>
                    
                </div>
                <Link :href="route('admin.point.create') + category_share.cat_route_appendix" class="mb-4 bg-white text-center relative rounded-lg shadow-md flex justify-center items-center" style="width:200px; min-height: 200px;">
                    <div class="rounded-full bg-green-500 text-3xl   text-neutral-100 h-10 w-10" >
                        +
                    </div>
                </Link>
            </div>
        </div>

    </AdminLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';

export default {
    components: {Head, AdminLayout, Link},
    props: {
        errors: Object,
        auth: Object,
        category_share:Object,
        points:Object,
    },
    methods : {
        destroyPoint(id) {
            if (confirm("削除してもいいですか？")) {
                Inertia.delete(route('admin.point.destroy', {'id': id}), {preserveScroll: true});
            }
        },
    },
    data(){
        return {
            // catagries: ['sdfads', 'abc', 'sdfads', 'sdfads'],
        }  
    },
}
</script>