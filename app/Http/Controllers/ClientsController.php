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
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class ClientsController extends Controller
{
    public $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         $this->user = Auth::guard('web')->user();
    //         return $next($request);
    //     });
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Get clients data
        $clients = Client::leftJoin('client_companies', 'client_companies.id', '=', 'clients.company_id')
        ->select('clients.*', 'client_companies.name as company_name')
        ->where('clients.status', '=', '1')
        ->get();
        
        // Get user data
        $userId = Auth::id(); 
        
        // Get client company data
        $clientcompany = ClientCompany::all();

        return view('clients.index', compact('clients', 'users', 'clientcompany'));
    }

    /**
     * Show the client users list.
     */
    public function list(Request $request)
    {
        $company = $request->input('company');

        $columns = array(
            0 => 'name',
            1 => 'email',
            2 => 'phone',
            3 => 'company_name',
            4 => 'id',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumnName = $columns[$orderColumnIndex];
        $orderDirection = $request->input('order.0.dir');
        
        // Get users data
        $userId = Auth::id(); 
        
        // Get client company data
        $clientcompany = ClientCompany::all();
        
        // SQL query
        $query = Client::leftJoin('client_companies', 'client_companies.id', '=', 'clients.company_id')
        ->select('clients.id', 'clients.name', 'clients.email', 'clients.phone', 'clients.designation', 'client_companies.name as company_name', 'clients.status')
        ->where('client_companies.status', '=', '1')
        ->where('clients.status', '=', '1')
        ->orderBy($orderColumnName, $orderDirection);
        
        // Check if filter company name is set
        if ($company != "") {
            $query->where('clients.company_id', $company);
        }

        // Get total records count
        $totalData = $query->count();

        // Check if search value is set
        $searchValue = trim(strtolower($request->input('search.value')));

        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('clients.name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('clients.email', 'LIKE', "%{$searchValue}%")
                    ->orWhere('clients.phone', 'LIKE', "%{$searchValue}%")
                    ->orWhere('client_companies.name', 'LIKE', "%{$searchValue}%");
            });
        }

        // Get total filtered records count
        $totalFiltered = $query->count();
        
        // Apply pagination
        $filteredData = $query->skip($start)->take($limit)->get();

        $data = array();

        foreach ($filteredData as $d) {
            $nestedData = array(
                'name' => $d->name,
                'email' => $d->email,
                'phone' => $d->phone,
                'company_name' => $d->company_name,
                'designation' => $d->designation,
                'status' => $d->status,
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
        $name           = $request->name;
        $contact        = $request->contact;
        $company_id     = $request->company_id;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'contact' => [
                    'required',
                    'regex:/^\+?[0-9]+$/',
                    'max:255',
                ],
                'company_id' => [
                    'required',
                ],
            ],
            [
                'name.required' => 'The "Client Name" field is required.',
                'name.string' => 'The "Client Name" must be a string.',
                'name.max' => 'The "Client Name" must not be greater than :max characters.',

                'contact.required' => 'The "Contact No." field is required.',
                'contact.regex' => 'The "Contact No." field must only contain "+" symbol and numbers.',
                'contact.max' => 'The "Contact No." field must not be greater than :max characters.',

                'company_id.required' => 'The "Company" field is required.',
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
            $client = Client::create([
                'name'          => $name,
                'contact'       => $contact,
                'company_id'    => $company_id,
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
        $id                     = $request->original_client_id;
        $name                   = $request->name;
        $contact                = $request->contact;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'contact' => [
                    'required',
                    'regex:/^\+?[0-9]+$/',
                    'max:255',
                ],
            ],
            [
                'name.required' => 'The "Client Name" field is required.',
                'name.string' => 'The "Client Name" must be a string.',
                'name.max' => 'The "Client Name" must not be greater than :max characters.',

                'contact.required' => 'The "Contact No." field is required.',
                'contact.regex' => 'The "Contact No." field must only contain "+" symbol and numbers.',
                'contact.max' => 'The "Contact No." field must not be greater than :max characters.',
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
            Client::where('id', $id)
                ->update([
                    'name'          => $name,
                    'contact'       => $contact,
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
        // Get the current UTC time
        $current_UTC = Carbon::now('UTC');

        $delete_client_id = $request->delete_client_id;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'delete_client_id' => [
                    'required',
                    'integer',
                    'exists:clients,id',
                ],
            ],
            [
                'delete_client_id.exists' => 'The client cannot be found.',
            ] 
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();
            
            // Select user id from client
            $client = Client::select('user_id')->where('id', $delete_client_id)->first();

            // Update stus to 0 as deleted (soft delete)
            Client::where('id', $delete_client_id)
                ->update([
                    'user_id'        => NULL,
                    'status'        => '0',
                    'deleted_at'    => $current_UTC,
                ]);
            
            // Delete user related to clients
            if ($client->user_id != ""){
                User::find($client->user_id)->delete();
            }

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
