<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoginResource;
use App\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return LoginResource
     */
    public function store(Request $request)
    {
        $obj = Login::updateOrCreate([
            'id' => isset($request->id) ? $request->id != '' ? $request->id : 0 : 0
        ], $request->except(['id']));

        return new LoginResource($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = Login::findorFail($id);
        $obj->delete();

        return response()->json(null, 204);
    }

    public function indexFS(Request $request)
    {
        $queryParent = $this->queryStructure();

        $model = new Login();

        $relations = $model->relationships;

        $queryParent = $this->makeFilterAndSortingQuery($request, $queryParent, $relations);

        $pageSize = isset($request['page_size']) ? $request['page_size'] != '' ? $request['page_size'] : 0 : 0;
        return LoginResource::collection($queryParent->paginate($pageSize));
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['username', 'password']);

        if (!isset($request->username) || $request->username == "") {
            return response()->json(['error' => 'Username is required!'], 402);
        }
        if (!isset($request->password) || $request->password == "") {
            return response()->json(['error' => 'Password is required!'], 402);
        }
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json("token invalidated.", 200);
    }

    public function refreshToken()
    {
        $token = JWTAuth::getToken();
        $new_token = JWTAuth::refresh($token);

        return $this->respondWithToken($new_token);
    }

    protected function respondWithToken($token)
    {
        $login = Auth::guard('api')->user();
        $user = [];
        if ($login->id) {
            $user = $this->queryStructure()->where('id', $login->id)->first();
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(),
            'user' => $user
        ]);
    }

    public function bulkRegister(Request $request)
    {
        $objects = $request['data'];
        $data = [];
        foreach ($objects as $obj) {
            $data[] = $this->store(new Request($obj));
        }
        return ["data" => $data];
    }

    private function queryStructure()
    {
        return Login::with(['userId', 'role_id']);
    }
}
