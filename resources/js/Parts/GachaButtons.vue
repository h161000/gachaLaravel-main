<template>

    <div class="w-full mx-auto flex flex-col justify-center items-center h-full px-2 gap-1.5 py-2"
        style="max-width: 430px;">
        <div class="w-full grid grid-cols-2 gap-x-3 gap-y-2" style="max-width: 430px;">
            <template v-for="(count, index) in buttons">
                <button @click="clickgacha(count)" class="cursor-pointer rounded-md text-white border border-white"
                    :class="{
                        'opacity-50': processing, 'bg-rose-500 hover:bg-rose-400': (index == buttons.length - 1),
                        'col-span-2': (index == buttons.length - 1) && (buttons.length & 1),
                        'bg-theme bg-theme-hover': (index < buttons.length - 1)
                    }" :disabled="processing">
                    <div class="m-1">
                        <div>
                            <span class="font-bold text-lg">{{ count < gacha.count_rest ? count + (count == 1 ? '回' : '連') : '全て' }}</span>
                            <span class="font-bold text-lg">ガチャ</span>
                        </div>
                    </div>
                </button>
            </template>
            
        </div>
    </div>
    <TransitionRoot :show="isDialogMessage" class="h-full fixed inset-0 z-40 ">
        <TransitionChild enter="transition ease-in-out duration-200 transform" enter-from="-translate-y-10"
            enter-to="translate-y-0" leave="transition ease-in-out duration-200 transform" leave-from="opacity-100"
            leave-to="opacity-0" as="template">

            <div
                class="flex flex-col text-neutral-700 rounded-md relative z-20 top-20 w-fit min-w-48 bg-neutral-50 border-l border-neutral-200 m-auto p-2 gap-4">
                <div class="pt-3 pb-1 px-1 text-center font-bold">
                    消費ポイント確認
                </div>

                <div class="px-4 text-sm text-center" v-html="dialogMessage">
                </div>

                <div class="flex justify-center gap-4 p-2 text-sm">
                    <button @click="isDialogMessage = false"
                        class="rounded-md w-28 py-1 border-sky-500 border text-sky-600 hover:bg-sky-50">
                        キャンセル
                    </button>
                    <button @click="onConfirm"
                        class="rounded-md w-28 py-1 border-sky-500 border text-white bg-theme bg-theme-hover">
                        確定
                    </button>

                </div>
            </div>

        </TransitionChild>
        <TransitionChild enter="transition-opacity ease-linear duration-200" enter-from="opacity-0"
            enter-to="opacity-100" leave="transition-opacity ease-linear duration-200" leave-from="opacity-100"
            leave-to="opacity-0" as="template">

            <div class="fixed inset-0 bg-neutral-600 h-full bg-opacity-50" @click="isDialogMessage = false"></div>
        </TransitionChild>
    </TransitionRoot>
</template>

<script>
import { TransitionRoot, TransitionChild, Dialog, DialogOverlay } from '@headlessui/vue';
import { Link, usePage, useForm } from '@inertiajs/inertia-vue3';
import { PlayIcon } from "@heroicons/vue/24/solid";


export default {
    components: {
        Link,
        PlayIcon,
        TransitionRoot,
        TransitionChild,
        Dialog,
        DialogOverlay
    },
    props: {
        gacha: Object,
    },
    data() {
        return {
            category_share: usePage().props.value.category_share,
            processing: false,
            isDialogMessage: false,
            dialogMessage: "",
            onConfirm: null,
            buttons: [],
        };
    },
    methods: {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },

        clickgacha(number) {
            this.isDialogMessage = true;
            this.dialogMessage = `${this.format_number(this.gacha.point * Math.min(this.gacha.count_rest, number))}ポイント消費します。`;

            this.onConfirm = () => {
                this.isDialogMessage = false;
                this.processing = true;
                if (this.url_edit) { return; }
                useForm({ id: this.gacha.id, number }).post(route('user.gacha.start_post'), {
                    onFinish: () => {
                        this.processing = false;
                    }
                });
            }
        },


    },
    mounted() {
        this.buttons = this.gacha.buttons ? this.gacha.buttons.split(',').map(val => parseInt(val)).sort(function (a, b) { return a - b }) : [];
        while (this.buttons.length >= 2 && this.buttons[this.buttons.length - 2] >= this.gacha.count_rest) {
            this.buttons.pop()
        }
    }
}
</script>
