<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientCompany;
use App\Models\Contractor;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class ContractorController extends Controller
{
    public $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('web')->user();
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (is_null($this->user) || !$this->user->can('client.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any client. Contact system admin for access !');
        }

        // Get clients data
        $clients = Client::leftJoin('client_companies', 'client_companies.id', '=', 'clients.company_id')
        ->select('clients.*', 'client_companies.name as company_name')
        ->where('clients.status', '=', '1')
        ->get();
        
        // Get user data
        $users = User::where('id', '!=', auth()->id())->get();
        
        // Get client company data
        $clientcompany = ClientCompany::all();

        return view('contractors.index', compact('clients', 'users', 'clientcompany'));
    }

    /**
     * Show the contractor users list.
     */
    public function list(Request $request)
    {

        $columns = array(
            0 => 'company_name',
            1 => 'name',
            2 => 'phone',
            3 => 'id',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumnName = $columns[$orderColumnIndex];
        $orderDirection = $request->input('order.0.dir');
        
        // SQL query
        $query = Contractor::select('contractors.*')
        ->orderBy($orderColumnName, $orderDirection);

        // Get total records count
        $totalData = $query->count();

        // Check if search value is set
        $searchValue = trim(strtolower($request->input('search.value')));

        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('contractors.company_name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('contractors.name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('contractors.phone', 'LIKE', "%{$searchValue}%");
            });
        }

        // Get total filtered records count
        $totalFiltered = $query->count();
        
        // Apply pagination
        $filteredData = $query->skip($start)->take($limit)->get();

        $data = array();

        foreach ($filteredData as $d) {
            $nestedData = array(
                'company_name' => $d->company_name,
                'name' => $d->name,
                'phone' => $d->phone,
                'id' => $d->id,
            );
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
        
    }

    /**
     * Create client.
     */
    public function create(Request $request)
    {
        $company     = $request->company;
        $name           = $request->name;
        $phone        = $request->phone;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'company' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'phone' => [
                    'required',
                    'regex:/^\+?[0-9]+$/',
                    'max:255',
                ],
            ],
            [
                'company.required' => 'The "Contractor Company Name" field is required.',
                'company.string' => 'The "Contractor Company Name" must be a string.',
                'company.max' => 'The "Contractor Company Name" must not be greater than :max characters.',

                'name.required' => 'The "Contractor Name" field is required.',
                'name.string' => 'The "Contractor Name" must be a string.',
                'name.max' => 'The "Contractor Name" must not be greater than :max characters.',

                'phone.required' => 'The "Phone No." field is required.',
                'phone.regex' => 'The "Phone No." field must only contain "+" symbol and numbers.',
                'phone.max' => 'The "Phone No." field must not be greater than :max characters.',
            ]
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            // Insert new client company
            $client = Contractor::create([
                'company_name'          => $company,
                'name'          => $name,
                'phone'       => $phone,
            ]);

            // Ensure all queries successfully executed, commit the db changes
            DB::commit();

            return response()->json([
                "success"   => "success",
            ], 200);
        } catch (\Exception $e) {
            // If any queries fail, undo all changes
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $id                     = $request->id;
        $company                = $request->company;
        $name                   = $request->name;
        $phone                  = $request->phone;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'company' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'phone' => [
                    'required',
                    'regex:/^\+?[0-9]+$/',
                    'max:255',
                ],
            ],
            [
                'company.required' => 'The "Company Name" field is required.',
                'company.string' => 'The "Company Name" must be a string.',
                'company.max' => 'The "Company Name" must not be greater than :max characters.',

                'name.required' => 'The "Contractor PIC Name" field is required.',
                'name.string' => 'The "Contractor PIC Name" must be a string.',
                'name.max' => 'The "Contractor PIC Name" must not be greater than :max characters.',

                'phone.required' => 'The "Phone No." field is required.',
                'phone.regex' => 'The "Phone No." field must only contain "+" symbol and numbers.',
                'phone.max' => 'The "Phone No." field must not be greater than :max characters.',
            ]
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            // Update client company
            Contractor::where('id', $id)
                ->update([
                    'company_name'          => $company,
                    'name'                  => $name,
                    'phone'               => $phone,
                ]);

            // Ensure all queries successfully executed, commit the db changes
            DB::commit();

            return response()->json([
                "success"   => "success",
            ], 200);
        } catch (\Exception $e) {
            // If any queries fail, undo all changes
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Delete client.
     */
    public function delete(Request $request)
    {

        $id = $request->id;

        logger('delete: ' . $id);

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'id' => [
                    'required',
                    'integer',
                    'exists:contractors,id',
                ],
            ],
            [
                'id.exists' => 'The contractor cannot be found.',
            ] 
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            // Update stus to 0 as deleted (soft delete)
            Contractor::where('id', $id)->delete();

            // Ensure all queries successfully executed, commit the db changes
            DB::commit();

            return response()->json([
                "success"   => "success",
            ], 200);
        } catch (\Exception $e) {
            // If any queries fail, undo all changes
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
