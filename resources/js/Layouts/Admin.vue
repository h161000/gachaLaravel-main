<template>
    <div class="flex min-h-screen text-neutral-700 text-base underline-offset-2 font-medium">
        <TransitionRoot :show="sidebarOpened" class="h-full">
            <TransitionChild enter="transition ease-in-out duration-200 transform" enter-from="translate-x-full"
                enter-to="translate-x-0" leave="transition ease-in-out duration-200 transform"
                leave-from="translate-x-0" leave-to="translate-x-full" as="template">
                <div class="fixed h-full right-0 z-40">

                    <div class="flex flex-col relative h-full w-64 bg-gray-100 border-l border-neutral-200 ml-auto">
                        <button @click="sidebarOpened = false"
                            class="absolute top-[1.125rem] md:right-4 right-2 flex items-center justify-center w-9 h-9 rounded-full focus:outline-none bg-theme bg-theme-hover"
                            type="button" value="Close sidebar">
                            <XMarkIcon class="h-5 w-5 rounded-full text-white" />
                        </button>

                        <div class="flex-1 overflow-y-auto">
                            <Sidebar />
                        </div>
                    </div>
                </div>
            </TransitionChild>
            <TransitionChild enter="transition-opacity ease-linear duration-200" enter-from="opacity-0"
                enter-to="opacity-100" leave="transition-opacity ease-linear duration-200" leave-from="opacity-100"
                leave-to="opacity-0" class="fixed z-30 w-full h-full">
                <div class="bg-neutral-600 h-full bg-opacity-50" @click="sidebarOpened = false"></div>
            </TransitionChild>
        </TransitionRoot>

        <TransitionRoot :show="isDialogMessage" class="h-full">
            <Dialog :open="isDialogMessage" @close="{ closeOnConfirm && closeModal?.call(); isDialogMessage = false; }"
                class="fixed h-full inset-0 z-40 ">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95">
                    <div
                        class="flex flex-col text-neutral-700 rounded relative z-20 top-20 w-fit min-w-48 max-w-lg bg-neutral-50 border-l border-neutral-200 m-auto">
                        <div class="pt-3 pb-1 px-1 text-center font-bold border-b mb-2">
                            {{ dialogTitle }}
                        </div>

                        <div class="px-4 text-sm text-center" v-html="dialogMessage">
                        </div>

                        <hr class="mt-3" />
                        <button @click="{ closeOnConfirm && closeModal?.call(); isDialogMessage = false; }"
                            class="cursor-pointer p-2 text-cyan-500 text-center text-base focus-visible:outline-0">
                            OK
                        </button>
                    </div>
                </TransitionChild>
                <TransitionChild enter="transition-opacity ease-linear duration-200" enter-from="opacity-0"
                    enter-to="opacity-100" leave="transition-opacity ease-linear duration-200" leave-from="opacity-100"
                    leave-to="opacity-0" as="template">
                    <DialogOverlay class="fixed inset-0 bg-neutral-600 h-full bg-opacity-50"></DialogOverlay>
                </TransitionChild>
            </Dialog>
        </TransitionRoot>
        <div class="min-h-screen w-full">
            <div class="w-full z-[10] fixed border-b bg-white border-[#0ea5e9]">

                <div class="flex justify-center md:px-4 px-2 items-center h-[4.5rem]">
                    <div class="w-full flex md:px-0 px-2 mx-auto justify-center md:gap-4 gap-2">
                        <div class="flex items-center flex-1">
                            <Logo />
                        </div>
                        <div class="flex-shrink-0 flex items-center">
                            <Link v-if="this.user" :href="point_link"
                                class="text-center text-white rounded-md md:text-sm text-xs h-9" as="button">
                            <div class="flex items-center md:px-3 px-1.5 rounded-md h-full bg-theme bg-theme-hover">
                                <img src="/images/icon_coin.png" class="md:h-5 h-4" />&nbsp;&nbsp;&nbsp;{{ point_value
                                }}&nbsp;PT&nbsp;<span class="border-white border-l md:mx-2 mx-1 h-3"></span>&nbsp;+
                            </div>
                            </Link>

                            <Link v-else :href="route('register')"
                                class="text-center text-white rounded-md md:text-sm text-xs h-9" as="button">
                            <div
                                class="md:px-4 px-2 rounded-md h-full text-center items-center flex bg-theme bg-theme-hover">
                                新規登録</div>
                            </Link>
                            <!--   <div class="min-w-[60px] h-[26px] text-right bg-blue-500 hover:bg-cyan-700 text-red-50 py-1 pr-5 pl-5 rounded-full text-xs m-1 mb-0"> {{dp_value}} <span class="text-xs"> tp</span>
                            </div>
                            -->

                        </div>
                        <div class="relative flex-shrink-0 flex items-center ">
                            <!-- <UserCircleIcon v-if="this.user" @click="sidebarOpened=true" class="w-9 h-9 text-cyan-700 inline cursor-pointer" /> -->
                            <img v-if="this.user" @click="sidebarOpened = true"
                                class="w-9 h-9 inline cursor-pointer rounded-full" src="/images/icon_user.png" />
                            <Link :href="route('login')" v-else
                                class="border-solid rounded-md h-9 md:text-sm text-xs text-white" as="button">
                            <div
                                class="md:px-4 px-2 rounded-md h-full text-center items-center flex bg-theme bg-theme-hover">
                                ログイン</div>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>

            <main class="w-full pt-[4.5rem] min-h-screen flex flex-col">

                <div class="w-full lg:w-[1024px] flex-1 flex flex-col mx-auto sm:px-8 md:px-12">

                    <Category v-if="!hide_cat_bar" />
                    <div class="w-full relative flex-1 flex justify-center">
                        <div class="w-full">
                            <slot />

                        </div>

                    </div>
                </div>
            </main>
        </div>

    </div>
</template>

<script>
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay, Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import Logo from '@/Parts/Logo.vue';
import Sidebar from '@/Parts/Sidebar.vue';
import Footer from '@/Parts/Footer.vue';
import { usePage, Link } from '@inertiajs/inertia-vue3';

import Category from '@/Parts/Category.vue';

import { XMarkIcon, ChevronLeftIcon, Bars3Icon } from '@heroicons/vue/24/outline'

import Toastify from "toastify-js";

export default {
    components: { Category, Sidebar, Footer, TransitionRoot, TransitionChild, Dialog, DialogOverlay, XMarkIcon, ChevronLeftIcon, Bars3Icon, Link, Menu, MenuButton, MenuItems, MenuItem, Logo },
    props: {
        closeModal: Function
    },
    data() {
        return {
            sidebarOpened: false,
            isDialogMessage: false,
            closeOnConfirm: false,
            dialogMessage: "",
            dialogTitle: "",
            user: {},
            point_value: 0,
            dp_value: 0,
            point_link: route('user.point'),
            hide_cat_bar: "",
            gacha_button: {},
        }
    },
    mounted() {
        this.user = usePage().props.value.auth.user;
        if (this.user) {
            this.point_value = this.user.point;
            this.dp_value = this.user.dp;
        }

        this.gacha_button = usePage().props.value.gacha_button;

        this.hide_cat_bar = usePage().props.value.hide_cat_bar;
    },
    computed: {
        flash() {
            return usePage().props.value.flash;
        }
    },
    watch: {
        flash: function (newVal, oldVal) {
            if (newVal.type == "notification") {
                this.notification(newVal.message);
            }
            if (newVal.type == "dialog") {
                this.dialogMessage = newVal.message;
                this.dialogTitle = newVal.title;
                this.isDialogMessage = true;
                this.closeOnConfirm = true;
            }
            if (newVal.data && newVal.data.user) {
                this.point_value = newVal.data.user.point;
            }
            if (newVal.data && newVal.data.close !== undefined) {
                this.closeOnConfirm = newVal.data.close;
            }
        }
    },
    methods: {
        format_number(n) {
            // return n;
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        notification(message) {
            if (message) {
                Toastify({
                    text: message,
                    duration: 300000,
                    newWindow: true,
                    close: false,
                    gravity: "top", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    stopOnFocus: false, // Prevents dismissing of toast on hover
                    className: "toastify-content newClass",
                    onClick: function () { } // Callback after click
                }).showToast();
            }
        },
        back() {
            window.history.back();
        },
    }
}
</script>
