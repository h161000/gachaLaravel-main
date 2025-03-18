<template>
    <UserLayout>
        <Head title="ログイン"/>
        <div class="pt-6 px-4 mx-4">
            <h1 class="underline underline-offset-8 mb-10 text-center text-xl font-bold">&nbsp;&nbsp;&nbsp;ログイン&nbsp;&nbsp;&nbsp;</h1> 
            <form @submit.prevent="submit()">
                <div class="mb-6">
                    <label for="text1" class="block text-base font-bold pl-2 mb-2">メールアドレス</label>
                    <input v-model="form.email" id="text1" type="email" class="w-full h-10 text-neutral-700 border-neutral-300 shadow-xs focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="例) user@raffle-pro.com"/>
                    <div v-if="errors.email" class="text-red-500 text-sm mt-1">
                        {{ errors.email }}
                    </div>
                </div>

                <div class="mb-6">
                    <label  for="text2" class="block text-base font-bold pl-2 mb-2">パスワード</label>
                    <input v-model="form.password" id="text2" :type="passwordFieldType" class="w-full h-10 text-neutral-700 shadow-xs border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="半角英数字6～20文字"/>
                    <div v-if="errors.password" class="text-red-500 text-sm mt-1">
                        {{ errors.password }}
                    </div>
                </div>
                <div class="block mb-8 px-2">
                    <label class="flex items-center cursor-pointer gap-2">
                        <input type="checkbox" @click="switchVisibility()" class="w-5 h-5 rounded border-neutral-300 text-neutral-600 shadow-sm focus:ring-neutral-500"/>
                        <span class="text-base">パスワードを表示</span>
                    </label>
                </div>

                <div class="mb-8 text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit mx-auto">
                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-base text-white uppercase tracking-widest">
                        ログイン
                    </button>
                </div>
                <div class="flex gap-6 justify-center">
                    <div class="mb-8 flex items-center justify-center mt-5">
                        <Link :href="route('password.request')" class="underline text-base text-neutral-600">パスワードをお忘れの方はこちら</Link>
                    </div>
                    <div class="mb-8 flex items-center justify-center mt-5">
                        <Link :href="route('register')" class="underline text-base text-neutral-600">新規会員登録はこちら</Link>
                    </div>
                </div>
            </form>

        
        </div>
        
    </UserLayout>

</template>
<script>
    import UserLayout from '@/Layouts/UserLayout.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';
    
    export default {
        components: {  Head, UserLayout, Link },
        data: () => ({
            passwordFieldType: "password",
        }),
        props: {
            errors: Object
        },
        methods: {
            submit () {
                this.form.post(route('login'), {
                    preserveScroll: true,
                    onFinish: () => {
                        this.form.reset('password');
                    },
                });
            },
            
            switchVisibility () {
                this.passwordFieldType = this.passwordFieldType==="password"?"text":"password";
            },
        },
        setup() {
            const form = useForm( {
                email:'',
                password:'',
            })

            return { form }
        },
        mounted(){
            
        },
    }
</script>