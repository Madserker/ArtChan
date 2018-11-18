<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Draw;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserController extends Controller
{


//==============================================================LOGIN/REGISTER METHODS
    public function signup(Request $request){

        $randomPic = ['src/assets/storage/profile1.jpg','src/assets/storage/profile2.png',
        'src/assets/storage/profile3.png','src/assets/storage/profile4.jpg',
        'src/assets/storage/profile5.jpg','src/assets/storage/profile6.jpg'];

        $this->validate($request,[//validamos el registro
        'username' => 'required|unique:users', //el nombre de usuario es obligatorio y unico en la tabla de usuarios
        'email' => 'required|email|unique:users', //el email tiene que ser obligatorio, formato email y unico en la tabla de usuarios
        'password' => 'required', //contraseña obligatoria
            ]);
        $user = new User([//creamos el usuario con los parametros del request
            'profilePic' => array_random($randomPic),//default image
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'birthday' => $request->input('birthday'),
            'password' => bcrypt($request->input('password')), //bcrypt encripta la contraseña del usuario
            'description' => "",
        ]);
        $user->save();//guardamos el usuario en la DB
        return response()->json([
            'message' => 'User succesfully created'
        ],201);
    }

    public function signin(Request $request){
        $this->validate($request,[//validamos el registro
            'username' => 'required', //el nombre es obligatorio
            'password' => 'required', //contraseña obligatoria
                ]);
            $credentials = $request->only('username','password');
            $user = User::where('username', $request->input('username'))->get();
            try {
                if(!$token = JWTAuth::attempt($credentials)){//intenta crear token
                //si if falla, las credenciales no son validas
                    return response()->json([//return error
                        'error' => 'Invalid Credentials'
                    ], 401);
                }
            }catch(JWTException $e){//si no ha podido crear el token
                return response()->json([
                    'error' => 'Could not create token'
                ], 500);
            }
            return response()->json([//si las credenciales son validas y no ha habido error al crear token, retornamos token y usuario
                'token' => $token,
                'user' => $user
            ],200);
    }  
    
    

    //===============================================USERS METHODS
    public function getUsers(){
        $users = User::all();
        $response = [
            'users' => $users
        ];

        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);
    }

    public function getUserByUsername(String $username){
        $user = User::where('username',$username)->get();
        if(!$user){//si no ha encontrado el user con ese id
            return response()->json(['message' => 'User not found'],404);//json con mensaje de error 404 not found
        }
        return response()->json(['user' => $user],200);
    }

    public function getFollowers(String $username){
        $user = User::where('username',$username)->get();
        if(!$user){//si no ha encontrado el user con ese id
            return response()->json(['message' => 'User not found'],404);//json con mensaje de error 404 not found
        }
        return response()->json(['followers' => $user[0]->followers],200);
    }

    public function getFollowing(String $username){
        $user = User::where('username',$username)->get();
        if(!$user){//si no ha encontrado el user con ese id
            return response()->json(['message' => 'User not found'],404);//json con mensaje de error 404 not found
        }
        return response()->json(['followers' => $user[0]->following],200);
    }

    public function follow(Request $request){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }
        
        $user = User::find($request->input('username'));

        if (!$user->followers->contains($request->input('follower'))) {//comprobamos que no este esta relacion ya en la tabla
            $user->followers()->attach($request->input('follower'));
            return response()->json(['user' => $user], 201);//retornamos 201
        }
        return response()->json(['message' => 'Already following that user'],404); //si ya seguimos al usuario, lanzamos error
    }

    public function unfollow($following,$username){

        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        $user = User::find($username);
        if ($user->following->contains($following)) {//comprobamos que este esta relacion ya en la tabla
            $user->following()->detach($following);
            return response()->json(['user' => $user], 201);//retornamos 201
        }
        return response()->json(['message' => 'You do not follow that user'],404); //si ya seguimos al usuario, lanzamos error
    }

    public function getFollowingUsersDraws($username){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }
        $user = User::where('username',$username)->get();
        if(!$user){//si no ha encontrado el user con ese id
            return response()->json(['message' => 'User not found'],404);//json con mensaje de error 404 not found
        }
        $following = $user[0]->following;
        $draws = [];
        for($i=0; $i<sizeof($following);$i++){
            $authorDraws = Draw::where('author',$following[$i])->get();
            array_push($draws,$authorDraws);
        }

        return response()->json(['draws' => $following],200); 



    }
}