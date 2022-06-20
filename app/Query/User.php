<?php

namespace App\Query;
use App\Models\User as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;

class User {

    public static function authenticateuser($params)
    {
        $required_params = [];
        if (!$params->nirk) $required_params[] = 'nirk';
        if (!$params->password) $required_params[] = 'password';
        if (count($required_params)) throw new \Exception("Parameter berikut harus diisi: " . implode(", ", $required_params));

        $user = Model::where(function ($query) use ($params){
            $query->where('nirk',$params->nirk);
        })->first();
        if(!$user) throw new \Exception("Pengguna belum terdaftar.");
        // if (!Hash::check($params->password, $user->password)) throw new \Exception("Email atau password salah.",400);
        $user->id_jenis_produk = $user->refRoleProduk->id_jenis_produk ?? null;
        $user->role_produk = $user->manyRoleProduk->map(function ($item){
            return [
                'id_jenis_produk' => $item->id_jenis_produk,
                'nama_jenis_produk' => $item->refJenisProduk->nama_jenis_produk ?? null,
            ];
        });
        $user->nama_role = $user->refRole->nama_role ?? null;
        unset(
            $user->refRole,
            $user->manyRoleProduk,
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
        $data =  Model::with(['refRole', 'refCabang'])->find($id);
        $data->id_jenis_produk = $data->refRoleProduk->id_jenis_produk ?? null;
        $data->role_produk = $data->manyRoleProduk->map(function ($item){
            return [
                'id_jenis_produk' => $item->id_jenis_produk,
                'nama_jenis_produk' => $item->refJenisProduk->nama_jenis_produk ?? null,
            ];
        });
        $data->nama_role = $data->refRole->nama_role ?? null;
        $data->nama_cabang = $data->refCabang->nama_cabang ?? null;
        unset(
            $data->refRole,
            $data->refCabang,
            $data->refRoleProduk,
            $data->manyRoleProduk
        );
        return [
            'items' => $data,
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
}
