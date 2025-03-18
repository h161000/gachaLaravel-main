<template>
    <AdminLayout>
        <Head title="特定商取引法に基づく表記" />
        <div class="pt-6 md:px-2 px-4">  
            <h1 class="mb-2 text-lg font-bold">特定商取引法に基づく表記</h1>
            <hr class="mb-8" />
            <div class="mb-8 text-sm">
                <h2 class="text-sm font-semibold mb-1">運営</h2>
                **
                <br/><br/>

                <h2 class="text-sm font-semibold mb-1">代表責任者</h2>
                ** **
                <br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">所在地</h2>
                〒***-****<br/>
                ***
                <br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">連絡先</h2>
                電話：****-**-**<br/>
                担当：***-****-****<br/>
                メール：support@raffle-pro.com<br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">価格</h2>
                商品毎に表示しております。
                <br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">利用者の商品代金以外の料金</h2>
                消費税、銀行振込時の代金支払いの振込手数料。
                <br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">お支払い方法・お支払時期</h2>
                Stripe決済システムをご利用いただけます。<br/>
                <br/>
                
                <h2 class="text-sm font-semibold mb-1">返品と期限</h2>
                1. 基本的に返品及び交換は致しません。<br/>
                但し、当社に100％過失がある場合は考慮いたします。<br/>
                なお、返品時の送料は当社が負担致します。<br/><br/>

                2. 鑑定品については弊社にて検品を行なっておりますがカードの状態やケースに微傷がある場合がございます。交換対応致しませんのでご了承の上、ご購入をお願いいたします。<br/><br/>

                3. BOXについては弊社にて検品を行なっておりますがごく稀にシュリンク破れ、傷、再シュリンク品が紛れてしまう可能性がございます。<br/>
                再シュリンク品につきましはすり替え防止の為、開封時は動画を撮影いただけますと交換対応を行います。
                <br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">商品の状態</h2>
                カードの状態は確認した上で表記しておりますが、傷の見逃しや配送時に出来てしまった傷などがある場合がございます。こちらもご了承いただき、オリパの購入をお願いいたします。
                <br/><br/>

                <h2 class="text-sm font-semibold mb-1">購入上限</h2>
                1回あたり100万ptまでとする。
                <br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">ポイントの有効期限</h2>
                サービスの利用者はポイントを獲得してから180日です。有効期限を超えたものは、当社が告知することなくポイントを抹消できるものとします。
                <br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">配送時期</h2>
                発送依頼を受けた日から、1日〜12日以内で発送いたします。<br/>
                配送方法はレターパック（ライト・プラス）、クリップポスト、クロネコヤマトにてお送りさせていただきます。<br/><br/><br/>
                
                <h2 class="text-sm font-semibold mb-1">許認可と資格</h2>
                古物商営業許可<br/>
                第***<br/>
                ***公安委員会<br/>
                ****
                <br/><br/>
                
            </div>
        </div>
        
    </AdminLayout>

</template>
<script>
    import { Link, Head } from '@inertiajs/inertia-vue3';
    import AdminLayout from '@/Layouts/Admin.vue';
    export default {
        components: { Link, AdminLayout, Head },
    }
</script>