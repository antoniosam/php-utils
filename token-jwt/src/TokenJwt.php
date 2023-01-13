<?php

namespace Ast\TokenJwt;

use Firebase\JWT\JWT;

class TokenJwt
{
    private $key ;
    private $duration = 3600;
    private $offset = 0;

    private $datadecode;
    private $error;
    private $decodesuccess = false;

    function __construct($key, $duration=null, $timeoffset = null  )
    {
        $this->key = $key;
        if(!is_null($duration)){
            $this->duration = $duration;
        }
        if(!is_null($timeoffset)){
            $this->offset = $timeoffset;
        }
    }

    /**
     * @param array $data
     * @return string
     */
    public function generate($data){
        $token = [
            'iat' => time() + $this->offset,
            'exp' => time() + $this->duration,
            'data' => $data
        ];
        return JWT::encode($token, $this->key);
    }

    /**
     * @param $jwt
     * @return bool
     */
    public function validate($jwt){
        try{
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
            $this->datadecode = (array) $decode->data;
            $this->decodesuccess = true;
            return true;
        }catch (\Exception $e){
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * @return array|null
     */
    public function getDatadecode()
    {
        if($this->decodesuccess){
            return $this->datadecode;
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getErrorDecode()
    {
        return $this->error;
    }



    /**
     * @param $key
     * @param $data
     * @param null $duration
     * @return string
     */
    public static function generateToken($key,$data,$duration = null){
        $obj = new self($key,$duration);
        return $obj->generate($data);
    }

    /**
     * @param $key
     * @param $jwt
     * @return array|null
     */
    public static function validaToken($key,$jwt){
        $obj = new self($key);
        if($obj->validate($jwt)){
            return $obj->getDatadecode();
        }else{
            return null;
        }
    }

}