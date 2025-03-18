<template>

    <Head title="ガチャ管理" />

    <AdminLayout :closeModal="closeModal">

        <div>
            <div class="px-6 sm:px-4 mt-2 mb-12 sm:grid-cols-2 grid-cols-1 grid gap-4">
                <template v-for="(gacha, key) in gachas.data">
                    <GachaCard :url_edit="route('admin.gacha.edit', gacha.id) + category_share.cat_route_appendix"
                        :gacha="gacha" />
                </template>

                <Link :href="route('admin.gacha.create') + category_share.cat_route_appendix"
                    class="rounded-lg border-2 border-[#0ea5e9] shadow w-full min-h-[300px] flex items-center justify-center">
                <div class="rounded-full bg-sky-500 text-4xl text-center  text-neutral-100 h-16 w-16 pt-2.5">
                    +
                </div>
                </Link>

            </div>
            <div class="text-center mb-12 flex justify-center gap-3">
                <Link :href="(route('admin.product') + category_share.cat_route_appendix)"
                    class="inline-block items-center w-36 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                カード編集
                </Link>

                <Link :href="(route('admin.gacha.sorting') + category_share.cat_route_appendix)"
                    class="inline-block items-center w-36 py-2.5 bg-cyan-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150">
                並び替え
                </Link>
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
                                    class="p-3 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                                    <form @submit.prevent="submit()">

                                        <div class="mb-4">
                                            <div class="flex justify-between items-center">
                                                <label for="gacha_csv"
                                                    class="block font-medium text-neutral-700 mb-2">ガチャリストcsv
                                                </label>

                                                <a class="rounded-md bg-sky-500 text-white text-sm px-4 py-1 my-1"
                                                    :href="route('admin.gacha.csv_sample')">csv形式のダウンロード</a>
                                            </div>

                                            <input id="gacha_csv" ref="gacha_csv" type="file" class="w-full" />

                                        </div>
                                        <div class="mb-4">
                                            <div class="flex justify-between items-center">
                                                <label for="product_csv" class="block font-medium text-neutral-700 mb-2">カードcsv
                                                </label>
                                                <a class="rounded-md bg-sky-500 text-white text-sm px-4 py-1 my-1"
                                                    :href="route('admin.gacha.product_csv_sample')">csv形式のダウンロード</a>
                                            </div>

                                            <input id="product_csv" ref="product_csv" type="file" class="w-full" />

                                        </div>

                                        <div class="text-center">
                                            <button type="submit"
                                                class="inline-block items-center px-8 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                                                保存
                                            </button>

                                            <button type="button" @click="open = false"
                                                class="inline-block items-center px-8 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
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
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/Admin.vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

import GachaCard from '@/Parts/GachaCard.vue';

export default {
    components: { Head, AdminLayout, Link, GachaCard, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot },
    props: {
        errors: Object,
        auth: Object,
        gachas: Object,
        category_share: Object,
    },
    data() {
        return {
            open: false,
            id: ""
        }
    },
    mounted() {

    },
    setup(props) {
        const form = useForm({
            gacha_csv: '',
            product_csv: ''
        });
        return { form };
    },
    methods: {
        submit() {
            this.form.gacha_csv = this.$refs.gacha_csv.files[0];
            this.form.product_csv = this.$refs.product_csv.files[0];
            this.form.post(route('admin.gacha.csv_post'));
        },
        closeModal() {
            this.open = false;
        }
    }
}
</script>