<template>
    <UserLayout>
        <Head title="新規登録" />
 
        <div class="pt-6 md:px-2 px-4">
            <form @submit.prevent="submit">
                <h2 class="mb-2 text-lg font-bold">{{step_titles[step_integer]}}</h2>
                <hr class="mb-8"/>
                <div v-if="step_integer==0">
                    <div class="mb-8">
                        <InputLabel for="phone" value="電話番号" class="block text-base font-bold text-neutral-700 pl-4 mb-4"/>
                        <input v-model="form.phone" id="phone" type="tel" class="h-12 w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="半角数字のみで入力してください"/>
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="flex items-center justify-center">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-flex items-center px-10 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-theme bg-theme-hover focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                            認証へ進む
                        </button>
                    </div>
                </div>
                <div v-if="step_integer==1">
                    <div class="mb-8">
                        <InputLabel for="code" class="block text-base font-bold text-neutral-700 pl-4 mb-4" value="smsに記載された4桁の認証コードを入力してください。" />
                        <input v-model="form.code" id="code" type="tel" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="認証コードを入力してください"/>

                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>

                    <div class="flex items-center justify-center">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-flex items-center px-10 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-800 active:bg-red-800 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                            次へ
                        </button>
                    </div>
                </div>

                <div v-if="step_integer==2">
                    <div class="mb-4">
                        <InputLabel for="email" class="block text-base font-bold text-neutral-700 pl-4 mb-4" value="メールアドレス" />
                        <input v-model="form.email" id="email" type="email" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="例) deeoripa@example.com"/>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="block mb-4">
                        <InputLabel for="password" class="block text-base font-bold text-neutral-700 pl-4 mb-4" value="パスワード" />
                        <input v-model="form.password" id="password" :type="passwordFieldType" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="半角英数字6～20文字"/>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="block mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" @click="switchVisibility()" class="rounded border-neutral-300 text-neutral-600 shadow-sm focus:ring-neutral-500"/>
                            <span class="ml-2 text-sm text-gray-600">パスワードを表示</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-center">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-flex items-center px-10 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-800 active:bg-red-800 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150">
                            登録
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-center mt-8 mb-8">
                    <Link :href="route('login')" class="underline text-sm text-cyan-500 hover:text-cyan-700 active:text-cyan-700 ">すでにアカウントをお持ちの方はこちら</Link>
                </div>
            </form>

        
        </div>
        
    </UserLayout>

</template>
<script>
    import axios from 'axios';
    import Checkbox from '@/Components/Checkbox.vue';
    import UserLayout from '@/Layouts/UserLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/inertia-vue3';
    import { ref } from 'vue';


    export default {
        components: { Checkbox, Head, UserLayout, InputError, InputLabel, TextInput, Link },
        data: () => ({
            passwordFieldType: "password",
            step_integer: 0,    
            step_titles: ['ご本人認証', '認証コード入力', 'アカウント登録'],
            is_processing: false,
            data: {},
        }),
        props: {
            errors: Object
        },
        methods: {
            submit () {
                if (this.step_integer==0) {
                    this.form.post(route('register.phone.send'), {
                        onFinish: () => {
                            this.data = usePage().props.value.flash;
                            
                            if (this.data.data) {
                                if (this.data.data.status==1) {
                                    this.step_integer = 1;
                                }
                            }
                            
                        },
                    });
                } else if (this.step_integer==1) {
                    this.form.post(route('register.phone.verify'), {
                        onFinish: () => {
                            this.data = usePage().props.value.flash;
                            if (this.data.data) {
                                if (this.data.data.status==1) {
                                    this.step_integer = 2;
                                }
                            }
                        },
                    });
                } else {
                    this.form.post(route('register.phone.register'), {
                        onFinish: () => {
                           
                        },
                    });
                }
                
            },
            submit_phone () {
                const data = { phone: this.form.phone };
                this.is_processing = true;
                axios.post(route('register.phone.send'), data). 
                    then(response => {
                        if(response.status == 201 || response.status == 200) {
                            console.log(response.data);
                            
                        }
                        this.is_processing = false;
                    }).catch( error=> {
                        this.is_processing = false;
                        console.log(error);
                    });
            },
            switchVisibility () {
                this.passwordFieldType = this.passwordFieldType==="password"?"text":"password";
            },
        },
        setup() {
            const form = useForm( {
                phone:'',
                code:'',
                email: '',
                password: '',
            })

            return { form }
        },
        mounted(){
            
        },
    }
</script>