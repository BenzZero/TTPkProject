<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Auth;
use Input as Input;
use App\Models\Image;
use App\Models\Review;
use App\Models\Map;


class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index','show','searchform');    
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagenum = 8;
        $activities = Activity::orderBy('id','desc')->where('destroy','like',1)->paginate($pagenum);
        $page = $request->input('page');
        $page = ($page != null)?$page:1;

        // $activities = Activity::all();
        // $activities = $activities->shuffle();
        
        $user = Auth::user();
        return view('activity.index')   ->with('activities',$activities)
                                        ->with('user',$user);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->roles->role_name == "admin"){
            return view('activity.create');
        }
        else {
            $message = array('head' => 'You do not have role','detail' => 'You do not have role in create activity');
            return view('error.dohaverole')->with('message', $message);
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
        Activity::create( $request->all() );
        $id = Activity::all()->last();
        Map::create( $request->all() );
        $map = Map::all()->last();
        $map->activities_id = $id->id;
        $map->save();

        if(Input::hasFile('files')){
            foreach(Input::file('files') as $file){
                $imageName = rand();
                $imageSur = $file->getClientOriginalExtension();
                $nameimg = $imageName.'.'.$imageSur;

                // $cur_dir = explode('\\', getcwd());
                // $file->move($ddd, $nameimg);
                
                // $ddd =  $cur_dir[count($cur_dir)-1];
                // $ddd = $ddd.'/img/';
                // echo $ddd.$nameimg;
                   $file->move(base_path() . '/public/img/', $nameimg);
                $img = new Image;
                $img->image_name = $nameimg;
                $img->activities_id = $id->id;
                $img->save();
                // echo base_path();
            }
        }

        return redirect('/activity');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activities = Activity::findOrFail($id);
        $Excellent = $activities->reviews->where('score_review', 'Excellent')->count();
        $Verygood = $activities->reviews->where('score_review', 'Very good')->count();
        $Average = $activities->reviews->where('score_review', 'Average')->count();
        $Poor = $activities->reviews->where('score_review', 'Poor')->count();
        $Terrible = $activities->reviews->where('score_review', 'Terrible')->count();
        $sum = $activities->reviews->count();
        if($sum == 0)
            $sum = 1;
        
        return view('activity.show')    ->with('activities',$activities)
                                        ->with('id',$id)
                                        ->with('Excellent',$Excellent)
                                        ->with('Verygood',$Verygood)
                                        ->with('Average',$Average)
                                        ->with('Poor',$Poor)
                                        ->with('Terrible',$Terrible)
                                        ->with('sum',$sum);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $activities = Activity::findOrFail($id);
        return view('activity.edit')->with('activities',$activities);
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
        $activities = Activity::findOrFail($id);      
        
        if($activities->map_location){
            $activities->map_location->update($request->all());
        }
        else{
            Map::create( $request->all() );
            $map = Map::all()->last();
            $map->activities_id = $id;
            $map->save();
        }

        if(Input::hasFile('files')){
            foreach(Input::file('files') as $file){
                $imageName = rand();
                $imageSur = $file->getClientOriginalExtension();
                $nameimg = $imageName.'.'.$imageSur;

                // $cur_dir = explode('\\', getcwd());
                // $file->move($ddd, $nameimg);
                
                // $ddd =  $cur_dir[count($cur_dir)-1];
                // $ddd = $ddd.'/img/';
                // echo $ddd.$nameimg;
                   $file->move(base_path() . '/public/img/', $nameimg);
                $img = new Image;
                $img->image_name = $nameimg;
                $img->activities_id = $id->id;
                $img->save();
                // echo base_path();
            }
        }

        $activities->update($request->all());
        return redirect('/activity');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Activitydes = Activity::findOrFail($id);
        $Activitydes->destroy = 0;
        $Activitydes->save();
        return redirect('/activity');
    }

    public function postreview(Request $request, $id_activity)
    {
        $request['users_id'] = Auth::user()->id;
        $request['activities_id'] = $id_activity;
        Review::create( $request->all() );
        return redirect()->action('ActivityController@show', ['id' => $id_activity]);
    }

    public function searchform(Request $request)
    {
        $pagenum = 8;
        $search = $request->get('site_search');
        $activities = Activity::where('activity_name','like','%'.$search.'%')->paginate($pagenum);
        $page = $request->input('page');
        $page = ($page != null)?$page:1;

        $user = Auth::user();
        return view('activity.index')   ->with('activities',$activities)
                                        ->with('user',$user);
    }
}
