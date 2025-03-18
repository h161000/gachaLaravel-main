<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.value.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-neutral-600">プロフィール情報</h2>
            <p class="mt-1 text-sm text-neutral-600">アカウントのプロフィール情報と電子メール アドレスを更新します。</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6 ">
            <div>
                <InputLabel for="name" value="名前" class="text-neutral-600 pl-2"/>
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="電子メールアドレス" class="text-neutral-600 pl-2" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="email" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div v-if="user.email_verified_at === null">
                
                <p class="text-sm mt-2 text-neutral-600">
                    メールアドレスは認証されていません。
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="mt-2 underline text-sm text-neutral-600 hover:text-neutral-600 rounded-md"
                    >
                    確認メールを再送信するには、ここをクリックしてください。
                    </Link>
                </p>
                <div v-show="props.status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-yellow-500">
                    新しい確認リンクがあなたの電子メール アドレスに送信されました。
                </div>
            </div>

            <div class="flex gap-4 items-center justify-end">
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-neutral-600">保存されました。</p>
                </Transition>
                <div class="text-center border border-[#0ea5e9] rounded-full p-0.5 w-fit">
                    <button type="submit" :class="{ 'opacity-50': form.processing }" :disabled="form.processing" class="inline-block items-center px-10 py-1 bg-theme bg-theme-hover rounded-full font-semibold text-base text-white uppercase tracking-widest">
                        保存
                    </button>
                </div>
            </div>
        </form>
    </section>
</template>
