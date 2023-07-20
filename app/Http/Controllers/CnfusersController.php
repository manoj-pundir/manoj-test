<?php
namespace App\Http\Controllers;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CnfusersController;
use App\Models\Cnfusers;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreCnfusersRequest;
use App\Http\Requests\UpdateCnfusersRequest;
use DB;

class CnfusersController extends Controller{
    public function index(){
        if(auth()->id()==1){
            $users = Cnfusers::latest()->paginate(10);
        }else{
            $users = Cnfusers::where('super_cnf_id',auth()->id())->latest()->paginate(10);
        }
        return view('cnfuser.index', compact('users'));
    }

    public function create(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/getBankList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'hash: D+Nwt9hHhH3pPUXxdgv2aWDDNQc+mqCZ+EjnosvJQqw=',
                'ownerType: SUPERCNF',
                'ownerId: 1',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $banklist = json_decode($response, true);
        curl_close($curl);
        $state = DB::table('geo_locations')->where('location_type', 'STATE')->orderBy('id')->get();
        return view('cnfuser.create', compact('state', 'banklist'));
    }

    public function store(Cnfusers $user, StoreCnfusersRequest $request){
        if(!empty($request->service)){
            $service = implode(",",$request->service);
        }else{
            $service = "";
        }
        $orgDate = $request->date_of_birth;  
        $date = str_replace('/"', '-', $orgDate);  
        $newDate = date("d-m-Y", strtotime($date));

        $dataArr = array(
            'super_cnf_id'=>auth()->id(),
            "name"=>$request->first_name." ".$request->last_name,
            "first_name"=>$request->first_name,
            "last_name"=>$request->last_name,
            "email"=>$request->email,
            "password"=>"12345678",
            "app_id"=>$request->app_id,

            "date_of_birth"=>$newDate,
            "gender"=>$request->gender,
            "addressLine1"=>$request->address,
            "country"=>$request->country,
            "state"=>$request->state,
            "district"=>$request->district,
            "tehsil"=>$request->tehsil,
            "pincode"=>$request->pincode,
            "police_station"=>$request->police_station,
            "aadhar_number"=>$request->aadhar_number,
            "pan_number"=>$request->pan_number,
            "mobile_number"=>$request->mobile_number,
            "alternate_mobile_number"=>$request->alternate_mobile_number,
            "telephone_number"=>$request->telephone_number,
            "gst_number"=>$request->gst_number,
            "ret_price"=>$request->ret_price,
            "dist_price"=>$request->dist_price,
            "super_dist_price"=>$request->super_dist_price,
            "cnf_type"=>$request->cnf_type,
            "brandName"=>$request->brandName,
            "displayName"=>$request->displayName,
            "companyName"=>$request->companyName,
            "url"=>$request->url,
            "smsKeyword"=>$request->smsKeyword,
            "emailAddress"=>$request->emailAddress,
            "logoUrl"=>$request->logoUrl,
            "videosUrl"=>$request->videosUrl,
            "service"=>$service,
            "account_holder_name"=>$request->account_holder_name,
            "bank_name"=>$request->bank_name,
            "account_type"=>$request->account_type,
            "account_number"=>$request->account_number,
            "ifsc_code"=>$request->ifsc_code,
            "status"=>$request->status
        );
        $user->create($dataArr);
        return redirect()->route('cnfuser.index')->withSuccess(__('Cnfuser created successfully.'));
    }

    public function show(Cnfusers $user){
        $supercnf = DB::table('super_cnf')->where('status', 'active')->orderBy('id')->get();
        $state = DB::table('geo_locations')->where('location_type', 'STATE')->orderBy('id')->get();
        $district = DB::table('geo_locations')->where('location_type', 'district')->orderBy('id')->get();
        $tehsil = DB::table('geo_locations')->where('location_type', 'tehsil')->orderBy('id')->get();
        return view('cnfuser.show', [
            'user' => $user, 'supercnf'=>$supercnf, 'state'=>$state, 'district'=>$district, 'tehsil'=>$tehsil
        ]);
    }

    public function edit(Cnfusers $user){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/getBankList',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'hash: D+Nwt9hHhH3pPUXxdgv2aWDDNQc+mqCZ+EjnosvJQqw=',
                'ownerType: SUPERCNF',
                'ownerId: 1',
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $banklist = json_decode($response, true);
        curl_close($curl);
        $state = DB::table('geo_locations')->where('location_type', 'STATE')->orderBy('id')->get();
        $district = DB::table('geo_locations')->where('location_type', 'district')->orderBy('id')->get();
        $tehsil = DB::table('geo_locations')->where('location_type', 'tehsil')->orderBy('id')->get();
        return view('cnfuser.edit', [
            'user' => $user, 'banklist'=>$banklist, 'state'=>$state, 'district'=>$district, 'tehsil'=>$tehsil
        ]);
    }

    public function update(Cnfusers $user, UpdateCnfusersRequest $request){
        if(!empty($request->service)){
            $service = implode(",",$request->service);
        }else{
            $service = "";
        }
        $orgDate = $request->date_of_birth;  
        $date = str_replace('/"', '-', $orgDate);  
        $newDate = date("d-m-Y", strtotime($date));

        $dataArr = array(
            'super_cnf_id'=>auth()->id(),
            "name"=>$request->first_name." ".$request->last_name,
            "first_name"=>$request->first_name,
            "last_name"=>$request->last_name,
            "email"=>$request->email,
            "app_id"=>$request->app_id,

            "date_of_birth"=>$newDate,
            "gender"=>$request->gender,
            "addressLine1"=>$request->address,
            "country"=>$request->country,
            "state"=>$request->state,
            "district"=>$request->district,
            "tehsil"=>$request->tehsil,
            "pincode"=>$request->pincode,
            "police_station"=>$request->police_station,
            "aadhar_number"=>$request->aadhar_number,
            "pan_number"=>$request->pan_number,
            "mobile_number"=>$request->mobile_number,
            "alternate_mobile_number"=>$request->alternate_mobile_number,
            "telephone_number"=>$request->telephone_number,
            "gst_number"=>$request->gst_number,
            "ret_price"=>$request->ret_price,
            "dist_price"=>$request->dist_price,
            "super_dist_price"=>$request->super_dist_price,
            "cnf_type"=>$request->cnf_type,
            "brandName"=>$request->brandName,
            "displayName"=>$request->displayName,
            "companyName"=>$request->companyName,
            "url"=>$request->url,
            "smsKeyword"=>$request->smsKeyword,
            "emailAddress"=>$request->emailAddress,
            "logoUrl"=>$request->logoUrl,
            "videosUrl"=>$request->videosUrl,
            "service"=>$service,
            "account_holder_name"=>$request->account_holder_name,
            "bank_name"=>$request->bank_name,
            "account_type"=>$request->account_type,
            "account_number"=>$request->account_number,
            "ifsc_code"=>$request->ifsc_code,
            "status"=>$request->status
        );
        $user->update($dataArr);
        return redirect()->route('cnfuser.index')->withSuccess(__('Cnfuser updated successfully.'));
    }

    public function getAdhaarVerify(Cnfusers $user, Request $request){
        $aadhar_number = $request->aadhar_number;
        $adhardata = $user::select('*')->where('aadhar_number', $aadhar_number)->get();
        if(sizeof($adhardata)==0){
            $arr = array('status'=>'true', 'msg'=>'This adhaar number is valid.');
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'This adhaar number is already in used.');
            echo json_encode($arr);
            die();
        }
        //return response()->json($response);
    }

    public function getPancardVerify(Cnfusers $user, Request $request){
        $pan_number = $request->pan_number;
        $pandata = $user::select('*')->where('pan_number', $pan_number)->get();
        if(sizeof($pandata)==0){
            $arr = array('status'=>'true', 'msg'=>'This pancard number is valid.');
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'This pancard number is already in used.');
            echo json_encode($arr);
            die();
        }
    }

    public function getLocation(Request $request){
        $id = $request->id;
        $type = $request->type;
        $query = DB::table('geo_locations')->where('location_type', $type)->where('parent_id', $id)->orderBy('id')->get();
        if(sizeof($query)>0){
            $a=array();
            foreach ($query as $value){
                $hmtl = '<option value='.$value->id.'>'.$value->name.'</option>';
                array_push($a,$hmtl);
            }
            $arr = array('status'=>'true', 'msg'=>$type.' list found successfully', 'data'=>$a);
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'Sorry no '.$type.' found for this state.');
            echo json_encode($arr);
            die();
        }
    }

    public function calculateHmac($payload, $secret) {
        $key = hash_hmac('sha256', $payload, $secret, true);
        $result = base64_encode($key);
        return $result;
    }

    public function paymonkCnfList(User $user, Request $request){
        $userdata = $user::select('*')->where('id', auth()->id())->get();
        //echo "Auth Id===".auth()->id()."<br>";
        $method = "POST";
        $mobile = $userdata[0]->mobile_number;
        $aadhar = $userdata[0]->aadhar_number;
        $headerVal= $mobile."|".$aadhar;
        $key = $userdata[0]->mid_secret_key;

        /*
        $has = hash_hmac('sha256', $headerVal, $key, true);
        $hashKey =  base64_encode($has);
        */

        $hashKey =  $this->calculateHmac($headerVal, $key);

        $header = [
            'hash:'.$hashKey,
            'ownerType:SUPERCNF',
            'ownerId:'.auth()->id(),
            'Content-Type:application/json'
        ];
        $request = '{"searchObject":{"appId":"SHREEDHAN","superCnfId":'.auth()->id().',"status":"ACTIVE"},"paging":{"pageNo":1,"count":20},"sortingOrder":{"field":"id","value":"desc"}}';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/getcnf',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT=>180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => $header
        ));
        $result = curl_exec($curl);
        $array = json_decode($result, true);
        if(sizeof($array['result']['payload'])>0 && sizeof($array['result']['payload']['searchObjectList'])>0){
            $a=array('<option>---- Choose Cnf ----</option>');
            foreach ($array['result']['payload']['searchObjectList'] as $value){
                $hmtl = '<option value='.$value['id'].'>'.$value['name'].'</option>';
                array_push($a,$hmtl);
            }
            $arr = array('status'=>'true','msg'=>'Cnf User Record Found Here','data'=>$a);
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'Cnf User Record Not Found');
            echo json_encode($arr);
            die();
        }
    }

    public function paymonkCnfPassbook(User $user, Request $request){
        $userdata = $user::select('*')->where('id', auth()->id())->get();
        $method = "POST";
        $mobile=$userdata[0]->mobile_number;
        $aadhar = $userdata[0]->aadhar_number;
        $headerVal= $mobile."|".$aadhar;
        $key = $userdata[0]->mid_secret_key;

        $has = hash_hmac('sha256', $headerVal, $key, true);
        $hashKey = base64_encode($has);
        $header = [
            'hash:'.$hashKey,
            'cache-control:no-cache',
            'content-type:application/json',
            'ownerId:'.auth()->id(),
            'ownerType:SUPERCNF'
        ];
        $request = '{"searchObject":{"ownerId":'.$request->id.',"ownerType":"RETAILER","status":"SUCCESS"},"paging":{"pageNo":1,"count":20},"sortingOrder":{"field":"id","value":"desc"}}';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/walletpassbook/search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT=>180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => $header
        ));
        $result = curl_exec($curl);
        $array = json_decode($result, true);
        if($array['result']['status']=="SUCCESS" && sizeof($array['result']['response'])>0){
            //print_r($array['result']['response']['searchObjectList']);
            $a=array();
            $i=1;
            foreach($array['result']['response']['searchObjectList'] as $value){
                $hmtl = '<tr>
                        <td>'.$i.'</td>
                        <td>'.$value['txnDate'].'</td>
                        <td>'.$value['orderId'].'</td>
                        <td>'.$value['message'].'</td>
                        <td>'.$value['id'].'</td>
                        <td>'.$value['txnAmount'].'</td>
                        <td>'.$value['commissionAmount'].'</td>
                        <td>'.$value['markUpAmount'].'</td>
                        <td>'.$value['balance'].'</td>
                        <td>'.$value['tdsAmount'].'</td>
                        <td>'.$value['txnFlow'].'</td>
                        <td>'.$value['txnType'].'</td>
                        <td>'.$value['status'].'</td>        
                        <td>'.$value['comment'].'</td>
                    </tr>
                ';
                array_push($a,$hmtl);
            $i++; }

            $arr = array('status'=>'true','msg'=>'Cnf User Record Found Here','data'=>$a);
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'Cnf User Record Not Found');
            echo json_encode($arr);
            die();
        }
    }

    public function paymonkCnfDistributor(User $user, Request $request){
        $userdata = $user::select('*')->where('id', auth()->id())->get();
        //print_r($userdata[0]->mid_secret_key);
        $method = "POST";
        $mobile=$userdata[0]->mobile_number;
        $aadhar = $userdata[0]->aadhar_number;
        $headerVal= $mobile."|".$aadhar;
        $key = $userdata[0]->mid_secret_key;

        $has = hash_hmac('sha256', $headerVal, $key, true);
        $hashKey = base64_encode($has);
        $header = [
            'hash:'.$hashKey,
            'cache-control:no-cache',
            'content-type:application/json',
            'ownerId:'.auth()->id(),
            'ownerType:SUPERCNF'
        ];
        $request = '{"searchObject":{"appId":"SHREEDHAN","cnfId":'.$request->id.',"status":"ACTIVE","dropDown":true}}';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/getDistributor',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT=>180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => $header
        ));
        $result = curl_exec($curl);
        $array = json_decode($result, true);
        if(sizeof($array['result']['payload'])>0 && $array['result']['payload']['searchObjectList']>0){
            //print_r($array['result']['response']['searchObjectList']);
            $a=array('<option>---- Choose Distributor ----</option>');
            foreach ($array['result']['payload']['searchObjectList'] as $value){
                $hmtl = '<option value='.$value['id'].'>'.$value['name'].'</option>';
                array_push($a,$hmtl);
            }
            $arr = array('status'=>'true','msg'=>'Cnf Distributor Record Found Here','data'=>$a);
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'Cnf Distributor Record Not Found');
            echo json_encode($arr);
            die();
        }
    }

    public function paymonkCnfDistPassbook(User $user, Request $request){
        $userdata = $user::select('*')->where('id', auth()->id())->get();
        //print_r($userdata[0]->mid_secret_key);
        $method = "POST";
        $mobile=$userdata[0]->mobile_number;
        $aadhar = $userdata[0]->aadhar_number;
        $headerVal= $mobile."|".$aadhar;
        $key = $userdata[0]->mid_secret_key;

        $has = hash_hmac('sha256', $headerVal, $key, true);
        $hashKey = base64_encode($has);
        $header = [
            'hash:'.$hashKey,
            'cache-control:no-cache',
            'content-type:application/json',
            'ownerId:'.auth()->id(),
            'ownerType:SUPERCNF'
        ];
        $request = '{"searchObject":{"ownerId":'.$request->id.',"ownerType":"RETAILER","status":"SUCCESS"},"paging":{"pageNo":1,"count":20},"sortingOrder":{"field":"id","value":"desc"}}';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/walletpassbook/search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT=>180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => $header
        ));
        $result = curl_exec($curl);
        $array = json_decode($result, true);
        if($array['result']['status']=="SUCCESS" && sizeof($array['result']['response'])>0 && $array['result']['response']['searchCount']>0){
            $a=array();
            $i=1;
            foreach($array['result']['response']['searchObjectList'] as $value){
                $hmtl = '<tr>
                        <td>'.$i.'</td>
                        <td>'.$value['txnDate'].'</td>
                        <td>'.$value['orderId'].'</td>
                        <td>'.$value['message'].'</td>
                        <td>'.$value['id'].'</td>
                        <td>'.$value['txnAmount'].'</td>
                        <td>'.$value['commissionAmount'].'</td>
                        <td>'.$value['markUpAmount'].'</td>
                        <td>'.$value['balance'].'</td>
                        <td>'.$value['tdsAmount'].'</td>
                        <td>'.$value['txnFlow'].'</td>
                        <td>'.$value['txnType'].'</td>
                        <td>'.$value['status'].'</td>        
                        <td>'.$value['comment'].'</td>
                    </tr>
                ';
                array_push($a,$hmtl);
            $i++; }

            $arr = array('status'=>'true','msg'=>'Cnf User Record Found Here','data'=>$a);
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'Cnf User Record Not Found');
            echo json_encode($arr);
            die();
        }
    }

    public function paymonkCnfRetailer(User $user, Request $request){
        $userdata = $user::select('*')->where('id', auth()->id())->get();
        //print_r($userdata[0]->mid_secret_key);
        $method = "POST";
        $mobile=$userdata[0]->mobile_number;
        $aadhar = $userdata[0]->aadhar_number;
        $headerVal= $mobile."|".$aadhar;
        $key = $userdata[0]->mid_secret_key;

        $has = hash_hmac('sha256', $headerVal, $key, true);
        $hashKey = base64_encode($has);
        $header = [
            'hash:'.$hashKey,
            'cache-control:no-cache',
            'content-type:application/json',
            'ownerId:'.auth()->id(),
            'ownerType:SUPERCNF'
        ];
        $request = '{"searchObject":{"appId":"SHREEDHAN","status":"ACTIVE","distributorId":'.$request->id.'},"paging":{"pageNo":1,"count":20},"sortingOrder":{"field":"id","value":"desc"}}';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/getRetailer',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT=>180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => $header
        ));
        $result = curl_exec($curl);
        $array = json_decode($result, true);
        if(sizeof($array['result']['payload'])>0 && $array['result']['payload']['searchObjectList']>0){
            //print_r($array['result']['response']['searchObjectList']);
            $a=array('<option>---- Choose Retailer ----</option>');
            foreach ($array['result']['payload']['searchObjectList'] as $value){
                $hmtl = '<option value='.$value['id'].'>'.$value['name'].'</option>';
                array_push($a,$hmtl);
            }
            $arr = array('status'=>'true','msg'=>'Retailer Record Found Here','data'=>$a);
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'Retailer Record Not Found');
            echo json_encode($arr);
            die();
        }
    }

    public function paymonkRetailerPassbook(User $user, Request $request){
        $userdata = $user::select('*')->where('id', auth()->id())->get();
        //print_r($userdata[0]->mid_secret_key);
        $method = "POST";
        $mobile=$userdata[0]->mobile_number;
        $aadhar = $userdata[0]->aadhar_number;
        $headerVal= $mobile."|".$aadhar;
        $key = $userdata[0]->mid_secret_key;

        $has = hash_hmac('sha256', $headerVal, $key, true);
        $hashKey = base64_encode($has);
        $header = [
            'hash:'.$hashKey,
            'cache-control:no-cache',
            'content-type:application/json',
            'ownerId:'.auth()->id(),
            'ownerType:SUPERCNF'
        ];
        $request = '{"searchObject":{"ownerId":'.$request->id.',"ownerType":"RETAILER","status":"SUCCESS"},"paging":{"pageNo":1,"count":20},"sortingOrder":{"field":"id","value":"desc"}}';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://preprod.paymonk.com/megatron/api/reseller/walletpassbook/search',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT=>180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => $header
        ));
        $result = curl_exec($curl);
        $array = json_decode($result, true);
        if($array['result']['status']=="SUCCESS" && sizeof($array['result']['response'])>0 && $array['result']['response']['searchCount']>0){
            $a=array();
            $i=1;
            foreach($array['result']['response']['searchObjectList'] as $value){
                $hmtl = '<tr>
                        <td>'.$i.'</td>
                        <td>'.$value['txnDate'].'</td>
                        <td>'.$value['orderId'].'</td>
                        <td>'.$value['message'].'</td>
                        <td>'.$value['id'].'</td>
                        <td>'.$value['txnAmount'].'</td>
                        <td>'.$value['commissionAmount'].'</td>
                        <td>'.$value['markUpAmount'].'</td>
                        <td>'.$value['balance'].'</td>
                        <td>'.$value['tdsAmount'].'</td>
                        <td>'.$value['txnFlow'].'</td>
                        <td>'.$value['txnType'].'</td>
                        <td>'.$value['status'].'</td>        
                        <td>'.$value['comment'].'</td>
                    </tr>
                ';
                array_push($a,$hmtl);
            $i++; }

            $arr = array('status'=>'true','msg'=>'Cnf Wallet Passbook Record Found Here','data'=>$a);
            echo json_encode($arr);
            die();
        }else{
            $arr = array('status'=>'false', 'msg'=>'Cnf Wallet Passbook Record Not Found');
            echo json_encode($arr);
            die();
        }
    }

    public function destroy(Cnfusers $user){
        $user->delete();
        return redirect()->route('cnfuser.index')->withSuccess(__('User deleted successfully.'));
    }

}