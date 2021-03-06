<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shuttle;
use Illuminate\Http\Request;
use Auth;
use App\Models\Booking;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth')->only('create');
    }

    public function index()
    {
        return view('shuttle.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id_book,Request $request)
    {
        $book = $request->all();
        $user = Auth::user();

        if($id_book == 2){
            $rules = [
                // "radio" => "required|max:20|min:3",
                // "pick_up" => "required|max:20|min:3",
                // "drop_off" => "required|max:20|min:3",
                // "depart_date" => "required|max:20|min:3",
                // "depart_time" => "required|max:20|min:3",
                // "return_date" => "required|max:20|min:3",
                // "return_time" => "required|max:20|min:3",
                // "vehicle_type" => "required|max:20|min:3",
                // "round" => "required|max:20|min:3",
                "first_name" => "required|max:20|min:3",
                "last_name" => "required|max:20|min:3",
                "email" => "required|max:20|min:5|email",
                "telephone" => "required|digits:10",
                "town_city" => "required|max:20|min:5",
                "country" => "required|max:20|min:5",
            ];
            $this->validate($request, $rules);
        }

        if($id_book == 1)
            return view('reservation.create1')  ->with('book', $book)
                                                ->with('user',$user);
        else if($id_book == 2){
            $book = $request->all();
            return view('reservation.create2')  ->with('book', $book)
                                                ->with('user',$user);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->radio === 'form1'){
            Shuttle::create( $request->all() );
            $shuttleid = Shuttle::all()->last();
        }
        $user = Auth::user();
        Booking::create( $request->all() );
        $bookid = Booking::all()->last();
        $bookid->shuttles_id = $shuttleid->id;
        $bookid->save();
        $bookid->users()->sync(Auth()->user()->id);
        return redirect('/booking');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
