<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Point;
use App\Models\Payment;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Profile;
use App\Http\Controllers\PointHistoryController;

use Auth;
use Str;
use \Exception;

class PaymentController extends Controller
{
    public $config = [
        'testOrLive' => 'live',
        'is3DSecure' => '1',
        'fincode_public_key' => '',
        'fincode_secret_key' => '',
        'apiDomain' => '',
    ];

    protected function set_config() {
        $this->config['testOrLive'] = getOption('testOrLive');
        $this->config['is3DSecure'] = getOption('is3DSecure');

        if ($this->config['testOrLive'] =="test") {
            $this->config['fincode_public_key'] = env('FINCODE_TEST_API_KEY');
            $this->config['fincode_secret_key'] = env('FINCODE_TEST_SECRET_KEY');
            $this->config['apiDomain'] = "https://api.test.fincode.jp";
        } else {
            $this->config['fincode_public_key'] = env('FINCODE_LIVE_API_KEY');
            $this->config['fincode_secret_key'] = env('FINCODE_LIVE_SECRET_KEY');
            $this->config['apiDomain'] = "https://api.fincode.jp";
        }
    }

    public function do_request($apiPath, $method, $requestParams) {
        $res = [
            'status' => 1,
            'response' => '',
            'httpcode' => '0',
            'error' => '',
        ];

        try{
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $this->config['apiDomain'].$apiPath);
            curl_setopt($session, CURLOPT_CUSTOMREQUEST, $method);

            $headers = array(
                "Authorization: Bearer " . $this->config['fincode_secret_key'],
                "Content-Type: application/json"
                );
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            if ($requestParams) {
                $requestParamsJson = json_encode($requestParams);
                curl_setopt($session, CURLOPT_POSTFIELDS, $requestParamsJson);
            }
            

            curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($session);
            $httpcode = curl_getinfo($session, CURLINFO_HTTP_CODE);
            
            curl_close($session);
            $res['response'] = $response;
            $res['httpcode'] = $httpcode;

        } catch(Exception $e) {
            $text = "処理中に問題が発生しました！ " ;
            $res['status'] = 0;
            $res['error'] = $text;
        }

        return $res;
    }

    public function purchase_process(Request $request) {
        $this->set_config();

        $transaction = $request->all();
        unset($transaction['_token']);

        $user = auth()->user();
        if ($this->config['is3DSecure']=="1") {
            $transaction['tds2_ret_url'] = route('tds2_ret_url');  
        }
        
        $res = array();
        $res['status'] = '0';
        
        $apiPath = "/v1/payments/".$transaction['id'];
        $method = 'PUT';
        $requestParams = $transaction;
        if ($this->config['is3DSecure']=="1") {
            $ans = $this->do_request($apiPath, $method, $requestParams);
        }
        else {
            $ans = $this->do_request($apiPath, $method, [
                'pay_type' => 'Card',
                'access_id' => $transaction['access_id'],
                'method' => $transaction['method'],
                'token' => $transaction['token'],
                'pay_times' => $transaction['pay_times'],
                'holder_name' => $transaction['holder_name'],
                'expire' => $transaction['expire']
            ]);
        }
        
        if ($ans['httpcode']!='200') {
            $res['message'] = "決済実行エラー！\n";
            $res['message'] .= $this->getErrorText($ans) ;
            $res['status'] = '0';
            return $res;
        }
        $json_data = json_decode($ans['response']);
        
        $order_id = $json_data->id;
        $access_id = $json_data->access_id;
        if ($json_data->status=="CAPTURED") {
            $payments = Payment::where('order_id', $order_id)->where('access_id', $access_id)->where('status', 0)->get();
            if(count($payments)) {
                $payment = $payments[0];
                $user = User::find($payment->user_id);
                $point = Point::find($payment->point_id);
                $sum = ((int)$point->point) + ((int)$user->point);
                $user->update(['point'=>$sum]);
                $payment->update(['status'=>1]);
                $res['status'] = $json_data->status;  // CAPTURED
            }
        }

        if ($json_data->status=="AUTHENTICATED") {
            $payments = Payment::where('order_id', $order_id)->where('access_id', $access_id)->where('status', 0)->get();
            if(count($payments)) {
                $payment = $payments[0];
                $payment->update(['status'=>2]);
            }

            $res['order_id'] = $order_id;  
            $res['access_id'] = $access_id;
            $res['job_code'] = $json_data->job_code;  // CAPTURE
            $res['acs_url'] = $json_data->acs_url; 
            $res['status'] = $json_data->status; // AUTHENTICATED
        }

        return json_encode($res);
    }

    public function tds2_ret_url(Request $request) {
        $text = "" ;

        $this->set_config();
        $access_id = $request->MD;        
        if ($request->event!="AuthResultReady") {
            $apiPath = "/v1/secure2/$access_id";
            $method = 'PUT';
            $requestParams = ['param' => $request->param];
            
            $ans = $this->do_request($apiPath, $method, $requestParams);
            if ($ans['httpcode']!='200') {
                $text = $this->getErrorText($ans);
            } else {
                $json_data = json_decode($ans['response']);
                $text = "" ;
                switch ($json_data->tds2_trans_result) {
                    case 'Y':
                        $ans = $this->payment_after_auth($access_id);
                        $text .= "\n" . $ans['error'];
                        break;
                    case 'C':
                        header('Location: '. $json_data->challenge_url); 
                        die();
                        exit();
                        break;
                    case 'A':
                        $ans = $this->payment_after_auth($access_id);
                        $text = "3Dセキュア利用ポリシー設定が認証必須の設定の場合はエラーです。(A)";
                        $text .= "\n" . $ans['error'];
                        break;
                    default:
                        $text = "認証失敗しました！ ($json_data->tds2_trans_result)";
                }
            }
        } else {
            
            $apiPath = "/v1/secure2/$access_id";
            $method = 'GET';
            $requestParams = [];
            $ans = $this->do_request($apiPath, $method, $requestParams);
            if ($ans['httpcode']!='200') {
                
                $text .= $this->getErrorText($ans);
            } else {
                $json_data = json_decode($ans['response']);
                if ($json_data->tds2_trans_result!='Y') {
                    $text = "チャレンジ認証失敗しました！($json_data->tds2_trans_result)" ;
                } else {
                    $res = $this->payment_after_auth($access_id);
                    $text .= "\n" . $res['error'];
                }
            }
        }

        
        $hide_back_btn = 1;
        $hide_cat_bar = 1;
        return inertia('NoProduct', compact('text', 'hide_back_btn', 'hide_cat_bar'));
    }

    public function payment_after_auth($access_id) {
        // 認証後決済
        $res = [
            'status' => 0,
            'error' => '',
        ];
        $payments = Payment::where('access_id', $access_id)->where('status', 2)->get();
        if (count($payments)) {
            $payment = $payments[0];
            $order_id = $payment->order_id;

            $apiPath = "/v1/payments/$order_id/secure";
            $method = 'PUT';
            $requestParams = ['pay_type'=>'Card', 'access_id'=>$access_id];
            $ans = $this->do_request($apiPath, $method, $requestParams);
            if ($ans['httpcode']!='200') {
                $res['error'] = "認証後決済エラー！\n";
                $res['error'] .= $this->getErrorText($ans);
                return $res;
            } else {
                $json_data = json_decode($ans['response']);
                
                if (isset($json_data->status)) {
                    $order_id = $json_data->id;
                    $access_id = $json_data->access_id;
                    if ($json_data->status=="CAPTURED") {
                        $payments = Payment::where('order_id', $order_id)->where('access_id', $access_id)->get();
                        if(count($payments)) {
                            $payment = $payments[0];
                            $user = User::find($payment->user_id);
                            $point = Point::find($payment->point_id);
                            $sum = ((int)$point->point) + ((int)$user->point);
                            $invite = Invitation::where('user_id', $user->id)->where('status', 0)->first();
                            if ($invite) {
                                $sum += 300;
                                $friend = User::find($invite->inviter);
                                if ($friend) {
                                    $friend->point += 300;
                                    $friend->save();
                                }
                            }
                            $user->update(['point'=>$sum]);
                            $payment->update(['status'=>1]);
                            $res['status'] = 1;  // CAPTURED
                            $redirect_uri = ($user->type==1)? 'test.purchase_success': 'purchase_success';
                            header('Location: '. route($redirect_uri)); 
                            die();
                            exit();
                        } 
                    }
                } else {
                    $res['error'] = "認証後決済エラー！\n";
                    $res['error'] .= '状態が存在しません。';
                }
            }
        } else {
            $res = [
                'status' => 0,
                'error' => 'データベースに取引が存在しません。',
            ];
        }
        return $res;
    }

    protected function getErrorText($data) {
        $error_print = "";
        try{
            
            $json_data = json_decode($data['response']);
            if ($json_data->errors) {
                foreach($json_data->errors as $item) {
                    $error_print .= "\n";
                    $error_print .= "$item->error_code : ";
                    $error_print .= "$item->error_message";
                }
            }
        } catch(Exception $e) {
            
        }
        return $error_print;
    }

    public function purchase ($id) {
        $this->set_config();
        
        $point = Point::find($id);
        if (!$point) {
            return redirect()->route('user.point');
        }
        
        $user = auth()->user();
        $is_admin = 0;
        if ($user) {
            if ( $user->type==1 ) {
                $is_admin = 1;
            }
        }
        
        $amount = $point->amount;

        $profile = Profile::where('user_id', $user->id)->first();

        $apiPath = "/v1/sessions";
        
        $expirationDate = now()->addDays(3)->format('Y/m/d H:i:s');
        $token = Str::random(64);
        $requestParams = [
            "success_url" => route('fincode_success', ['token' => $token]),
            "cancel_url" => route('fincode_cancel', ['token' => $token]),
            "expire" => $expirationDate,
            "receiver_mail" => auth()->user()->email,
            "mail_customer_name" => $profile ? $profile->first_name.$profile->last_name : auth()->user()->name,
            "guide_mail_send_flag" => "0",
            "thanks_mail_send_flag" => "0",
            // "shop_mail_template_id" => "test template id",
            "transaction" => [
                "pay_type" => [
                    "Card",
                    "Konbini",
                    "Paypay"
                ],
                "amount" => str($amount),
                // "tax" => "0",
                "client_field_1" => str($user->id),
                "client_field_2" => str($point->id),
            ],
            "konbini" => [
                "payment_term_day" => "3",
                "konbini_reception_mail_send_flag" => "1"
            ],
            "card" => [
                "job_code" => "CAPTURE",
                "tds_type" => $this->config['is3DSecure'] == "1" ? "2" : "0"
            ],
            "paypay" => [
                "job_code" => "CAPTURE"
            ],
        ];
        $res = $this->do_request($apiPath, 'POST', $requestParams);
        
        if ($res['httpcode'] == 200) {
            $res = json_decode($res['response']);
            $user->forceFill([
                'remember_token' => $token
            ])->save();

            return redirect($res->link_url);
            // return [
            //     'status' => 1,
            //     'link_url' => $res->link_url
            // ];
        }
        $text = "決済登録エラー！\n";
        $text .= $this->getErrorText($res) ;
        $hide_cat_bar = 1;
        return inertia('NoProduct', compact('text', 'hide_cat_bar'));
    }

    public function webhook (Request $request) {
        if (isset($request->pay_type) && isset($request->status)) {
            $point = Point::find($request->client_field_2);
            $user = User::find($request->client_field_1);

            if ($point && $user) {
                // if ($request->pay_type == 'Card' || $request->pay_type == 'Konbini') {
                    
                    if ($request->status == 'CAPTURED') {
                        $payment = Payment::where('order_id', $request->order_id)->first();
                        if ($payment && $payment->status == 0) {
                            (new PointHistoryController)->create($user->id, $user->point, $point->point, 'purchase', $payment->id);
                            $payment->update(['status' => 1]);
                            $user->point += $point->point;
                            $user->save();
                        }
                    }
                    else if (str_ends_with($request->event, 'regist')) {
                        Payment::Create([
                            'user_id' => $user->id,
                            'point_id' => $point->id,
                            'access_id' => $request->access_id,
                            'order_id' => $request->order_id,
                            'pay_type' => $request->pay_type,
                        ]);
                    }
                // }
                
            }

            return response()->json(['receive' => "0"]);
        }
        else {
            return response()->json(['receive' => "1"]);
        }
    }

    public function fincode_success(Request $request) {
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if ($user) {
            Auth::loginUsingId($user->id);
            $user->forceFill(['remember_token' => null])->save();
            return redirect()->route(($user->type == 1)? 'test.purchase_success': 'purchase_success');
        }
        return redirect()->route('main');
    }

    public function fincode_cancel(Request $request) {
        $token = $request->token;
        $user = User::where('remember_token', $token)->first();
        if ($user) {
            Auth::loginUsingId($user->id);
            $user->forceFill(['remember_token' => null])->save();
            return redirect()->route(($user->type == 1)? 'test.user.point': 'user.point');
        }
        return redirect()->route('main');
    }
}
