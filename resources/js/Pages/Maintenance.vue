<template>
    <UserLayout>
        <div class="w-full mx-auto md:w-[768px] text-neutral-300 px-4">
            <h1 class="mb-2 text-lg font-bold pt-6">メンテナンス中</h1>
            <hr class="mb-8" />
            <div class="mb-8">
                ただいまメンテナンスのため一時サービスを停止しております。<br/><br/>
                大変ご不便、ご迷惑をおかけいたしますが、 今しばらくお待ちください。 <br/>
                その他、サービスのお問い合わせは、下記TwitterのDMまでご連絡ください。<br/><br/>
                【お問い合わせ】<br/>
                <a class="hover:text-cyan-800 text-cyan-600 " href="https://twitter.com/Raffle-pro">https://twitter.com/Raffle-pro</a>
            </div>
        </div>
    </UserLayout>

</template>

<script>
    import UserLayout from '@/Layouts/UserLayout.vue';
    export default {
        components: {UserLayout},
    }
</script>