<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DrawController extends Controller
{
    public function postDraw(Request $request){
        $draw = new Draw();
        $draw->name = $request->input('name');//cogemos el name del draw desde la request del frontend
        $draw->save();//guardamos el draw
        return response()->json(['draw' => $draw],201);//retornamos 201 y el dibujo
    }

    public function getDraws(){
        $draws = Draw::all();
        $response = [
            'draws' => $draws
        ];
        return response()->json($response,200);
    }

    public function putDraw(Request $request, $id){//actualizar draw atributes
        $draw = Draw::find($id);
        if(!$draw){//si no ha encontrado el draw con ese id
            return response()->json(['message' => 'Draw not found'],404);//json con mensaje de error 404 not found
        }
        $draw->name = $request->input('name');
        $draw->save();
        return response()->json(['draw' => $draw],200);
    }

    public function deleteDraw($id){
        $draw = Draw::find($id);
        $draw->delete();
        return response()->json(['message' => 'Draw deleted'],200);
    }
}