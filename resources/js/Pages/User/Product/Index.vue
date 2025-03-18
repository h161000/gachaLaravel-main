<template>

    <Head title="獲得した商品一覧" />

    <UserLayout>

        <div v-if="ready_delivery == 0" class="w-full">
            <div class="md:px-6 px-4 py-2">
                <h1 class="mb-2 py-4 text-base md:text-lg text-center font-bold underline underline-offset-8 text-neutral-600">
                    獲得した商品一覧</h1>
                <div class="w-full px-2 pb-2 text-neutral-700 text-sm">
                    <div
                        class="w-full mx-auto overflow-x-auto text-center rounded-md shadow-[0_2px_6px_rgba(0,0,0,0.2)] bg-white flex no-scrollbar font-['mplus2'] text-sm md:text-base divide-[#53ccff] divide-x">
                        <Link v-for="(item, key) in main_tab"
                            :href="item.route_url" class="flex-1" as="button">
                        <p class="h-full w-full px-2 py-2 rounded-sm whitespace-nowrap bg-theme-hover hover:text-white"
                            :class="{ 'bg-theme text-white': item.is_active }">
                            {{ item.title }}
                        </p>
                        </Link>
                    </div>
                </div>
            </div>
            <!-- <p class="px-3 pb-4 text-yellow-500">※ 発送期限を過ぎると、自動的にポイントに変換されます</p> -->
            <div class="md:px-8 px-6 grid sm:grid-cols-2 gap-2">
                <div v-for="(item, key) in products.data" class="px-3 py-2 bg-[#53ccff11] hover:bg-[#53ccff33] border border-[#53ccff] rounded-lg">
                    <div class="flex items-start gap-2">
                        <input :id="'checkbox' + item.id" v-model="form.checks['id' + item.id]" type="checkbox"
                            @change="changeCheck()"
                            class="cursor-pointer rounded border-[#0ea5e9] text-[#00B1FF] shadow-sm focus:ring-0" />

                        <label :for="'checkbox' + item.id"
                            class="cursor-pointer flex text-sm items-center gap-2 w-max flex-1">
                            <img :src="item.image" class="w-20 inline-block object-contain" />

                            <div class="h-full flex flex-col justify-evenly text-neutral-600">
                                <div class="text-sm">{{ item.name }}</div>
                                <div class="text-sm">{{ item.rare }}</div>
                                <div v-if="item.is_lost_product != 2"
                                    class="text-xs flex bg-red-500 justify-center items-center rounded-full py-1 px-2 text-white w-fit gap-1">
                                    <img src="/images/icon_coin.png" class="h-4 w-4" />
                                    <span>{{ item.point }}&nbsp;PT</span>
                                </div>
                                <!-- <div class="text-xs md:text-sm text-rose-500">発送期限: {{ item.delivery_deadline }}</div> -->

                            </div>

                        </label>
                    </div>

                </div>
            </div>

            <div class="pb-6 pt-6 text-center text-neutral-600">
                <template v-if="hasCheck">
                    選択した商品を...
                </template>
                <template v-else>
                    商品を選択してください！
                </template>
            </div>

            <div class="mb-8 pb-8 text-center text-neutral-600 flex justify-center items-center gap-2 text-xs md:text-sm">
                <button type="button" @click="submit('point')" :class="{ 'opacity-50': form.processing || (!hasCheck) }"
                    :disabled="form.processing || (!hasCheck)"
                    class="w-40 md:w-60 inline-block items-center px-1 py-1 border rounded-md font-semibold text-white uppercase tracking-widest bg-theme bg-theme-hover focus:shadow-outline-blue transition ease-in-out duration-150">
                    <div class="border-solid border border-white rounded-sm py-2">ポイントに換える</div>
                </button>
                <button type="button" @click="submit('delivery')"
                    :class="{ 'opacity-50': form.processing || (!hasCheck) }" :disabled="form.processing || (!hasCheck)"
                    class="w-40 md:w-60 inline-block items-center px-1 py-1 border rounded-md font-semibold text-white uppercase tracking-widest bg-theme bg-theme-hover focus:shadow-outline-lime transition ease-in-out duration-150">
                    <div class="border-solid border border-white rounded-sm py-2">発送する</div>
                </button>
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
                    <template v-if="profile.id">
                        <p class="font-bold text-sm">{{ profile.first_name + profile.last_name }}</p>
                        <p class="text-sm">〒{{ profile.postal_code }}</p>
                        <p class="text-sm">{{ profile.address }}</p>
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
                <button type="button" @click="back_delivery()"
                    :class="{ 'opacity-50': form.processing || (!hasCheck) }" :disabled="form.processing || (!hasCheck)"
                    class="w-40 inline-block items-center px-1 py-2 rounded-md bg-theme bg-theme-hover transition ease-in-out duration-150">
                    獲得した商品一覧へ
                </button>
            </div>
        </div>


    </UserLayout>
</template>

<script>
import { Head, useForm, Link } from '@inertiajs/inertia-vue3';
import UserLayout from '@/Layouts/UserLayout.vue';



export default {
    components: { Head, UserLayout, Link },
    props: {
        errors: Object,
        gacha: Object,
        category_share: Object,
        products: Object,
        profile: Object,
    },
    data() {
        return {
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
        })
        return { form }
    },
    methods: {
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
                    products_count++; point = parseInt(point) + parseInt(this.products.data[i]['point']);
                }
            }

            let need_submit = false;

            if (submit_type == "point") {
                if (point > 0) {
                    if (confirm('交換しますか？ 選択した' + products_count + '点の商品を' + point + 'ptと交換します。')) {
                        this.form.post(route('user.products.point.exchange'), {
                            preserveScroll: true,
                            onFinish: () => {
                            },
                        });
                    }
                }
            } else {
                if (products_count > 0) {
                    this.products_count = products_count;
                    this.ready_delivery = 1;
                    // if (confirm('選択した'+products_count+'点の商品を発送しますか？')){
                    //     
                    // }
                }
            }
        },
        back_delivery() {
            this.ready_delivery = 0;
        },
        submit_delivery() {
            if (this.profile.address) {
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


    },
}
</script>