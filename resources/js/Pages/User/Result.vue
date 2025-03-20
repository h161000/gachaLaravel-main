<template>
    <Head title="ガチャ–結果" />

    <UserLayout>
        <div v-if="ready_delivery == 0"
            class="md:w-[768px] m-auto items-center flex justify-center h-full px-2 sm:px-6 md:px-8">
            <div>
                <h1
                    class="mb-2 text-neutral-600 text-base md:text-lg underline underline-offset-8 py-4 text-center font-bold ">
                    &nbsp;&nbsp;&nbsp;ガチャ – 結果&nbsp;&nbsp;&nbsp;</h1>
                <form @submit.prevent="submit()">
                    <div class="mb-3 justify-center flex flex-wrap">
                        <template v-for="(product, key) in products.data">
                            <div class="relative max-w-40 min-w-[20%]">
                                <label :for="('checkbox' + product.id)"
                                    class="z-[5] block mb-5 mx-2.5 cursor-pointer text-center justify-center relative rounded-sm">
                                    <div class="text-center p-0 relative mb-1">
                                        <img class="inline-block w-full " :src="product.image"
                                            :class="{ 'shadow-[0_3px_12px_6px_#0033aaaa]': product.is_last }" />
                                    </div>
                                    <div class="text-neutral-900 truncate text-center text-base px-4 mt-2">
                                        {{ product.name }}
                                    </div>
                                    <div class="text-neutral-600 text-sm truncate mb-1 text-center px-4">
                                        {{ product.rare }}
                                    </div>

                                    <div class="flex justify-center items-center w-fit px-4 py-0.5 mx-auto text-white relative rounded-full text-xs text-center bg-theme border border-white"
                                        style="line-height:1.5;">
                                        <PlayIcon class="w-3 h-3 text-white mr-2" />
                                        {{ format_number(product.point) }}<span class="text-white">&nbsp;PT</span>
                                    </div>


                                    <div class="absolute -top-3.5 -right-2">
                                        <input :id="('checkbox' + product.id)" type="checkbox"
                                            v-model="form.checks['id' + product.id]"
                                            class="h-5 w-5 cursor-pointer rounded text-[#00B1FF] shadow-sm focus:ring-0 focus:outline-none" 
                                            @change="changeCheck()"
                                        />
                                    </div>
                                </label>

                            </div>

                        </template>

                    </div>

                    <div class="text-center text-neutral-600 mb-6 z-10 relative">
                        <label class="cursor-pointer flex items-center justify-center">
                            <input type="checkbox" v-model="isCheckAll" @change="checkall(),changeCheck()"
                                class="h-5 w-5 md:h-6 md:w-6 rounded-sm border-neutral-600 text-[#00B1FF] shadow-s focus:ring-0"/>
                            <span class="ml-2 text-sm md:text-base font-bold">全てを選択する</span>
                        </label>
                    </div>

                    <div class="mb-3 text-center text-neutral-50 z-10 relative">
                        <button type="button" @click="submit('point')" :class="{ 'opacity-50': form.processing || (!hasCheck) }"
                            :disabled="form.processing"
                            class="inline-block items-center px-8 py-2.5 border border-transparent rounded-lg font-semibold text-xs md:text-sm text-white uppercase tracking-widest bg-theme bg-theme-hover focus:outline-none transition ease-in-out duration-150 m-2">
                            選択した商品をポイントに変換
                        </button>
                        <button type="button" @click="submit('delivery')"
                            :class="{ 'opacity-50': form.processing || (!hasCheck) }"
                            :disabled="form.processing || (!hasCheck)"
                            class="inline-block items-center px-8 py-2.5 border border-transparent rounded-lg font-semibold text-xs md:text-sm text-white uppercase tracking-widest bg-theme bg-theme-hover focus:outline-none transition ease-in-out duration-150 m-2">
                            選択した商品を発送する
                        </button>
                    </div>
                    <div class="pb-6 pt-4 text-center text-sm md:text-base font-bold text-neutral-600 z-10">
                        ※選択されなかった商品は「獲得済み 商品一覧」に移動されます。<br>
                        ※獲得した商品は合計10000pt以上で発送が可能です。
                    </div>
                </form>
            </div>
        </div>
        <div v-if="ready_delivery == 1" class="pt-6 md:px-6 px-4 text-neutral-600">
            <h2 class="mb-2 text-base md:text-lg font-bold">配送内容の確認</h2>
            <hr class="mb-6 border-neutral-400" />
            <div class="mb-3 text-sm">配送商品 （計{{ products_count }}点）</div>
            <div class="mb-8 grid sm:grid-cols-2">
                <template v-for="(item, key) in products.data">
                    <div v-if="form.checks['id' + item.id]" class="mb-0 border-neutral-200 border-b py-2">
                        <div class="flex border-neutral-200 items-center gap-2">
                            <img :src="item.image" class="w-16 inline-block object-contain" />
                            <label :for="'checkbox' + item.id"
                                class="cursor-pointer flex items-center justify-between flex-1 text-xs py-1 px-2 ">
                                <div class="flex flex-col justify-end h-full">
                                    <div>{{ item.name }}</div>
                                    <div>{{ item.rare }}</div>
                                    <div
                                        class="text-xs flex bg-red-500 justify-center items-center rounded-full px-3 md:my-1 py-0.5 text-white w-fit">
                                        <img src="/images/icon_coin.png" class="h-4 w-4 mr-1" />
                                        <span>{{ item.point }}&nbsp;PT</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
            <div class="mb-2 text-sm">配送先情報</div>
            <div class="mb-8 border border-neutral-600 rounded-md px-4 py-2 flex items-center justify-between">
                <div class="flex-1">
                    <template v-if="products.id">
                        <p class="font-bold text-sm">{{ products.first_name + products.last_name }}</p>
                        <p class="text-sm">〒{{ products.postal_code }}</p>
                        <p class="text-sm">{{ products.address }}</p>
                    </template>
                </div>
                <div>
                    <Link :href="route('user.address')" type="button" @click="back_delivery()"
                        :class="{ 'opacity-50': form.processing || (!hasCheck) }"
                        :disabled="form.processing || (!hasCheck)"
                        class="inline-block items-center px-5 py-1.5 rounded-md text-sm text-white bg-theme bg-theme-hover uppercase tracking-widest transition ease-in-out duration-150">
                    編集
                    </Link>
                </div>
            </div>

            <div class="mb-8 text-center text-neutral-50 flex items-center justify-center gap-2 text-xs md:text-sm">
                <button type="button" @click="back_delivery()" :class="{ 'opacity-50': form.processing || (!hasCheck) }"
                    :disabled="form.processing || (!hasCheck)"
                    class="w-32 inline-block items-center px-1 py-2 rounded-md bg-red-500 hover:bg-red-600 transition ease-in-out duration-150">
                    戻る
                </button>
                <button type="button" @click="submit_delivery()"
                    :class="{ 'opacity-50': form.processing || (!hasCheck) }" :disabled="form.processing || (!hasCheck)"
                    class="w-32 inline-block items-center px-1 py-2 rounded-md bg-theme bg-theme-hover transition ease-in-out duration-150">
                    発送する
                </button>
            </div>
        </div>
        <div v-if="ready_delivery == 2" class="pt-6 md:px-6 px-4 text-neutral-600">
            <h2 class="mb-2 text-lg font-bold">配送手続き完了</h2>
            <hr class="mb-8 border-neutral-400" />
            <div class="mb-8 ">
                計{{ products_count }}点の商品の配送手続きが完了しました。<br /><br />
                商品の発送には<span class="font-bold">数日~数週間</span>かかる可能性がございます。<br />
                ご了承のほどよろしくお願い致します。
            </div>

            <div class="mb-8 text-center text-neutral-50 flex items-center justify-center gap-2 text-xs md:text-sm">
                <Link as="button" :href="route('main')"
                    class="w-40 inline-block items-center px-1 py-2 rounded-md bg-red-500 hover:bg-red-600 transition ease-in-out duration-150">
                ガチャTOPへ
                </Link>
                <button type="button" @click="back_delivery()" :class="{ 'opacity-50': form.processing || (!hasCheck) }"
                    :disabled="form.processing || (!hasCheck)"
                    class="w-40 inline-block items-center px-1 py-2 rounded-md bg-theme bg-theme-hover transition ease-in-out duration-150">
                    獲得した商品一覧へ
                </button>
            </div>
        </div>
        <!-- box-shadow: 0px 0px 15px 2px rgba(255, 255, 255, 0.6); -->

    </UserLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { PlayIcon } from '@heroicons/vue/24/solid';

export default {
    components: { Head, UserLayout, Link, PlayIcon },
    props: {
        errors: Object,
        auth: Object,
        category_share: Object,
        products: Object,
        token: String,
        products: Object,
    },
    data() {
        return {
            // catagries: ['sdfads', 'abc', 'sdfads', 'sdfads'],
            isCheckAll: false,
            hasCheck: false,
            main_tab: [
                { title: "未選択", route_url: route('user.products'), is_active: true },
                { title: "発送待ち", route_url: route('user.products.wait'), is_active: false },
                { title: "発送済み", route_url: route('user.products.delivered'), is_active: false },
            ],
            ready_delivery: 0,
            products_count: 0,
        }
    },
    setup(props) {
        let checks = {};
        let i;
        for (i = 0; i < props.products.data.length; i++) {
            checks['id' + props.products.data[i]['id']] = false;
        }
        const form = useForm({
            checks: checks,
            token: props.token,
        })

        return { form }
    },
    methods: {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        changeCheck() {
            let i; let hasCheckLocal = false;
            for (i = 0; i < this.products.data.length; i++) {
                if (this.form.checks['id' + this.products.data[i]['id']]) {
                    hasCheckLocal = true;
                    break;
                }
            }
            this.hasCheck = hasCheckLocal;
        },
        submit(submit_type) {
            let i; let products_count = 0; var point = 0;
            for (i = 0; i < this.products.data.length; i++) {
                if (this.form.checks['id' + this.products.data[i]['id']]) {
                    products_count++; point = point + parseInt(this.products.data[i]['point']);
                }
            }
            if (submit_type == 'point') {

                let need_submit = false;
                if (point > 0) {
                    if (confirm('交換しますか？ 選択した' + products_count + '点の商品を' + point + 'ptと交換します。')) {
                        need_submit = true;
                    }
                } else {
                    if (confirm('全ての商品が「獲得済み商品一覧」に移動します。')) {
                        need_submit = true;
                    }
                }

                if (need_submit) {
                    this.form.post(route('user.gacha.result.exchange'), {
                        onFinish: () => {
                        },
                    });
                }
            } else {
                if (products_count > 0) {
                    if (point >= 10000) {
                        this.products_count = products_count;
                        this.ready_delivery = 1;
                    } else {
                        alert('合計10000pt以上の商品を選択してください。\n選択した商品の合計ポイントは' + point + 'ptです。');
                    }
                }
            }
        },
        back_delivery() {
            this.ready_delivery = 0;
        },
        submit_delivery() {
            if (this.products.address) {
                if (confirm('' + this.products_count + '点の商品を配送しますか？')) {
                    this.form.post(route('user.delivery.post'), {
                        preserveScroll: true,
                        onFinish: () => {
                            this.ready_delivery = 2;
                        },
                    });
                }
            } else {
                alert('入力エラー、配送先情報を入力してください。')
            }

        },
        checkall() {
            let i;

            for (i = 0; i < this.products.data.length; i++) {
                this.form.checks['id' + this.products.data[i]['id']] = this.isCheckAll;
            }
        },
    }
}
</script>