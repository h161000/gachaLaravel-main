<template>
    <Head title="設定" />

    <AdminLayout>
        <div class="mt-6 px-5">  

        <h3 class="mb-2 text-lg font-bold">メンテナンスの設定</h3>
            <hr class="mb-4" />
            <div class="mb-6">
                <table>
                    <tbody>
                        <tr>
                            <td>メンテナンス</td>
                            <td class="px-3 text-cyan-500 text-sm">
                                <div @click="changemaintenance('1')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-l-md w-16 py-1" :class="{'bg-cyan-500 text-white':form.maintenance=='1'}">On</div> 
                                <div @click="changemaintenance('0')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-r-md w-16 py-1" :class="{'bg-cyan-500 text-white':form.maintenance!='1'}">Off</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-6">
                <button type="button" @click="submit()" class="inline-block items-center px-10 py-2 bg-cyan-500 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    保存
                </button>
            </div>
    
            <h3 class="mb-2 text-lg font-bold">決済の設定</h3>
            <hr class="mb-4" />
            <div class="mb-6">
                <table>
                    <tbody>
                        <tr class="h-10">
                            <td>決済モード</td>
                            <td class="px-3  text-cyan-500 text-sm">
                                <div @click="changeTestOrLive('live')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-l-md w-16 py-1" :class="{'bg-cyan-500 text-white':form_payment.testOrLive=='live'}">ライブ</div> 
                                <div @click="changeTestOrLive('test')" class="cursor-pointer border border-1 border-cyan-500 inline-block text-center rounded-r-md w-16 py-1" :class="{'bg-cyan-500 text-white':form_payment.testOrLive!='live'}">テスト</div>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
            
            <div class="mb-6 text-center">
                テスト用決済ページ: <a target="_blank" :href="route('test.user.point')" class="text-cyan-500 hover:text-cyan-900"> {{ route('test.user.point') }}</a>
            </div>
            <div class="text-center mb-6">
                <button type="button" @click="submit_payment()" class="inline-block items-center px-10 py-2 bg-cyan-500 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150 mr-2">
                    保存
                </button>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import { Head, useForm, usePage } from '@inertiajs/inertia-vue3';
import AdminLayout from '@/Layouts/Admin.vue';

import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';

export default {
    components: {Head, AdminLayout, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot},
    props: {
        errors: Object,
        maintenance:String,
        testOrLive:String,
    },
    data() {
        return {
        }
    },
    methods: {
        changemaintenance(value) {
            this.form.maintenance = value;
        },
        changeTestOrLive(value) {
            this.form_payment.testOrLive = value;
        },
        submit() {
            this.form.post(route('admin.settings.maintenance_store'), {
                preserveScroll: true,
                onFinish: () => {
                    
                },
            }); 
        },  
        submit_payment() {
            this.form_payment.post(route('admin.settings.payment_store'), {
                preserveScroll: true,
                onFinish: () => {
                    
                },
            }); 
        },  
    },
    setup(props) {
        let data = {
            maintenance: props.maintenance,
        };
        const form = useForm( data );

        let data_payment = {
            testOrLive: props.testOrLive,
        };
        const form_payment = useForm( data_payment );

        return { form, form_payment }
    },
    mounted() {
    },
}
</script>