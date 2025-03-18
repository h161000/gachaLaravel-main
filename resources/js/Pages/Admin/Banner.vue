<template>
    <Head title="バナー設定" />

    <AdminLayout>
        <div class="pt-6 md:px-2 px-4">  
            <h1 class="mb-2 text-lg font-bold">バナー設定</h1>
            <hr class="mb-8" />
            <div class="">
                <form @submit.prevent="submit()">
                    <table class="m-auto min-w-full text-sm font-medium">
                        <tbody class="border border-neutral-500">
                            <tr class="border-b">
                                <td class="bg-white px-3 py-2 whitespace-nowrap border-r w-28">バナー画像</td>
                                <td>
                                    <input @change="e => previewImage(e, -1)" ref="file" type="file" class="w-full"/>
                                    <img 
                                        v-if="banner.image != ''"
                                        :src="banner.image"
                                        class="inline-block my-2 w-full min-w-48 max-h-32 object-contain"
                                    />
                                    
                                </td>
                            </tr>
                            <tr class="">
                                <td class="bg-white px-3 py-2 whitespace-nowrap border-r w-28">バナーURL</td>
                                <td class="">
                                    <input v-model="banner.link_url" type="text" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="入力してください"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center py-2">
                        <button type="submit" class="w-28 bg-neutral-500 hover:bg-neutral-700 text-white text-sm font-normal py-2 px-5  rounded">
                            登録
                        </button>
                    </div>
                </form>
            </div>

            <hr class="my-4" />

            <div class="text-center pb-2">
                <div class="border-b border-r border-l bg-neutral-300 p-2">
                    バナー編集
                </div>
                
                <div class="flex mb-1">
                    <draggable class="w-full" :list="banners" @change="log">
                        <div
                            class="my-1 text-left cursor-pointer border border-neutral-500 flex items-center hover:bg-white focus:bg-neutral-100 flex-wrap justify-center"
                            v-for="element in banners"
                            :key="element.id">
                            <div class="flex-1 h-full border-r">
                                <div class="flex">
                                    <div class="flex-1">
                                        <input @change="e => previewImage(e, index)" ref="photo" type="file" class="w-full"/>
                                        <img 
                                            v-if="element.image != ''"
                                            :src="element.image"
                                            class="inline-block my-2 w-full min-w-48 max-h-32 object-contain"
                                        />
                                    </div>
                                </div>
                                <div class="flex w-full">
                                    <div class="flex-1">
                                        <input v-model="element.link_url" type="text" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" placeholder="入力してください"/>
                                    </div>
                                </div>
                            </div>
                            <div class="align-middle h-full flex items-center z-10 gap-1 p-2">
                                <button @click.prevent="levelUp(element.id)" class="bg-neutral-700 rounded p-1 hover:bg-neutral-600"><ArrowLongUpIcon class="w-5 h-5 text-white block" /></button>
                                <button @click.prevent="levelDown(element.id)" class="bg-neutral-700 rounded p-1 hover:bg-neutral-600"><ArrowLongDownIcon class="w-5 h-5 text-white block" /></button>
                                <button @click="destroy(element.id)" class="bg-red-500 hover:bg-red-700 text-white text-sm font-normal py-1 px-2 rounded">削除</button>
                            </div>
                        </div>
                    </draggable>
                </div>
                <div class="p-1 flex justify-center">
                    <button @click.prevent="submit_order()" :class="{ 'opacity-25': order_form.processing }" :disabled="order_form.processing" class="w-28 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-normal py-2 px-3 inline-block rounded">
                        <div class="inline-block">保存</div>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';

import { VueDraggableNext } from 'vue-draggable-next';
import { ArrowLongUpIcon, ArrowLongDownIcon } from '@heroicons/vue/24/solid'


export default {
    components: {Link, Head, AdminLayout, draggable:VueDraggableNext, ArrowLongUpIcon, ArrowLongDownIcon},
    props: {
        errors: Object,
        auth: Object,
        banners: Object,
    },
    methods: {
        log() {

        },
        submit() {
            this.new_form.file = this.$refs.file.files[0];
            this.new_form.link_url = this.banner.link_url;
            this.new_form.post(route('admin.banner.create'),{ onSuccess:()=>this.new_form.reset(),preserveScroll: true});
        },
        destroy(id){
            if (confirm("削除してもいいですか？")) {
                Inertia.delete(route('admin.banner.destroy', id), {preserveScroll: true});
            }
        },
        previewImage(e, index) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                if (index == -1) this.banner.image = URL.createObjectURL(file);
                else this.banners[index].image = URL.createObjectURL(file);
            }
        },
        levelUp(id) {
            let i;
            for(i=1; i<this.banners.length; i++) {
                if (this.banners[i].id==id) {
                    break;
                }
            }
            if (i<this.banners.length) {
                this.arrayMove(i, i-1);
            }
        },
        arrayMove(from, to) {
            this.banners.splice(to, 0, this.banners.splice(from, 1)[0]);
        },
        levelDown(id) {
            let i;
            for(i=0; i<this.banners.length-1; i++) {
                if (this.banners[i].id==id) {
                    break;
                }
            }
            if (i<(this.banners.length-1)) {
                this.arrayMove(i, i+1);
            }
        },
        submit_order() {
            this.order_form.files = this.$refs.photo.map(item => item.files[0]);
            this.order_form.banners = this.banners;
            this.order_form.post(route('admin.banner.store'), {
                preserveScroll: true,
                onFinish: () => {
                }
            });
        }
    },
    data(){
        return {
            open: true,
            dragging: false,
            banner: {
                link_url: "",
                image: ""
            }
        }
    },
    setup(props) {
        const new_form = useForm({
            file: null,
            link_url: null
        });
        const order_form = useForm( {
            files: [],
            banners: [],
        })
        return {new_form, order_form};
    },
    mounted() {
        
    }
}
</script>

