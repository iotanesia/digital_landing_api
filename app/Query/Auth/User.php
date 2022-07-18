<?php

namespace App\Query\Auth;
use App\Models\Auth\User as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;
use App\Mail\ResetPasswordMail;
use App\Models\Auth\UserRole;
use Illuminate\Support\Facades\Mail;

class User {

    public static function authenticateuser($params)
    {
        $required_params = [];
        if (!$params->nirk) $required_params[] = 'nirk';
        if (!$params->password) $required_params[] = 'password';
        if (count($required_params)) throw new \Exception("Parameter berikut harus diisi: " . implode(", ", $required_params));

        $user = Model::with([
            'manyUserRole' => function ($query){
                $query->where('is_current','<>',UserRole::is_current);
            },
            'refUserRole' => function ($query){
                $query->where('is_current',UserRole::is_current);
            },
        ])->where(function ($query) use ($params){
            $query->where('nirk',$params->nirk);
        })->first();
        if(!$user) throw new \Exception("Pengguna belum terdaftar.");
        // if (!Hash::check($params->password, $user->password)) throw new \Exception("Email atau password salah.",400);
        $user->roles = $user->manyUserRole->map(function ($item){
            return [
                'id_role' => $item->id_role,
                'nama_role' => $item->refRole->nama ?? null,
            ];
        });
        $user->id_role = $user->refUserRole->id_role ?? null;
        $user->nama_role = $user->refUserRole->refRole->nama ?? null;
        $user->id_produk = $user->refUserRole->refRole->refRolesProduk->id_produk ?? null;
        $user->nama_produk = $user->refUserRole->refRole->refRolesProduk->refProduk->nama ?? null;
        $user->menu = [];
        if ($user->refUserRole) {
            $user->menu = $user->refUserRole->refRole->manyRolesMenu->map(function ($item){
                return [
                    'nama_menu' => $item->refMenu->nama ?? null,
                    'kode_menu'=> $item->refMenu->kode ?? null,
                    'url'=> $item->refMenu->url ?? null,
                    'path'=> $item->refMenu->path ?? null,
                    'icon'=> $item->refMenu->icon ?? null,
                    'platfom'=> $item->refMenu->platfom ?? null,
                    'modul'=> $item->refMenu->modul ?? null,
                ];
            });
        }
        
        unset(
            $user->refUserRole,
            $user->manyUserRole,
        );
        $user->access_token = Helper::createJwt($user);
        $user->refresh_token = Helper::createJwt($user, TRUE);
        $user->expires_in = Helper::decodeJwt($user->access_token)->exp;

        return [
            'items' => $user,
            'attributes' => null
        ];
    }

    public static function getAllData($params)
    {
        $data = Model::where(function ($query) use ($params){
            if($params->search) $query->where('username','ilike',"%{$params->search}%")
            ->orWhere('email','ilike',"%{$params->search}%")
            ->orWhere('ip_whitelist','ilike',"%{$params->search}%");
        })->paginate($params->limit ?? null);
        return [
            'items' => $data->items(),
            'attributes' => [
                'total' => $data->total(),
                'current_page' => $data->currentPage(),
                'from' => $data->currentPage(),
                'per_page' => $data->perPage(),
           ]
        ];
    }

    public static function admin($id)
    {
        return [
            'items' => Model::where('group_id',Group::ADMIN)->find($id),
            'attributes' => null
        ];
    }

    public static function byId($id)
    {
        $user =  Model::with(['refRole', 'refCabang'])->find($id);
        $user->roles = $user->manyUserRole->map(function ($item){
            return [
                'id_role' => $item->id_role,
                'nama_role' => $item->refRole->nama ?? null,
            ];
        });
        $user->id_role = $user->refUserRole->id_role ?? null;
        $user->nama_role = $user->refUserRole->refRole->nama ?? null;
        $user->id_produk = $user->refUserRole->refRole->refRolesProduk->id_produk ?? null;
        $user->nama_role =  $user->refUserRole->refRole->nama ?? null;
        $user->nama_cabang = $user->refCabang->nama_cabang ?? null;
        $user->nama_produk = $user->refUserRole->refRole->refRolesProduk->refProdu->namak ?? null;

        unset(
            $user->refRole,
            $user->refCabang,
            // $user->refRoleProduk,
            $user->manyUserRole,
            $user->refUserRole
        );
        return [
            'items' => $user,
            'attributes' => null
        ];
    }

    public static function saveData($params)
    {
        DB::beginTransaction();
        try {

             // * validator ---- /
            Validator::extend('valid_username', function($attr, $value){
                return preg_match('/^\S*$/u', $value);
            });

            $validator = Validator::make($params->all(), [
            'username' => 'required|valid_username|min:4|unique:users,username'
            ],['valid_username' => 'please enter valid username.']);

            if (!$validator) throw new \Exception("Wrong Parameter.");

            // * end validator ----- /

            $keys = Model::where('email',$params->email)->first();
            if($keys) throw new \Exception("Email available.");
            $keys_username = Model::where('username',$params->username)->first();
            if($keys_username) throw new \Exception("Username available.");

            $insert = new Model;
            $insert->username = $params->username;
            $insert->email = $params->email;
            $insert->nama = $params->nama;
            $insert->kode_cabang = $params->kode_cabang;
            $insert->kode_role = $params->kode_role;
            $insert->description = $params->description;
            $insert->password = Hash::make($params->password);
            $insert->save();
            $insert->manyRoleProduk()->createMany(self::setParamRoleProduk($params,$insert->id));
            DB::commit();
            return [
                'items' => $insert,
                'attributes' => null
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function setParamRoleProduk($request,$id)
    {
        if($request->role_produk){
            $params = [];
            foreach ($request->role_produk as $key => $val) {
                $params[] = [
                    'id_user' => $id,
                    'id_produk' => $val['id_produk']
                ];
            }
            return $params;
        }
    }

    public static function updateData($params,$id)
    {
        DB::beginTransaction();
        try {
            $update = Model::find($id);
            if(!$update) throw new \Exception("id tidak ditemukan.");
            $update->username = $params->username;
            $update->app_name = $params->app_name;
            $update->email = $params->email;
            $update->description = $params->description;
            $update->save();
            DB::commit();
            return [
                'items' => $update,
                'attributes' => null
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public static function deleteData($id)
    {
        DB::beginTransaction();
        try {
            $delete = Model::destroy($id);
            DB::commit();
            return [
                'items' => $delete,
                'attributes' => null
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function byUsername($username)
    {
        return Model::where('username',$username)->first();
    }

    public static function getSuperadmin($username)
    {
        return Model::where('username',$username)->whereNull('nirk')->first();
    }

    public static function byEmail($email)
    {
        return Model::where('email',$email)->first();
    }

    public static function requestResetPassword($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->email) $require_fileds[] = 'email';
            if(count($require_fileds) > 0) throw new \Exception('Paramter ini harus diisi '.implode(',',$require_fileds),400);

            list($token, $expired_at) = Helper::createVerificationToken([
                "email" => $request->email,
                "action" => 'reset-password'
            ]);

            $user = self::byEmail($request->email);
            if(!$user) throw new \Exception("email belum terdaftar", 401);
            $store = $user;
            $store->remember_token = $token;
            $store->save();
            if($is_transaction) DB::commit();
            /**
             * Send verification mail
             */
            $mail_to = $request->email;
            $mail_data = [
                "reset_password_token" => $token,
                "username" => $request->username
            ];
            Mail::to($mail_to)->send(new ResetPasswordMail($mail_data));
            return ['token' => $token,'exp' => $expired_at];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function verifyResetPassword($request)
    {
        try {

            $require_fileds = [];
            if(!$request->reset_password_token) $require_fileds[] = 'reset_password_token';
            if(count($require_fileds) > 0) throw new \Exception('Paramter ini harus diisi '.implode(',',$require_fileds),400);
            $data = Helper::getJwtData($request->reset_password_token);
            return [
                'items' => [
                    'status' => $data ? true : false,
                    'token' => $request->reset_password_token
                ]
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function resetPassword($request)
    {
        try {

            $require_fileds = [];
            if(!$request->reset_password_token) $require_fileds[] = 'reset_password_token';
            if(!$request->new_password) $require_fileds[] = 'new_password';
            if(count($require_fileds) > 0) throw new \Exception('Paramter ini harus diisi '.implode(',',$require_fileds),400);
            $data = Helper::getJwtData($request->reset_password_token);
            $user = self::byEmail($data->email);
            $user->password = Hash::make($request->new_password);
            $user->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
