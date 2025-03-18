<template>
    <div class="text-neutral-700 font-semibold space-y-2 px-3 text-sm">
        <div class="h-[4.5rem] px-2 text-lg font-bold flex items-center">{{ menu_title }}</div>
        <template v-for="item in main_menu">

            <div :class="[isActive(item.route_name) ? 'bg-theme text-white ' : 'bg-white ']"
                class="relative flex cursor-pointer rounded-lg bg-theme-hover hover:text-white">
                <Link v-if="item.type == 'link'" :href="route(item.route_name)" class="py-2 px-5 w-full text-start" as="button"> {{ item.title }}
                </Link>
            </div>

            <!-- <li v-if="item.type == 'link'" :class="getActiveColor(item.route_name)"
                class=" text-sm mx-6 my-2 border hover:bg-zinc-200">
                
            </li> -->

        </template>
        <div class="py-2"></div>
        <div class="relative flex cursor-pointer rounded-lg bg-theme-hover hover:text-white bg-white">
            <Link :href="route('logout')" method="get" class="py-2 px-5 w-full text-start" as="button">ログアウト</Link>
        </div>
    </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/inertia-vue3';
import admin_menu from '@/Store/admin-menu';
import user_menu from '@/Store/user-menu';
import staff_menu from '@/Store/staff-menu';
export default {
    components: { Link },
    data() {
        return {
            main_menu: [],
            current_route: "",
            menu_title: "管理者メニュー",
        }
    },
    mounted() {
        this.current_route = route().current();

        let user = usePage().props.value.auth.user;
        if (user) {
            if (user.type == 1) {
                this.main_menu = admin_menu;
                this.menu_title = "管理者メニュー";
            } else if (user.type == 2) {
                this.main_menu = staff_menu;
                this.menu_title = "職員ページ";
            } else {
                this.main_menu = user_menu;
                this.menu_title = "マイページ";
            }
            this.main_menu = [
                {
                    title: "プロフィール",
                    route_name: "profile.edit",
                    type: "link",
                },
                ...this.main_menu
            ];
        } else {
            this.main_menu = [];
        }


    },
    methods: {
        isActive(route_name) {
            return this.current_route.indexOf(route_name) !== -1;
        }
    },
}

</script>