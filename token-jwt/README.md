TokenJwt  
===============

Libreria para generar tokens con JWT (Firebase)

## Instalar
```
composer require antoniosam/tokenjwt
```

## Uso

**$data Debe ser arreglo** 

$data = [ 'id' => 1 ];
```
 $data = ['id'=>1];
 $tmp = new TokenJwt($key,$duration,$timeoffset);
 $token = $tmp->generate($data);
 if($obj->validate($token)){
    $info_decode  = $obj->getDatadecode();
 }else{
    echo $obj->getErrorDecode();
 }

```
## Metodos staticos

```
TokenJwt::generateToken($secret_key,$data);
$info_decode = TokenJwt::validaToken($secret_key,$jwt);

if(!is_null($info_decode)){
//success decode
}else{
//error token
}
```
