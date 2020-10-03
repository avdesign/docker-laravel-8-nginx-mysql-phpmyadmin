<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    private $model;


    public function __construct(Place $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $visited = $this->model->whereVisited(1)->get();
        $togo = $this->model->whereVisited(0)->get();

        /*
        $visited = DB::select('select * from places where visited = ?', [1]); 
        $togo = DB::select('select * from places where visited = ?', [0]);
        */

        return view('travel_list', ['visited' => $visited, 'togo' => $togo ] );
    }
}
