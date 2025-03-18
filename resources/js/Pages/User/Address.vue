<template>
    <Head title="個人情報登録" />

    <UserLayout>
        <div class="pt-6 md:px-2 px-4">
            <h1 class="mb-8 text-base md:text-lg text-center underline underline-offset-8 font-bold text-neutral-600">&nbsp;&nbsp;個人情報登録&nbsp;&nbsp;</h1>
            <div class="w-full md:px-20">  
                <form @submit.prevent="submit()">
                    <div class="w-full mb-4 flex md:gap-4 gap-1">
                        <div class="w-1/2 inline-block">
                            <label for="first_name" class="pb-2 px-3 text-sm md:text-base block">姓</label>
                            <input v-model="form.first_name" id="first_name" type="text"  class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="山田"/>
                            <div v-if="errors.first_name" class="text-red-500 text-sm ">
                                {{ errors.first_name }}
                            </div>
                        </div>
                        <div class="w-1/2 inline-block">
                            <label for="last_name" class="pb-2 px-3 text-sm md:text-base block">名</label>
                            <input v-model="form.last_name" id="last_name" type="text"  class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="太郎"/>
                            <div v-if="errors.last_name" class="text-red-500 text-sm ">
                                {{ errors.last_name }}
                            </div>
                        </div>
                    </div>

                    <div class="w-full mb-4 flex md:gap-4 gap-1">
                        <div class="w-1/2 inline-block">
                            <label for="first_name_gana" class="pb-2 px-3 text-sm md:text-base block">姓 (カナ)</label>
                            <input v-model="form.first_name_gana" id="first_name_gana" type="text"  class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="ヤマダ"/>
                            <div v-if="errors.first_name_gana" class="text-red-500 text-sm ">
                                {{ errors.first_name_gana }}
                            </div>
                        </div>
                        <div class="w-1/2 inline-block">
                            <label for="last_name_gana" class="pb-2 px-3 text-sm md:text-base block">名 (カナ)</label>
                            <input v-model="form.last_name_gana" id="last_name_gana" type="text"  class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="タロウ"/>
                            <div v-if="errors.last_name_gana" class="text-red-500 text-sm ">
                                {{ errors.last_name_gana }}
                            </div>
                        </div>
                    </div>

                    <div class="w-full mb-4">
                        <label for="postal_code" class="pb-2 px-3 text-sm md:text-base block">郵便番号 (ハイフンなし半角数字)</label>
                        <input v-model="form.postal_code" id="postal_code" type="text"  class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="入力してください。"/>
                        <div v-if="errors.postal_code" class="text-red-500 text-sm ">
                            {{ errors.postal_code }}
                        </div>
                    </div>

                    <div class="w-full mb-4">
                        <label for="prefecture" class="pb-2 px-3 text-sm md:text-base block">都道府県</label>
                        <select v-model="form.prefecture" id="prefecture" class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="選択してください">
                            <option v-for="(item, key) in prefectures" :value="item">{{item}}</option>
                        </select>
                        <div v-if="errors.prefecture" class="text-red-500 text-sm ">
                            {{ errors.prefecture }}
                        </div>
                    </div>

                    <div class="w-full mb-4">
                        <label for="address" class="pb-2 px-3 text-sm md:text-base block">住所</label>
                        <input v-model="form.address" type="text" id="address" class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="例) 渋谷区神宮前1-2-3 DPビル501号室"/>
                        <div v-if="errors.address" class="text-red-500 text-sm ">
                            {{ errors.address }}
                        </div>
                    </div> 

                    <div class="w-full mb-6">
                        <label for="phone" class="pb-2 px-3 text-sm md:text-base block">電話番号 (ハイフンなし半角数字)</label>
                        <input v-model="form.phone" id="phone" type="tel" class="h-10 bg-white/90 w-full md:px-5 px-3 py-2 border border-[#53ccff] focus:border-[#53ccff] text-sm md:text-base focus:ring focus:ring-[#53ccff] focus:ring-opacity-50 rounded-md placeholder-neutral-400" placeholder="入力してください"/>
                        <div v-if="errors.phone" class="text-red-500 text-sm ">
                            {{ errors.phone }}
                        </div>
                    </div>
                    <div class="mb-8 text-center border border-[#53ccff] rounded-full p-0.5 w-fit mx-auto">
                        <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-white text-sm md:text-base uppercase tracking-widest">
                            保存
                        </button>
                    </div>
                </form> 
            </div>

        </div>
    </UserLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import prefectures from '@/Store/prefectures';


export default {
    components: {Head, UserLayout, Link},
    props: {
        errors: Object,
        gacha: Object,
        category_share:Object,
        profiles: Object,
    },
    data() {
        return {
            prefectures: prefectures,
        }
    },
    methods: {
        submit () {
            this.form.post(route('user.address.post'), {
                preserveScroll: true,
                onFinish: () => {},
            });
        },
    },
    setup(props) {
        let profile = {
            first_name:'',
            last_name:'',
            first_name_gana:'',
            last_name_gana:'',
            postal_code:'',
            prefecture: prefectures[0],
            address:'',
            phone:'',
        };
        if (props.profiles.length) {
            let item = props.profiles[0]
            profile = {
                first_name: item.first_name,
                last_name: item.last_name,
                first_name_gana: item.first_name_gana,
                last_name_gana: item.last_name_gana,
                postal_code: item.postal_code,
                prefecture: item.prefecture,
                address: item.address,
                phone: item.phone,
            };
        }

        const form = useForm( profile );
        return { form }
    },
    mounted() {
        
    },
}
</script>