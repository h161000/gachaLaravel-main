<template>
    <div class="mb-0 relative rounded-md shadow-[0_5px_10px_0px_rgba(0,0,0,0.3)] w-full bg-cover overflow-hidden border-2 bg-white flex flex-col"
        :class="{ 'cursor-pointer hover:shadow-[0_5px_10px_5px_rgba(0,0,0,0.3)]': !url_edit && gacha.count_rest }">
        <div @click="clickCard()" class="flex items-center">
            <img v-bind:src=gacha.thumbnail
                class="rounded-md object-contain hover:object-scale-down" />
        </div>

        <div v-if="url_edit">
            <div class="-rotate-45 absolute z-[7] top-0 left-0 items-center w-[130px] -ml-[46px] -mt-[8px] pt-6 pb-2 font-semibold text-sm text-white text-center tracking-widest "
                :class="{ 'bg-green-500': gacha.status, 'bg-neutral-500': !gacha.status }">
                {{ gacha.status == 1 ? '公開' : '非公開' }}
            </div>
            <!-- <div class="overflow-hidden absolute top-0 w-[75px] h-[75px]">
                <div class="-rotate-45 absolute z-[7] top-0 left-0 items-center w-[130px] ml-[-12px] mt-[30px] pt-2 pb-6 font-semibold text-sm text-white text-center tracking-widest"
                    :class="{ 'bg-red-500/50': gacha.status, 'bg-neutral-500': !gacha.status }">
                    {{ gacha.status == 1 ? '公開' : '非公開' }}
                </div>

            </div> -->
            <div
                class="absolute z-[7] top-0 right-0 items-center rounded-bl-lg font-semibold text-sm text-white uppercase overflow-hidden">
                <Link :href="url_edit"
                    class="bg-green-500 hover:bg-green-600 px-6 py-2 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                    {{ gacha.id }} - 編集
                </Link>

                <button @click="destroyGacha(gacha.id)"
                    class="z-20 px-6 py-2 bg-red-500 text-neutral-50 hover:bg-red-600">
                    削除
                </button>

            </div>
        </div>

        <div v-if="(url_edit && gacha.count_rest != gacha.ableCount)"
            class="absolute bottom-0 w-full h-full bg-opacity-70 z-[5] bg-neutral-900 flex items-center justify-center">
            <div class="text-white text-[30px] text-center font-bold">カード枚数不一致</div>
        </div>

        <div v-if="(gacha.count_rest == 0 && !url_edit)"
            class="absolute bottom-0 w-full h-full bg-opacity-80 z-[7] bg-neutral-900 flex items-center justify-center">
            <img src="/images/sold_out.png" class="w-2/3">
        </div>

        <div class="w-full flex flex-col flex-1">
            <div class="px-3 text-neutral-800 flex-1 py-1">
                <div class="flex justify-between items-end px-1 font-[lpop]">
                    <div>
                        <span class="text-base">残 </span>
                        <span class="text-2xl font-bold">{{ format_number(gacha.count_rest) }}</span>
                        <span class="text-base">/{{ format_number(gacha.count_card) }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="inline-block"><img class="w-5 h-5"
                                src="/images/icon_coin.png" /> </span>
                        <span class="font-bold text-2xl">{{ format_number(gacha.point) }}</span>
                        <span class="text-base"> pt</span>
                    </div>
                </div>
                <!-- <div class="rounded-corner-div mt-2 mb-3 p-[4px]">
                    <div
                        class="w-full h-8 overflow-hidden shadow border-[4px] flex justify-end border-neutral-800 bg-gradient-to-r from-[#dd742d] via-[#fae94e] to-[#fefec0] relative">
                        <div class="flex justify-between absolute h-full left-[-4px] right-[-4px]">
                            <div v-for="i in 12" class="border-[2px] h-full border-neutral-800"></div>
                        </div>
                        <div class="h-full text-right bg-neutral-800" :style="{ width: get_progress_value() + '%' }">
                        </div>
                    </div>
                </div> -->
                <vue-countdown :time="gacha.remaining * 1000" :transform="transform"
                    v-slot="{ days, hours, minutes, seconds }" class="flex flex-wrap mt-1 items-center justify-between"
                    @end="countdown = false" v-if="countdown && gacha.remaining > 0">
                    <span v-if="gacha.timeStatus == 0">発売まであと</span>
                    <span v-if="gacha.timeStatus == 1">終了まで:</span>
                    <span>
                        <!-- {{ gacha.start_time }} -->

                        <span class="text-red-600 font-black text-xl leading-[1] align-bottom" style="font-family: 'kei-font';">
                            <span v-if="days > 0" class="mr-3">{{ days }}日</span>{{ hours }}:{{ minutes }}:{{ seconds }}
                        </span>
                        です！
                    </span>

                </vue-countdown>
            </div>
            <div v-if="!url_edit">
                <GachaButtons :gacha="gacha" />
            </div>
        </div>
    </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { PlayIcon } from "@heroicons/vue/24/solid";
import GachaButtons from '@/Parts/GachaButtons.vue';
import VueCountdown from '@chenfengyuan/vue-countdown';

export default {
    components: { Link, PlayIcon, GachaButtons, VueCountdown },
    props: {
        gacha: Object,
        url_edit: String,
    },
    data() {
        return {
            category_share: usePage().props.value.category_share,
            url_card: "",
            str_gacha10: "",
            point_10gacha: 0,
            countdown: true,
        };
    },
    methods: {
        get_progress_value() {
            return Math.round(this.gacha.count * 100.0 / this.gacha.count_card)
        },
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },

        getImageClass(image) {
            return "url('" + image + "')";
        },

        clickCard() {
            if (this.gacha.count_rest > 0 && (!this.url_edit)) {
                window.location.href = this.url_card;
            }
        },


        destroyGacha(id) {
            if (confirm("削除してもいいですか？")) {
                Inertia.delete(route('admin.gacha.destroy', { 'id': id }), { preserveScroll: true });
            }
        },
        transform(props) {
            Object.entries(props).forEach(([key, value]) => {
                // Adds leading zero
                const digits = value < 10 && key != 'days' ? `0${value}` : value;

                // uses singular form when the value is less than 2
                const word = value < 2 ? key.replace(/s$/, '') : key;

                props[key] = `${digits}`;
            });

            return props;
        },
    },
    mounted() {


        if (this.gacha.count_rest < 10) {
            this.str_gacha10 = "全て";
            this.point_10gacha = this.gacha.count_rest * this.gacha.point;
        } else {
            this.str_gacha10 = "10連";
            this.point_10gacha = 10 * this.gacha.point;
        }

        if (!this.url_edit) {
            this.url_card = route('user.gacha', this.gacha.id) + this.category_share.cat_route_appendix;
        }
    }
}
</script>