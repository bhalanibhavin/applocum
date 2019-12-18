<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Validator;
use App\Companies;
use Auth;
use Mail;
use Hash;

class CompaniesController extends Controller
{
    
    public function __construct()
    {
        //$this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()){
            if ($request->ajax()) {
                $data = Companies::get();
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
       
                               $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editCompanies">Edit</a>';
       
                               $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteCompanies">Delete</a>';
        
                                return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
            }
      
            return view('companies.index',compact('companies'));        
        }else{
            if(Auth::guard('company')->check()){
                return redirect('/employees');
            }else{
                return redirect('/login');
            }
        }
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
        //echo "<pre>"; print_r($request->all()); exit;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:companies',
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->passes()) {
            $files = $request->file('logo');
            if(!empty($files)){
                /*$image_path = public_path('\images\\').$employe->logo;
                if(file_exists($image_path)) {
                    unlink($image_path);
                }*/
                $image_name = rand(11111, 99999) . '.' . $files->getClientOriginalExtension();
                $request->file('logo')->move(storage_path('/app/images/'), $image_name);
            }
            if(!empty($files)){
                Companies::updateOrCreate(['id' => $request->companies_id],
                    ['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'website' => $request->website, 'logo' => $image_name]);
            }else{
                Companies::updateOrCreate(['id' => $request->companies_id],
                    ['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'website' => $request->website]);
            }        


            // if($request->companies_id == ''){
                $toMail = $request->email;
                $toName = env('APP_NAME');
                $subject = 'New Added Companies!';
                // $data['user'] = $user;
                $data['user'] = 'user lists';
                $fromMail = env('MAIL_FROM_ADDRESS');
                //echo "====>".$fromMail; exit;
                Mail::send('mail.new_companies', $data, function ($m) use ($toMail,$toName, $subject, $fromMail) {
                    $m->from($fromMail, $toName);
                    $m->to($toMail, $toName)->subject($subject);
                });
            // }


            return response()->json(['success'=>'Companies saved successfully.', 'status' => '200']);
        }

        return response()->json(['errors'=>$validator->errors()->all(), 'status' => '100']);
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
        $companies = Companies::find($id);
        return response()->json($companies);        
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
        Companies::find($id)->delete();
     
        return response()->json(['success'=>'Companies deleted successfully.']);        
    }
}
