<template>
    <UserLayout>
        <Head title="新規登録" />
 
        <div class="pt-6 px-4 mx-4 h-fit text-neutral-600">
            <form @submit.prevent="submit">
                <h2 class="mb-10 text-xl text-center font-bold underline underline-offset-8">&nbsp;&nbsp;&nbsp;{{step_titles[step_integer]}}&nbsp;&nbsp;</h2>
                
                <div v-if="step_integer==0" class="flex flex-col items-center">
                    <div class="mb-10 w-full">
                        <label for="email" class="block text-base font-bold pl-2 mb-2">メールアドレス</label>
                        <input v-model="form.email" id="email" type="email" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm " />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center mb-8 text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit mx-auto hover:scale-105">
                        <div class="px-10 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-base text-white tracking-widest">
                            認証へ進む
                        </div>
                    </button>
                </div>
                <div v-if="step_integer==1">
                    <div class="mb-8">
                        <label for="code" class="block text-base pl-2 mb-2">登録したメールに送信された4行の認証コードを入力してください。</label>
                        <input v-model="form.code" id="code" type="tel" class="w-full text-neutral-700 border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm " placeholder="認証コードを入力してください"/>
                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>

                    <div class="mb-8 text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit mx-auto">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-base text-white uppercase tracking-widest">
                            次へ
                        </button>
                    </div>
                </div>
                <div v-if="step_integer==2">
                    <div class="mb-6">
                        <label for="phone" class="block text-base font-bold pl-2 mb-2">電話番号</label>
                        <input v-model="form.phone" id="phone" type="tel" class="text-neutral-700 h-10 w-full border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"  placeholder="半角数字のみで入力してください"/>
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-base font-bold pl-2 mb-2">パスワード</label>
                        <input v-model="form.password" id="password" :type="passwordFieldType" class="text-neutral-700 h-10 w-full border-sky-300 focus:border-sky-300 focus:ring focus:ring-sky-200 focus:ring-opacity-50 rounded-md shadow-sm"  placeholder="半角英数字6～20文字"/>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    
                    <div class="block mb-8 px-2">
                        <label class="flex items-center cursor-pointer gap-2">
                            <input type="checkbox" @click="switchVisibility()" class="w-5 h-5 rounded border-sky-300 text-neutral-600 shadow-sm focus:ring-neutral-500"/>
                            <span class="text-base">パスワードを表示</span>
                        </label>
                    </div>
                    
                    <div class="mb-8 text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit mx-auto">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-base text-white uppercase tracking-widest">
                            登録
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-center mt-8 mb-8 pb-8">
                    <Link :href="route('login')" class="underline text-base  text-neutral-600">すでにアカウントをお持ちの方はこちら</Link>
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
            step_titles: ['アカウント登録', '認証コード入力', 'ご本人認証'],
            is_processing: false,
            data: {},
        }),
        props: {
            errors: Object
        },
        methods: {
            submit () {
                if (this.step_integer==0) {
                    this.form.post(route('register.email.send'), {
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
                    this.form.post(route('register.email.verify'), {
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