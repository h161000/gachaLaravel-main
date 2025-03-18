<template>

    <Head title="ガチャ 編集" />

    <AdminLayout :closeModal="closeModal">
        <div class="pt-4 md:px-2 px-4">
            <h1 class="mb-1 text-lg font-bold">{{ gacha.id }} - ガチャ 編集</h1>
            <hr class="mb-4" />

            <div class="flex flex-wrap justify-between mb-4 gap-2">
                <div class="text-right">
                    <button v-if="(gacha.status == 1)" type="button" @click="toPublic(0)"
                        :class="{ 'opacity-25': form_to_public.processing }" :disabled="form_to_public.processing"
                        class="inline w-44 py-2.5 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150">
                        非公開にする
                    </button>
                    <button v-else type="button" @click="toPublic(1)"
                        :class="{ 'opacity-25': form_to_public.processing }" :disabled="form_to_public.processing"
                        class="inline w-44 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        公開にする
                    </button>
                </div>

                <div>
                    <button v-if="(gacha.gacha_limit == 1)" type="button" @click="gachaLimit(0)"
                        :class="{ 'opacity-25': form_to_limit.processing }" :disabled="form_to_limit.processing"
                        class="inline w-44 py-2.5 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150">
                        1日1回制限キャンセル
                    </button>
                    <button v-else type="button" @click="gachaLimit(1)"
                        :class="{ 'opacity-25': form_to_limit.processing }" :disabled="form_to_limit.processing"
                        class="inline w-44 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        1日1回制限設定
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit()">
                <div class="mb-4">
                    <label for="point" class="block font-medium text-sm text-neutral-700 mb-1">消費ポイント（テキスト）<span
                            class="text-red-500">*</span></label>
                    <input v-model="form.point" id="point" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.point" class="text-red-500 text-sm mt-1">
                        {{ errors.point }}
                    </div>
                </div>

                <div class="mb-4">
                    <label for="count_card" class="block font-medium text-sm text-neutral-700 mb-1">総カード数（半角数字）<span
                            class="text-red-500">*</span></label>
                    <input v-model="form.count_card" id="count_card" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.count_card" class="text-red-500 text-sm mt-1">
                        {{ errors.count_card }}
                    </div>
                </div>

                <div class="mb-4">
                    <label for="count_card" class="block font-medium text-sm text-neutral-700 mb-1">ガチャ試行回数</label>
                    <multiselect v-model="this.buttons" tag-placeholder="Add this as new tag" placeholder=""
                        :options="[]" :multiple="true" :taggable="true" label="label" track-by="value" @tag="addTag">
                    </multiselect>
                </div>

                <div class="mb-4">
                    <label for="spin_limit" class="block font-medium text-sm text-neutral-700 mb-1">購入回数制限を <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.spin_limit" id="spin_limit" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.spin_limit" class="text-red-500 text-sm mt-1">
                        {{ errors.spin_limit }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-sm text-neutral-700 mb-2 ml-1">発売時刻</label>
                    <div class="w-full flex flex-wrap gap-3 items-center">
                        <VueDatePicker v-model="form.start_time" format="Y-MM-dd HH:mm" class="flex-1"></VueDatePicker>
                    </div>
                </div>


                <div class="mb-4">
                    <label for="file1" class="block font-medium text-sm text-neutral-700 mb-1">サムネイル <span
                            class="text-red-500">*</span></label>
                    <div class="text-center w-full">
                        <img v-if="url1" :src="url1" class="inline-block mt-4 h-52" />
                    </div>
                    <input @change="previewImage1" ref="photo1" id="file1" type="file" class="w-full" />
                    <div v-if="errors.thumbnail" class="text-red-500 text-sm mt-1">
                        {{ errors.thumbnail }}
                    </div>
                </div>

                <div class="mb-8">
                    <label for="file1" class="block font-medium text-sm text-neutral-700 mb-1">詳細ページ画像 <span
                            class="text-red-500">*</span></label>
                    <div class="text-center w-full">
                        <img v-if="url" :src="url" class="inline-block mt-4 h-52" />
                    </div>
                    <input @change="previewImage" ref="photo" id="file1" type="file" class="w-full" />
                    <div v-if="errors.image" class="text-red-500 text-sm mt-1">
                        {{ errors.image }}
                    </div>
                </div>

                <div class="mb-8 text-center flex gap-2 justify-center">
                    <button type="submit"
                        class="inline-block items-center px-20 py-2 bg-green-500 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        保存
                    </button>
                    <Link :href="route('admin.gacha', { cat_id: gacha.category_id })" as="button"
                        class="inline-block items-center px-20 py-2 bg-rose-500 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-rose-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                    戻る
                    </Link>
                </div>
            </form>

            <h3 class="mb-2 text-lg font-bold">ラストワン賞 登録</h3>
            <hr />
            <div class="mb-6 py-2 border-neutral-200 border-b flex flex-wrap justify-center items-center gap-2">
                <div v-if="product_last.id" class="flex-1 flex border-neutral-200 items-center">
                    <img :src="product_last.image" class="w-20 h-20 inline-block object-contain" />
                    <div class="flex flex-col justify-evenly text-sm py-1 px-2 ">
                        <div>ID: {{ product_last.product_id }}</div>
                        <div>{{ product_last.name }}</div>
                        <div>{{ product_last.rare }}</div>
                        <div class="text-red-500">{{ product_last.point }}pt</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button v-if="product_last.name" type="button" @click="modalProductLastOpen()"
                        class="items-center px-8 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        編集
                    </button>

                    <button v-else type="button" @click="modalProductLastOpen()"
                        class="items-center px-28 py-2 my-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        追加
                    </button>

                    <button v-if="product_last.name" @click="destroy_product(product_last.id)" type="button"
                        class="items-center px-8 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                        削除
                    </button>
                </div>
            </div>

            <h3 class="flex-1 mb-2 text-lg font-bold">カード 登録</h3>
            <hr />
            <div v-for="(item, key) in products"
                class="py-2 flex justify-between border-neutral-200 border-b items-center flex-wrap gap-2">
                <div class="flex border-neutral-200 gap-2">
                    <div class="flex items-center gap-2">
                        <img :src="item.image" class="w-20 h-20 inline-block object-contain" />
                        <div class="flex flex-col justify-evenly text-sm text-wrap overflow-marquee">
                            <div>ID: {{ item.product_id }}</div>
                            <div>{{ item.name }}</div>
                            <div>{{ item.rare }}</div>
                            <div>
                                {{ item.count }}
                                <span v-if="item.order > 0">( {{ item.order }}番 )</span>
                            </div>
                            <div class="text-red-500">{{ item.point }}pt</div>
                        </div>
                    </div>
                </div>

                <div class="text-center flex flex-wrap gap-2">
                    <button type="button" @click="modalProductOpen(item)"
                        class="inline-block items-center px-8 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        編集
                    </button>

                    <button type="button" @click="destroy_product(item.id)"
                        class="inline-block items-center px-8 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                        削除
                    </button>
                </div>
            </div>
            <div class="text-center my-4">
                <button type="button" @click="modalProductOpen(0)"
                    class="inline-block items-center px-28 py-2 bg-cyan-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150">
                    追加
                </button>
            </div>

        </div>


        <template>
            <TransitionRoot as="template" :show="open">
                <Dialog as="div" class="relative z-10" @close="open = false" :open="open">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                    </TransitionChild>

                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                            <TransitionChild as="template" enter="ease-out duration-300"
                                enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                                leave-from="opacity-100 translate-y-0 sm:scale-100"
                                leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                <DialogPanel
                                    class="p-3 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                    <form @submit.prevent="submit_product()">
                                        
                                        <div class="mb-4">
                                            <label for="product_id"
                                                class="block font-medium text-sm text-neutral-700 mb-1">カードID<span
                                                    class="text-red-500">*</span></label>
                                            <input v-model="form_product.product_id" id="product_id" type="number"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="入力してください" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.product_id }}
                                            </div>
                                        </div>

                                        <div v-if="form_product.is_last == 0" class="mb-4">
                                            <label for="count"
                                                class="block font-medium text-sm text-neutral-700 mb-1">枚数（半角数字）<span
                                                    class="text-red-500">*</span></label>
                                            <input v-model="form_product.count" id="count" type="number"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="入力してください" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.count }}
                                            </div>
                                        </div>

                                        <div v-if="form_product.is_last == 0 && form_product.count == 1" class="mb-4">
                                            <label for="text1-4"
                                                class="block font-medium text-sm text-neutral-700 mb-1">排出順序</label>
                                            <input v-model="form_product.order" id="text1-4" type="number"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="入力してください" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.order }}
                                            </div>
                                        </div>

                                        
                                        <div class="mb-6 text-center">
                                            <button type="submit"
                                                class="inline-block items-center px-8 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                                                保存
                                            </button>

                                            <button type="button" @click="open = false"
                                                class="inline-block items-center px-8 py-2.5 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
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
import { Head, useForm, usePage, Link } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';
import Multiselect from 'vue-multiselect';

import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import 'vue-multiselect/dist/vue-multiselect.css'


export default {
    components: { Head, AdminLayout, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot, ExclamationTriangleIcon, XMarkIcon, VueDatePicker, Link, Multiselect },
    props: {
        errors: Object,
        gacha: Object,
        category_share: Object,
        product_last: Object,
        products: Object,
        // productsLostSetting: Object,
    },
    data() {
        return {
            url: null,
            url1: null,
            url2: null,
            open: false,
            buttons: [],
        }
    },
    methods: {
        addTag(val) {
            val = parseInt(val)
            if (val && !this.buttons.find(item => item.value == val)) {
                this.buttons.push({
                    label: val + (val > 1 ? '連' : '回'),
                    value: val
                });
                this.buttons.sort(function (a, b) { return a.value - b.value });
            }
        },
        destroy_product(id) {
            if (confirm("削除してもいいですか？")) {
                Inertia.delete(route('admin.gacha.product.destroy', { id: id }), { preserveScroll: true });
            }
        },
        modalProductLastOpen() {
            if (this.product_last.id) {
                this.form_product.id = this.product_last.id;
                this.form_product.product_id = this.product_last.product_id;
                this.form_product.is_update = 1;
            } else {
                this.form_product.id = "";
                this.form_product.product_id = "";
                this.form_product.is_update = 0;
            }
            this.form_product.is_last = 1;
            this.form_product.count = 1;
            this.form_product.order = 0;
            this.open = true;
        },
        modalProductOpen(product) {
            if (product) {
                this.form_product.id = product.id;
                this.form_product.product_id = product.product_id;
                this.form_product.count = product.count;
                this.form_product.order = product.order;
                this.form_product.is_update = 1;
            } else {
                this.form_product.id = "";
                this.form_product.product_id = "";
                this.form_product.count = 1;
                this.form_product.order = 0;
                this.form_product.is_update = 0;
            }
            this.form_product.is_last = 0;
            this.open = true;
        },
        submit_product() {
            if (this.form_product.is_last == 1) {
                this.form_product.post(route('admin.gacha.product.create'), {
                    preserveScroll: true,
                    onFinish: () => { }
                });
            } else {
                if (this.form_product.count != 1) this.form_product.order = 0;

                if (this.form_product.order > 0 && this.form_product.order <= this.gacha.count) {
                    alert('このガチャはすでに ' + this.gacha.count + ' 回されています。 ' + this.gacha.count + ' 以上を設定する必要があります。');
                    return;
                }
                if (this.form_product.is_update == 0 && this.form_product.order > 0 && this.products.find((item) => { return item.order == this.form_product.order; })) {
                    alert('この順番にカードがあります。');
                    return;
                }
                this.form_product.post(route('admin.gacha.product.create'), {
                    preserveScroll: true,
                    onFinish: () => {

                    }
                });
            }

        },

        closeModal() {
            this.open = false;
        },

        submit() {
            let count_rest = this.form.count_card - this.gacha.count;
            
            let count_product = 0;
            for (let i = 0; i < this.products.length; i++) {
                count_product = count_product + parseInt(this.products[i].count);
            }

            if (confirm(`残り口数 ${count_rest} ${count_rest == count_product ? '=' : '≠'} カードの残り数 ${count_product}`)) {
                if (this.$refs.photo) {
                    this.form.image = this.$refs.photo.files[0];
                }
                if (this.$refs.photo1) {
                    this.form.thumbnail = this.$refs.photo1.files[0];
                }
                this.form.buttons = this.buttons.map(item => item.value).join(',');
                this.form.post(route('admin.gacha.update'), { preserveScroll: true });
            }
        },

        previewImage(e) {
            const file = e.target.files[0];
            this.url = URL.createObjectURL(file);
        },

        previewImage1(e) {
            const file = e.target.files[0];
            this.url1 = URL.createObjectURL(file);
        },

        previewImage2(e) {
            const file = e.target.files[0];
            this.url2 = URL.createObjectURL(file);
        },

        toPublic(to_status) {
            this.form_to_public.to_status = to_status;
            this.form_to_public.post(route('admin.gacha.to_public'), {
                preserveScroll: true,
                onFinish: () => {

                },
            });
        },

        gachaLimit(to_status) {
            this.form_to_limit.to_status = to_status;
            this.form_to_limit.post(route('admin.gacha.gacha_limit'), {
                preserveScroll: true,
                onFinish: () => {

                },
            });
        },

    },
    setup(props) {
        const form = useForm({
            id: props.gacha.id,
            point: props.gacha.point,
            count_card: props.gacha.count_card,
            count: props.gacha.count,
            lost_product_type: props.gacha.lost_product_type,
            thumbnail: '',
            image: '',
            category_id: props.category_share.cat_id,
            spin_limit: props.gacha.spin_limit,
            start_time: props.gacha.start_time,
            end_time: props.gacha.end_time,
            buttons: props.gacha.buttons
        });

        let form_product_data = {
            id: "",
            gacha_id: props.gacha.id,
            product_id: "",
            is_last: "",
            order: "",
            count: "",
            is_update: 0,
        };

        const form_product = useForm(form_product_data);

        const form_to_public = useForm({ gacha_id: props.gacha.id, to_status: 1 });

        const form_to_limit = useForm({ gacha_id: props.gacha.id, to_status: 1 });
        return { form, form_product, form_to_public, form_to_limit }
    },
    mounted() {
        this.url = this.gacha.image;
        this.url1 = this.gacha.thumbnail;

        this.buttons = this.gacha.buttons ? this.gacha.buttons.split(',').map(val => {
            return {
                label: val + (val > 1 ? '連' : '回'),
                value: val
            }
        }) : []
    },
}
</script>