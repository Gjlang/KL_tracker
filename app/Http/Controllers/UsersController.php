<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use App\Models\ClientCompany;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
class UsersController extends Controller
{
    public $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // /** @var \Illuminate\Routing\Controller $this */
        // $this->middleware(function ($request, $next) {
        //     $this->user = Auth::guard('web')->user();
        //     return $next($request);
        // });
    }

    /**
     * Show the users page.
     */
    public function index()
    {
        $client_companies = ClientCompany::select('id', 'name')->get();

        return view('users.index');
    }

    /**
     * Show the users list.
     */
   public function list(Request $request)
{
    $orderColumnIndex = (int) $request->input('order.0.column', 1); // default ke kolom "name"
    $orderDir = $request->input('order.0.dir', 'asc');
    $orderColumnName = $request->input("columns.$orderColumnIndex.data", 'name');

    $allowed = ['name','username','role','email'];
    if (!in_array($orderColumnName, $allowed, true)) {
        $orderColumnName = 'name';
    }

    $baseQuery = User::leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->select('users.*', 'roles.name as role');

    $totalData = (clone $baseQuery)->count('users.id');

    $searchValue = trim(strtolower($request->input('search.value')));
    if ($searchValue !== '') {
        $baseQuery->where(function ($q) use ($searchValue) {
            $q->where('users.name', 'LIKE', "%{$searchValue}%")
              ->orWhere('users.username', 'LIKE', "%{$searchValue}%")
              ->orWhere('roles.name', 'LIKE', "%{$searchValue}%")
              ->orWhere('users.email', 'LIKE', "%{$searchValue}%");
        });
    }

    $totalFiltered = (clone $baseQuery)->count('users.id');

    $limit = (int) $request->input('length', 25);
    $start = (int) $request->input('start', 0);

    $dataRows = $baseQuery
        ->orderBy($orderColumnName, $orderDir)
        ->skip($start)
        ->take($limit)
        ->get();

    $data = [];
    foreach ($dataRows as $d) {
        $data[] = [
            'name'     => $d->name,
            'username' => $d->username,
            'role'     => $d->role ?? '',
            'email'    => $d->email,
            'id'       => $d->id,
        ];
    }

    return response()->json([
        "draw"            => (int) $request->input('draw'),
        "recordsTotal"    => (int) $totalData,
        "recordsFiltered" => (int) $totalFiltered,
        "data"            => $data,
    ]);
}


    /**
     * Create user.
     */
    public function create(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'name' => ['required','string','max:255'],
            'username' => [
                'nullable','string','max:255','regex:/^\S*$/'
            ],
            'role' => ['required','string','in:superadmin,admin,support,sales,services'],
            'password' => ['required','string','min:6','confirmed'],
            'email' => ['required','string','email','max:255','unique:users,email'],
        ],
        [
            'name.required' => 'The "Name" field is required.',
            'username.regex' => 'The "Username" must not contain any spaces.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]
    );

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->first()], 422);
    }

    $data = $validator->validated();

    $payload = [
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => $data['password'],
        'role'     => $data['role'],
    ];

    if (Schema::hasColumn('users', 'username')) {
        if (empty($data['username'])) {
            return response()->json(['error' => 'The "Username" field is required.'], 422);
        }

        $exists = User::where('username', $data['username'])->exists();
        if ($exists) {
            return response()->json(['error' => 'The "Username" is already been taken.'], 422);
        }

        $payload['username'] = $data['username'];
    }

    try {
        DB::beginTransaction();

        $user = User::create($payload);

        $roleModel = Role::findOrCreate($data['role'], 'web');
        $user->syncRoles([$roleModel->name]);

        DB::commit();

        return response()->json(['success' => 'success'], 200);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Create user failed', ['err' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 422);
    }
}

    public function edit(Request $request)
    {
        $name               = $request->name;
        $username           = $request->username;
        $original_username  = $request->original_username;
        $role               = $request->role;
        $email              = $request->email;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'username' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'original_username' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'role' => [
                    'required',
                    'string',
                    'in:superadmin,admin,support,sales,services',
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],
            ],
            [
                'name.required' => 'The "System Display Name" field is required.',
                'name.string' => 'The "System Display Name" must be a string.',
                'name.max' => 'The "System Display Name" must not be greater than :max characters.',

                'username.required' => 'The "System Login Username" field is required.',
                'username.string' => 'The "System Login Username" must be a string.',
                'username.max' => 'The "System Login Username" must not be greater than :max characters.',

                'role.required' => 'The "Role" field is required.',
                'role.string' => 'The "Role" must be a string.',
                'role.max' => 'The "Role" must not be greater than :max characters.',

                'email.required' => 'The "Email" field is required.',
                'email.string' => 'The "Email" field must be a string.',
                'email.email' => 'The "Email" field must be a valid email address.',
                'email.max' => 'The "Email" field must not be greater than :max characters.',
            ]
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            $user = User::where('username', $original_username)->first();
            $role_name = Role::where('name', $role)->first();

            // Handle the case where the user is not found
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            // Update user details
            User::where('username', $original_username)
                ->update([
                    'name'      => $name,
                    'username'  => $username,
                    'email'     => $email
                ]);

            // Update user role
            $user->syncRoles([$role_name]);

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
     * Delete user.
     */
    public function delete(Request $request)
    {
        $delete_user_id = $request->delete_user_id;

        // Validate fields
        $validator = Validator::make(
            $request->all(),
            [
                'delete_user_id' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
            ],
            [
                'delete_user_id.exists' => 'The employee cannot be found.',
            ]
        );

        // Handle failed validations
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            // Ensure all queries successfully executed
            DB::beginTransaction();

            // Update employee user_id to null as removing the association of the deleted user account
            User::where('id', $delete_user_id)
                ->update([
                    'status'   => 0
                ]);

            // Delete system user
            // User::find($delete_user_id)->delete();

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
