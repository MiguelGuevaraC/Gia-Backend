<?php
namespace App\Services;

use App\Mail\SendTokenMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    /**
     * Maneja el proceso de inicio de sesión.
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login_app(string $username, string $password): array
    {

        // Busca al usuario por correo
        $user = User::where('username', $username)->first();

        // Si el usuario no existe, retornamos un error genérico sin dar pistas sobre la existencia
        if (! $user) {
            return [
                'status'  => false,
                'message' => "Credenciales inválidas", // Mensaje más general
                'user'    => null,
                'token'   => null,
            ];
        }

        // Verifica si la contraseña es correcta
        if (! Hash::check($password, $user->password)) {
            return [
                'status'  => false,
                'message' => "Credenciales inválidas", // Mensaje más general
                'user'    => null,
                'token'   => null,
            ];
        }

        if ($user->rol_id == 1) {
            return [
                'status'  => false,
                'message' => "Su Tipo Usuario No puede Acceder al portal App", // Mensaje más general
                'user'    => null,
                'token'   => null,
            ];
        }


        // Autentica al usuario
        Auth::login($user);

        // Crea un token de autenticación
        $token = $user->createToken('auth_token', ['expires' => now()->addHour()])->plainTextToken;

        // Oculta el campo de contraseña antes de retornar los datos
        $user->makeHidden('password');

        // Retorna la respuesta con el token y usuario
        return [
            'status'  => true,
            'message' => 'Logueado Exitosamente',
            'token'   => $token,
            'user'    => $user,
        ];
    }

    public function login_admin(string $username, string $password): array
    {

        // Busca al usuario por correo
        $user = User::where('username', $username)->first();

        // Si el usuario no existe, retornamos un error genérico sin dar pistas sobre la existencia
        if (! $user) {
            return [
                'status'  => false,
                'message' => "Credenciales inválidas", // Mensaje más general
                'user'    => null,
                'token'   => null,
            ];
        }

        // Si el usuario no existe, retornamos un error genérico sin dar pistas sobre la existencia
        if ($user->rol_id == 2) {
            return [
                'status'  => false,
                'message' => "Su Tipo Usuario No puede Acceder al portal Administrativo", // Mensaje más general
                'user'    => null,
                'token'   => null,
            ];
        }

        // Verifica si la contraseña es correcta
        if (! Hash::check($password, $user->password)) {
            return [
                'status'  => false,
                'message' => "Credenciales inválidas", // Mensaje más general
                'user'    => null,
                'token'   => null,
            ];
        }

        // Autentica al usuario
        Auth::login($user);

        // Crea un token de autenticación
        $token = $user->createToken('auth_token', ['expires' => now()->addHour()])->plainTextToken;

        // Oculta el campo de contraseña antes de retornar los datos
        $user->makeHidden('password');

        // Retorna la respuesta con el token y usuario
        return [
            'status'  => true,
            'message' => 'Logueado Exitosamente',
            'token'   => $token,
            'user'    => $user,
        ];
    }

    public function authenticate(): array
    {

        $user   = auth()->user();
        $status = true;

        if (! $user) {
            $status = false;
            $user   = null;
        }

        // Llama al método login para realizar la autenticación
        return [
            'status' => true,
            'user'   => $user,
            'person' => $user?->person,
        ];
    }
    public function logout(): JsonResponse
    {
        try {
            // Verifica si el usuario está autenticado
            if (Auth::check()) {
                $accessToken = auth()->user()->currentAccessToken();

                // Verifica si existe un token de acceso
                if ($accessToken) {
                    // Establecer una nueva fecha de expiración (por ejemplo, 30 días a partir de ahora)
                    $accessToken->expires_at = Carbon::now();
                    $accessToken->save();
                }
            } else {
                return response()->json([
                    "message" => "El usuario no está Autenticado.",
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }
        } catch (QueryException $e) {
            // Captura la excepción de la base de datos (por ejemplo, si hay un problema al eliminar el token)
            return response()->json([
                "message" => "Ocurrio un error mientras cerraba sesión",
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            "message" => "Se cerró sesión Exitosamente",
        ]);
    }

    public function validate_token($email, $token_form)
    {
        $cachedToken = Cache::get("email_verification_token:{$email}");
        if ($cachedToken !== $token_form) {
            return false;
        }
        return true;
    }
    public function sendTokenByApi($number_phone, $token)
    {
        return "envio de codigo por telefono";
    }

    public function sendTokenByEmail($names, $email, $token)
    {
        Mail::to($email)->send(new SendTokenMail($token));
    }

    public function sendToken($data)
    {
        $token = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT); // Token de 4 dígitos

        Cache::put("email_verification_token:{$data['email']}", $token, 600);

        if ($data['send_by'] == 'api') {
            return $this->sendTokenByApi($data['phone'], $token);
        }
        if ($data['send_by'] == 'email') {
            return $this->sendTokenByEmail($data['names'], $data['email'], $token);
        }
    }
}
