<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $visited = DB::select('select * from places where visited = ?', [1]); 
        $togo = DB::select('select * from places where visited = ?', [0]);

        return view('travel_list', ['visited' => $visited, 'togo' => $togo ] );
    }
}
