<template>
    <Head title="カード設定" />

    <AdminLayout :closeModal="closeModal">
        <div class="pt-2 px-5 mx-auto md:w-[768px]">  
            <div class="text-red-600" style="white-space: break-spaces;">{{ get_product_status() }}</div>
            <div class="flex items-center gap-2 w-full flex-wrap mb-2 justify-center border p-2 text-sm">
                <div class="flex flex-col gap-2 flex-1">
                    <div class="flex flex-wrap gap-2">
                        <div class="flex items-center gap-2 flex-1 min-w-40">
                            <label class="w-16 text-right">ID</label>
                            <input v-model="form_search.id" type="text" class="h-9 flex-1 w-0 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                        </div>
                        <div class="flex items-center gap-2 flex-1 min-w-40">
                            <label class="w-16 text-right">名前</label>
                            <input v-model="form_search.name" type="text" class="h-9 flex-1 w-0 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                        </div>
                        <div class="flex items-center gap-2 flex-1 min-w-40">
                            <label class="w-16 text-right">レア度</label>
                            <input v-model="form_search.rare" type="text" class="h-9 flex-1 w-0 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-16 text-right">ポイント</label>
                        <input v-model="form_search.min" type="number" class="h-9 flex-1 w-16 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                        <span>~</span>
                        <input v-model="form_search.max" type="number" class="h-9 flex-1 w-16 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"/>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <button type="button" @click="clear" class="rounded-md border bg-neutral-600 text-white px-4 h-9">初期化</button>
                    <button type="button" @click="search" class="rounded-md border bg-neutral-600 text-white px-4 h-9">検索</button>
                </div>
            </div>
            <Pagination :search_cond="search_cond" route_name="admin.product" :total="total"></Pagination>
            <div v-for="(item, key) in products.data" class="py-2 flex justify-between border-neutral-200 border-b items-center flex-wrap gap-2">
                <div class="flex border-neutral-200 items-center">
                    <img :src="item.image" class="w-20 h-20 inline-block object-contain"/>
                    <div class="flex flex-col justify-evenly text-sm px-2 text-wrap overflow-marquee">
                        <div>ID: {{item.id}}</div>
                        <div>{{item.name}}</div>
                        <div>{{item.rare}}</div>
                        <div>{{item.count}}</div>
                        <div class="text-red-500">{{item.point}}pt</div>
                    </div>
                </div>
            
                <div class="text-center flex flex-wrap gap-2">
                    <button type="button" @click="modalProductOpen(item)" class="inline-block items-center px-8 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        編集
                    </button>

                    <button type="button" @click="destroy_product_last(item.id)" class="inline-block items-center px-8 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                        削除
                    </button>
                </div>
            </div>
            
            <div class="text-center my-6">
                <button type="button" @click="modalProductOpen(0)" class="inline-block items-center px-10 py-2.5 bg-cyan-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    追加
                </button>
            </div>
            
        </div>

        <template>
        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="relative z-10" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <DialogPanel class="p-3 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form @submit.prevent="submit_last()">
                        <div class="mb-4">
                            <label  for="text1-1" class="block font-medium text-sm text-neutral-700 mb-1">名前<span class="text-red-500">*</span></label>
                            <input v-model="form.name" id="text1-1" type="text" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="入力してください" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.name }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label  for="text1-2" class="block font-medium text-sm text-neutral-700 mb-1">変換ポイント（半角数字）<span class="text-red-500">*</span></label>
                            <input v-model="form.point" id="text1-2" type="number" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" placeholder="入力してください" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.point }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label  for="text1-3" class="block font-medium text-sm text-neutral-700 mb-1">レア度<span class="text-red-500">*</span></label>
                            <input v-model="form.rare" id="text1-3" type="text" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" placeholder="入力してください" />
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.rare }}
                            </div>
                        </div>


                        <div class="mb-4">
                            <label  for="text1-3" class="block font-medium text-sm text-neutral-700 mb-1">枚数（半角数字）<span class="text-red-500">*</span></label>
                            <input v-model="form.count" id="text1-4" type="number" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300" placeholder="入力してください" required/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.count }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="file1-1" class="block font-medium text-sm text-neutral-700 mb-1">画像アップロード <span class="text-red-500">*</span></label>
                            <div class="text-center w-full">
                                <img 
                                    v-if="url2"
                                    :src="url2"
                                    class="inline-block mt-4 h-24"
                                />
                            </div>
                            <input @change="previewImage2" ref="photo2" id="file1-1" type="file" class="w-full"/>
                            <div class="text-red-500 text-sm mt-1">
                                {{ errors.image }}
                            </div>
                        </div>
                        <div class="mb-6 text-center">
                            <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="inline-block items-center px-8 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                                保存
                            </button>

                            <button type="button" @click="open = false" class="inline-block items-center px-8 py-2.5 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                キャンセル
                            </button>
                        </div>
                        </form>
                    </DialogPanel>
                </TransitionChild>
                </div>
            </div>
            </Dialog>
        </TransitionRoot>
        </template>

    </AdminLayout>
</template>

<script>
import { Head, useForm, usePage } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';
import Pagination from '@/Parts/Pagination.vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

export default {
    components: {Head, AdminLayout, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot, Pagination},
    props: {
        errors: Object,
        category_share:Object,
        products: Object,
        products_status: Object,
        search_cond: Object,
        total: Number
    },
    data() {
        return {
            url2: null,
            open: false,

            point_value: 0,
        }
    },
    
    methods: {
        get_product_status() {
            let res = this.products_status.map((item)=>{
                return (parseInt(item.count) < parseInt(item.sum) ? `IDが${item.id}のカードが ${item.sum} つ必要です。   現在の在庫数は ${item.count}つです。\n` : "")
            })

            let text = "";
            res.forEach((item)=>{
                text = text + item;
            })

            return text;
        }, 
        destroy_product_last(id) {
            if (confirm("削除してもいいですか？")) {
                Inertia.delete(route('admin.product.destroy', {id:id}), {preserveScroll: true});
            }
        },
        modalProductOpen(product) {
            if (product) {
                this.form.id = product.id;
                this.form.name = product.name;
                this.form.point = product.point;
                this.form.rare = product.rare;
                this.form.count = product.count;
                this.form.image = "";
                this.form.is_update = 1;
                this.url2 = product.image;
            } else {
                this.form.id = "";
                this.form.name = "";
                this.form.point = "";
                this.form.rare = "";
                this.form.count = "";
                this.form.image = "";
                this.form.is_update = 0;
                this.url2 = "";
            }
            this.open = true;
        },
        submit_last() {
            if (this.$refs.photo2.files[0]) {
                this.form.image = this.$refs.photo2.files[0];
            } 
            this.form.post(route('admin.product.create'), {
                preserveScroll: true,
                onFinish: () => {},
            }); 
        },  

        previewImage2(e) {
            const file = e.target.files[0];
            this.url2 = URL.createObjectURL(file);
        },
        closeModal() {
            this.open = false;
        },
        search() {
            this.form_search.get(route('admin.product'));
        },
        clear() {
            this.form_search.id = "";
            this.form_search.rare = "";
            this.form_search.name = "";
            this.form_search.min = "";
            this.form_search.max = "";
        }
    },
    setup(props) {
        let form_data = {
            id: "",
            name: "",
            point: "",
            rare: "",
            count: "",
            image: "",
            is_update: 0,
        };
        const form = useForm( form_data );

        const form_search = useForm(props.search_cond);

        return { form, form_search }
    },
    mounted() {
    },
}
</script>