<template>

    <Head title="ユーザー管理" />

    <AdminLayout>

        <div class="md:px-1 px-2">
            <div class="border-b w-full p-2 my-2 font-semibold">
                <h3>ユーザー管理</h3>
            </div>
            <div class="w-full flex flex-col gap-4 mb-8">
                <div class="w-full flex justify-between gap-4">
                    <div class="flex-1 flex flex-wrap gap-2">
                        <input v-model="form_search.keyword" type="text"
                            class="text-sm flex-1 min-w-96 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                            placeholder="名前、メールアドレス、電話番号、住所を入力します。" />
                        <select v-model="form_search.order_by" id="order_by"
                            class="bg-white/90 flex-1 px-3 py-2 border border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md placeholder-neutral-400">
                            <option value="id">ID</option>
                            <option value="point">ポイント数</option>
                            <option value="amount">購入金額</option>
                        </select>
                    </div>
                    <button type="button" @click="search"
                        class="rounded-md border bg-neutral-600 text-white px-4 py-1">検索</button>
                </div>

                <table class="w-full table-fixed">
                    <thead>
                        <tr class="border-b border-collapse border-sky-100">
                            <th class="text-center py-2 w-16">詳細</th>
                            <th class="text-center py-2 w-24">名前</th>
                            <th class="text-center py-2 w-56">メールアドレス</th>
                            <th class="text-center py-2 w-28">電話</th>
                            <th class="text-center py-2">住所</th>
                            <!-- <th class="text-center py-2">ポイント</th>
                            <th class="text-center py-2">履歴</th> -->
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="user in users"
                            class="border border-sky-100 divide-x-2 divide-sky-50 border-collapse">
                            <td class="text-center py-2">
                                <Link class="px-2 py-0.5 border border-sky-300 rounded-lg hover:bg-sky-300 text-nowrap"
                                    :href="route('admin.users.detail', { id: user.id })">詳細</Link>
                            </td>
                            <td class="text-center py-2 text-red-600 text-nowrap">{{ user.name }}</td>
                            <td class="text-center py-2 max-w-full overflow-hidden whitespace-nowrap text-ellipsis">{{
                                user.email }}</td>
                            <td class="text-center py-2">{{ user.phone }}</td>
                            <td class="text-center py-2 max-w-full overflow-hidden whitespace-nowrap text-ellipsis">{{
                                user.address }}</td>
                            <!-- <td class="text-center py-2">{{ user.point }} pt</td>
                            <td>
                                <div class="flex justify-center items-center py-2">
                                    <Link :href="route('admin.users.purchase_log', user.id)" class="rounded float-right px-3 py-1 mr-2 text-sm bg-cyan-600 hover:bg-cyan-700 text-neutral-50">
                                        購入
                                    </Link>
                                    <Link :href="route('admin.users.gacha_log', user.id)" class="rounded float-right px-3 py-1 text-sm bg-red-500 hover:bg-red-700 text-neutral-50">
                                        ガチャ
                                    </Link>

                                </div>
                            </td> -->
                        </tr>
                    </tbody>
                </table>
                <Pagination :search_cond="search_cond" route_name="admin.users" :total="total"></Pagination>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/Admin.vue';
import Pagination from '@/Parts/Pagination.vue';

import GachaCard from '@/Parts/GachaCard.vue';

export default {
    components: { Head, AdminLayout, Link, GachaCard, Pagination },
    props: {
        errors: Object,
        users: Object,
        search_cond: Object,
        total: Number
    },
    mounted() {

    },
    methods: {
        search() {
            this.form_search.get(route('admin.users'));
        },
    },
    setup(props) {
        const form_search = useForm(props.search_cond);

        return { form_search }
    },
}
</script>