<template>
    <Head title="クーポン" />

    <UserLayout>

        <div class="md:px-6 px-4">
            <div class="text-center w-full py-4 mb-2 font-bold text-base md:text-lg text-neutral-600 underline underline-offset-8">
                <h3>&nbsp;&nbsp;&nbsp;クーポンの獲得&nbsp;&nbsp;&nbsp;</h3>
            </div>
            <div>
                <form @submit.prevent="submit()">
                    <div class="mb-6 text-sm md:text-base text-neutral-600">
                        <label for="code" class="block font-medium mb-2 ml-2">コードを入力してください! <span class="text-red-500">*</span></label>
                        <input v-model="form.code" id="code" type="text" class="text-sm md:text-base w-full p-2 border border-[#53ccff] focus:border-[#53ccff] focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-400" placeholder="入力してください"/>
                        <div v-if="errors.code" class="text-red-500 text-sm mt-1">
                            {{ errors.code }}
                        </div>
                    </div>
                    <div class="mb-8 text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit mx-auto">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-sm md:text-base text-white uppercase tracking-widest">
                            獲得
                        </button>
                    </div>
                    <!-- <button type="submit" class="w-full inline-block items-center px-8 py-2 bg-blue-900 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest focus:outline-none focus:border-indigo-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                        獲得
                    </button> -->
                    
                </form>
            </div>

            <div class="w-full my-8 text-neutral-600">
                <h3 class="underline underline-offset-4 text-sm md:text-base">獲得履歴</h3>
                <table class="w-full table-fixed mt-2 text-xs md:text-base">
                    <thead>
                        <tr class="border-b border-collapse border-neutral-400">
                            <th class="text-center py-2">タイトル</th>
                            <th class="text-center py-2">ポイント</th>
                            <th class="text-center py-2">獲得時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="coupon in coupons" class="border-b border-collapse">
                            <td class="text-center py-2">{{ coupon.title }}</td>
                            <td class="text-center py-2">{{ coupon.point }} pt</td>
                            <td class="flex justify-center py-2">{{ coupon.acquired_time }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </UserLayout>
</template> 

<script>
import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { Inertia } from '@inertiajs/inertia';

export default {
    components: {Head, UserLayout, Link},
    props: {
        coupons: Object,
        errors:Object,
    },
    mounted() {
        
    },
    methods: {
        submit() {
            this.form.post(route('user.coupon.post'));
        }
    },
    setup(props) {
        const form = useForm( {
            code : '',
        })
        return { form }
    }
}
</script>