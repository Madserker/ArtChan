<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TeamController;
use JWTAuth;
use App\Chat;
use App\TeamChat;
use App\PrivateChat;
use App\PublicChat;
use DB;
use App\User;
use App\Message;

class ChatController extends Controller
{

    public static function createTeamChat($team,$description){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        $chat = new Chat([
            'name' => $team ,//mismo name que el teamchat para encontrarlo con INNER JOIN
            'description' => $description
        ]);

        $chat->save();

        $teamChat = new TeamChat([
            'team'=>$team
        ]);

        $teamChat->id = $chat->id;

        $teamChat->save();
        
        
        DB::table('chat_user')->insert([
            ['chat' => $chat->id, 'user' => $user->username],
        ]);


        $response = [
            'teamChat' => $teamChat
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);

    }



    public function getTeamChats($username){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

//get user teams
        $user = User::find($username);
        //$users = $team->users;
        $teams=
        DB::table('team_user')
        ->join('authors', 'authors.username', '=', 'team_user.team')
        ->where('team_user.user',$username)
        ->select('authors.username')
        ->get();

        $teamChats = [];

        for($i=0;$i<sizeof($teams);$i++){
            $temp = 
            DB::table('team_chats')
            ->join('chats', 'chats.id', '=', 'team_chats.id')
            ->where('team_chats.team', $teams[$i]->username)
            ->select('chats.*','team_chats.*')
            ->get()->first();
            array_push($teamChats,$temp);
        }

        $response = [
            'chats' => $teamChats
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);
    }

    public function getPrivateChats($username){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        //get user teams
                $user = User::find($username);
                //$users = $team->users;
                $chats=
                DB::table('chat_user')
                ->join('private_chats', 'private_chats.id', '=', 'chat_user.chat')
                ->join('chats', 'chats.id', '=', 'private_chats.id')
                ->where('chat_user.user',$username)
                ->get();
                
        
                $response = [
                    'chats' => $chats
                ];
                $headers = ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'];
        
                return response()->json($response, 200, $headers);
            }
            
    public function getPublicChats($username){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }
        //get user teams
                $user = User::find($username);
                //$users = $team->users;
                $chats=
                DB::table('chat_user')
                ->join('public_chats', 'public_chats.id', '=', 'chat_user.chat')
                ->join('chats', 'chats.id', '=', 'public_chats.id')
                ->where('chat_user.user',$username)
                ->get();
                
        
                $response = [
                    'chats' => $chats
                ];
                $headers = ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'];
        
                return response()->json($response, 200, $headers);
            }

    public function getChat($id){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }
        
        $chat = Chat::find($id);
        $response = [
            'chat' => $chat
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);

    }

    public function getMessages($id){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        $messages=
        DB::table('messages')
        ->where('messages.chat',$id)
        ->select('messages.*')
        ->get();

        $response = [
            'messages' => $messages
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);
    }

    public function postMessage($id,Request $request){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }
        $message = new Message([
            'chat' => $id,
            'user'=> $user->username,
            'text' => $request->input('text')
        ]);
        $message->save();

        $response = [
            'message' => $message
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);
    }


    public function getChatMembers($id){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        $users=
        DB::table('chat_user')
        ->where('chat_user.id',$id)
        ->select('chat_user.user')
        ->get();

        $response = [
            'users' => $users
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);
    }




    public static function createPrivateChat($name,$description){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        $chat = new Chat([
            'name' => $name ,//mismo name que el teamchat para encontrarlo con INNER JOIN
            'description' => $description
        ]);

        $chat->save();

        $privateChat = new PrivateChat([
            'user' => $user->username
        ]);

        $privateChat->id = $chat->id;

        $privateChat->save();
        
        
        DB::table('chat_user')->insert([
            ['chat' => $chat->id, 'user' => $user->username],
        ]);


        $response = [
            'privateChat' => $privateChat
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);
    }

    public static function createPublicChat($name,$description){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        $chat = new Chat([
            'name' => $name ,//mismo name que el teamchat para encontrarlo con INNER JOIN
            'description' => $description
        ]);

        $chat->save();

        $publicChat = new PublicChat([

        ]);

        $publicChat->id = $chat->id;

        $publicChat->save();
        
        
        DB::table('chat_user')->insert([
            ['chat' => $chat->id, 'user' => $user->username],
        ]);


        $response = [
            'publicChat' => $publicChat
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];

        return response()->json($response, 200, $headers);
    }

    public function addMember($chat_id,$username){
        if(! $user = JWTAuth::parseToken()->authenticate()){//authenticate() confirms that the token is valid 
            return response()->json(['message' => 'User not found'],404); //si no hay token o no es correcto lanza un error
        }

        DB::table('chat_user')->insert([
            ['chat' => $chat_id, 'user' => $username],
        ]);
        $response = [
            'message' => 'member added'
        ];
        $headers = ['Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'];
        return response()->json($response, 200, $headers);

    }
    
    public function deleteChat($id){

    }

    //post public chat
    //add user to chat
    //remove user from chat
}
