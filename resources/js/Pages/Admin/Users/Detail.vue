<template>

    <Head title="ユーザー管理" />

    <AdminLayout>

        <div class="md:px-1 px-2">
            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between">
                <h3>ユーザー詳細 (ID-{{ user.id }})</h3>
                <button onclick="history.back()"
                    class="rounded-md px-2 text-sm font-normal text-white bg-red-500 hover:bg-red-400">
                    戻る
                </button>
            </div>

            <table class="w-full text-sm">
                <template v-if="user">
                    <tr>
                        <td class="p-1 w-2/5 font-bold">メールアドレス</td>
                        <td class="p-1 w-3/5">{{ user.email }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">電話番号</td>
                        <td class="p-1 w-3/5">{{ user.phone }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">ポイント</td>
                        <td class="p-1 w-3/5">
                            <input
                                class="bg-white/90 w-full px-3 py-2 border border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md placeholder-neutral-400"
                                type="number" v-model="form.point" />
                        </td>
                    </tr>
                </template>
                <template v-if="profile">
                    <tr>
                        <td class="p-1 w-2/5 font-bold">名前</td>
                        <td class="p-1 w-3/5">{{ profile.first_name }} {{ profile.last_name }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">名前(カナ)</td>
                        <td class="p-1 w-3/5">{{ profile.first_name_gana }} {{ profile.last_name_gana }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">郵便番号</td>
                        <td class="p-1 w-3/5">{{ profile.postal_code }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">都道府県</td>
                        <td class="p-1 w-3/5">{{ profile.prefecture }}</td>
                    </tr>
                    <tr>
                        <td class="p-1 w-2/5 font-bold">住所</td>
                        <td class="p-1 w-3/5">{{ profile.address }}</td>
                    </tr>

                </template>
            </table>
            <div class="my-6 text-center border border-sky-500 rounded-full p-0.5 w-fit mx-auto text-sm">
                <button type="button" @click="submit" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    class="inline-block items-center px-10 py-1 rounded-full font-semibold text-white uppercase tracking-widest bg-theme bg-theme-hover">
                    保存
                </button>
            </div>

            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between">
                <h3>入金履歴</h3>
            </div>

            <div class="w-full flex flex-col gap-4 mb-8">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border border-collapse divide-x-2">
                            <th class="text-center py-2">金額</th>
                            <th class="text-center py-2">ポイント</th>
                            <th class="text-center py-2">購入時間</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="payment in payments" class="border divide-x-2">
                            <td class="text-center py-2">{{ format_number(payment.amount) }}</td>
                            <td class="text-center py-2">{{ format_number(payment.point) }} pt</td>
                            <td class="text-center py-2">{{ payment.updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-b w-full p-2 my-2 font-semibold flex justify-between">
                <h3>クーポン使用履歴</h3>
            </div>

            <div class="w-full flex flex-col gap-4 mb-8">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border border-collapse divide-x-2">
                            <th class="text-center py-2">名前</th>
                            <th class="text-center py-2">コード</th>
                            <th class="text-center py-2">ポイント</th>
                            <th class="text-center py-2">使用時間</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="coupon in coupons" class="border divide-x-2">
                            <td class="text-center py-2">{{ coupon.title }}</td>
                            <td class="text-center py-2">{{ format_number(coupon.code) }}</td>
                            <td class="text-center py-2">{{ format_number(coupon.point) }} pt</td>
                            <td class="text-center py-2">{{ coupon.updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
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
        user: Object,
        profile: Object,
        payments: Object,
        coupons: Object
    },
    mounted() {
        console.log(this.payments);
    },
    methods: {
        submit() {
            this.form.post(route('admin.users.update'), {
                preserveScroll: true,
                onFinish: (res) => { console.log(res) },
            });
        },
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },

    },
    setup(props) {
        const form = useForm({
            id: props.user.id,
            point: props.user.point
        });

        return { form }
    },
}
</script>