<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/inertia-vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: Boolean,
});
</script>

<template>
    <Head title="プロフィール" />

   
    <UserLayout>
        <div class="w-full flex justify-center pt-4">
            <div class="md:w-[768px] w-full px-4 md:px-6 lg:px-8">
                <h2 class="font-semibold text-base md:text-lg text-neutral-600 leading-tight px-2">
                    プロフィール
                </h2>
                <div class="w-full py-4">
                    <div class="mx-auto space-y-6">
                        <div class="p-4 sm:p-8 bg-white/70 border shadow rounded-lg shadow-sky-300">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                
                            />
                        </div>

                        <div class="p-4 sm:p-8 bg-white/70 border shadow rounded-lg shadow-sky-300">
                            <UpdatePasswordForm  />
                        </div>

                        <!-- <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <DeleteUserForm  />
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        
    </UserLayout>
</template>
