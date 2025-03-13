<?php
namespace App\Http\Controllers;

use App\Http\Requests\AuthenticationRequest\LoginRequest;
use App\Http\Requests\UserRequest\SendTokenAppRequest;
use App\Http\Requests\UserRequest\StoreUserAppRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthenticationController extends Controller
{

    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     description="Log out user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="An error occurred while trying to log out. Please try again later.")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }
    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     description="Authenticate user and generate access token",
     * security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"username", "password", "branchOffice_id"},
     *             @OA\Property(property="username", type="string", example="admin"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),

     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *              @OA\Property(property="token", type="string", description="token del usuario"),
     *             @OA\Property(
     *             property="user",
     *             type="object",
     *             description="User",
     *             ref="#/components/schemas/User"
     *          ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Message Response"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="User not found or password incorrect",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Error message")
     *         )
     *     ),
     *       @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */

    public function login(LoginRequest $request): JsonResponse
    {

        try {

            $data = $request->only(['username', 'password']);
            // Llama al servicio de autenticación
            $authData = $this->authService->login($request->username, $request->password);

            // Verifica si el usuario es null
            if (! $authData['user']) {
                return response()->json([
                    'error' => $authData['message'],
                ], 422);
            }

            // Retorna la respuesta con el token y el recurso del usuario
            return response()->json([
                'token'   => $authData['token'],
                'user'    => new UserResource($authData['user']),
                'message' => $authData['message'],
            ]);
        } catch (\Exception $e) {
            // Captura cualquier excepción y retorna el mensaje de error
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/authenticate",
     *     summary="Get Profile user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     description="Get user",
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 description="Bearer token"
     *             ),
     *             @OA\Property(
     *             property="user",
     *             type="object",
     *             description="User",
     *             ref="#/components/schemas/User"
     *              ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Message Response"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *        @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Unauthenticated.")
     *         )
     *     ),
     * )
     */

    public function authenticate(Request $request)
    {
        // Llama al servicio de autenticación
        $result = $this->authService->authenticate();

        // Si la autenticación falla, devuelve el mensaje de error
        if (! $result['status']) {
            return response()->json(['error' => $result['message']], 422);
        }
        $token = $request->bearerToken();

        // Si la autenticación es exitosa, devuelve el token, el usuario y la persona
        return response()->json([
            'token'   => $token,
            'user'    => new UserResource($result['user']),
            'message' => 'Autenticado',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/validatetoken",
     *     summary="Enviar código de verificación por correo",
     *     tags={"Sale"},
     *     security={{"bearerAuth":{}}},
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="moviment_id", type="integer", example=101)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Código enviado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     )
     * )
     */

    public function validate_mail(StoreUserAppRequest $request): JsonResponse
    {

        if ($request->header('UUID') !== 'ZXCV-CVBN-VBNM') {
            return response()->json(['status' => 'unauthorized'], 401);
        }
        if (! $this->authService->validate_token($request->email, $request->token_form)) {
            return response()->json(['message' => 'Su token ha vencido, Debe generar nuevo token'], 422);
        }
        $data                  = $request->validated();
        $data['type_document'] = "DNI";
        $data['type_person']   = "USUARIO";
        $data['username']   = $request->email;
        $data['rol_id']   = "2"; //usuario
        $user                  = $this->userService->createUser($data);
        Cache::forget("email_verification_token:{$request->email}");
        return response()->json($user, 200);
    }

    public function send_token_sign_up(SendTokenAppRequest $request)
    {
        if ($request->header('UUID') !== 'ZXCV-CVBN-VBNM') {
            return response()->json(['status' => 'unauthorized'], 401);
        }
        $data            = $request->validated();
        $data['send_by'] = "email";
        $this->authService->sendToken($data);
        return "Código Enviado Exitosamente";
    }

}
