<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Good;
use App\Order;
use App\Setings;
use App\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function host_index()
    {
        $good = Good::Paginate(10);
        return view("admin.host_index",compact('good'));
    }

    public function users_index()
    {
        $user = User::Paginate(10);
        return view("admin.users_index",compact('user'));
    }

    public function orders_index()
    {
        $order = Order::latest()->Paginate(10);
        return view("admin.orders_index",compact('order'));
    }

    protected function user_home()
    {
        $user = Auth::user();
        return view("user_index",compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function user_edit($id)
    {
        $user = User::findOrFail($id);
        return view("admin.user_edit",compact('user'));
    }

    public function user_front_edit()
    {
        $user = Auth::user();
        return view("user_edit",compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_update(Request $request, $id)
    {
        if(Auth::user()->level==0):
        $user = User::findOrFail($id);
        $input = $request->all();
        if(!empty($request->input('the_password'))) {
            $new_password = bcrypt($request->input('the_password'));
            $input = array_merge(['password'=>$new_password],$input);
        }
        $user->update($input);
        endif;
        return redirect("/admin/users/");
    }

    public function user_front_update(Request $request)
    {
        $user = Auth::user();
        $input = $request->all();
        $input['level']=Auth::user()->level;
        if(!empty($request->input('the_password'))) {
            $new_password = bcrypt($request->input('the_password'));
            $input = array_merge(['password'=>$new_password],$input);
        }
        $user->update($input);
        return redirect("/home");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('/admin/users');
    }

    public function install()
    {
        if(Setings::all()->count()==0)
        {
            return view('admin.install');
        }
        return redirect('/');
    }

    public function initial(Request $request)
    {
        if(Setings::all()->count()==0)
        {
            $user = $request->all();
            $user["password"] = bcrypt($user["password"]);
            $user["level"] = 0;
            //dd($user);
            User::create($user);
            Setings::install($request->input('site'),$request->input('siteUrl'));
            return redirect('/auth/login');
        }
        return redirect('/');
    }

    public function setings_general()
    {
        $setings = Setings::all();
        return view('admin.setings_general',compact('setings'));
    }

    public function setings_theme()
    {
        $setings = Setings::all();
        return view('admin.setings_theme',compact('setings'));
    }

    public function setings_update(Request $request,$id)
    {
        if(Auth::user()->level==0):
        $setings = Setings::find($id);
        $input = $request->all();
        //dd($input);
        $setings->update($input);
        endif;
        return redirect()->back();
    }

    public function setings_server()
    {
        //Mail::mail(User::all()->last()->id,"order","3838438");
        $server = Setings::server();
        $setings = Setings::all();
        return view('admin.setings_server',compact('server'))->with('setings',$setings);
    }
}
