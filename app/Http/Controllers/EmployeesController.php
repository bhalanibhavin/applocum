<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Validator;
use App\Employees;
use App\Companies;
use Auth;
//use guard;

class EmployeesController extends Controller
{

    public function __construct()
    {
        /*$this->middleware('auth');
        if(Auth::check()) {
            return redirect('login');
        }*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = Companies::pluck('name', 'id');
        if (Auth::guard('company')->check() || Auth::user()){            
            if ($request->ajax()) {
                if (Auth::guard('company')->check()){
                    $company = Auth::guard('company')->user();
                    $data = Employees::where('company_id', $company->id)->get();
                }else{
                    $data = Employees::get();
                }
                return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row){
       
                               $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editEmployees">Edit</a>';
       
                               $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteEmployees">Delete</a>';
        
                                return $btn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
            }
          
            return view('employees.index',compact(['employees', 'companies']));
        }else{
            return redirect('/login');
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

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        //  if ($validator->fails())
        // {
        //     return response()->json(['errors'=>$validator->errors()->all()]);
        // }

        if ($validator->passes()) {
            Employees::updateOrCreate(['id' => $request->employees_id],
                    ['full_name' => $request->full_name, 'company_id' => $request->company_id, 'email' => $request->email, 'phone' => $request->phone]);        
       
            return response()->json(['success'=>'Employees saved successfully.', 'status' => '200']);
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
        $companies = Companies::pluck('name', 'id');
        $employees = Employees::find($id);
        return response()->json($employees);
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
        Employees::find($id)->delete();
     
        return response()->json(['success'=>'Employees deleted successfully.']);
    }
}
