/*

import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { HttpClient } from '@angular/common/http';
import { Draw } from "./_models/Draw.interface";
import { map} from 'rxjs/operators';


@Injectable()
export class DrawServiceService {
  constructor(private http: HttpClient) {

  }

  getDraws(): Observable<Draw[]>{
  //get: returns observable object
  //need to cast observable to interface Draw -> get<Draw[]>
    return this.http.get<Draw[]>('http://localhost:8000/api/draws').pipe(map(response=>response));


  }
}
*/
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from "rxjs";
import { map} from 'rxjs/operators';

import { Injectable } from '@angular/core';
import { Draw } from "./_models/Draw.interface";

//definimos interface para mapear la lista de dibujos
interface getjson{
  draws: Draw[]
}

@Injectable()
export class DrawServiceService {
 constructor(private http: HttpClient){}

 getDraws(): Observable<Draw[]> {

    return this.http.get<getjson>('http://localhost:8000/api/draws')
    .pipe(
      map(res => res.draws as Draw[] || [])); 
  }
} 