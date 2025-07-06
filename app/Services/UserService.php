<?php
namespace App\Services;

use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService
{

    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function createUser(array $data): User
    {
        // Verificar si la persona existe; si no, crearla

        $attributes = [
            'names' => $data['names'] ?? null,
            'father_surname' => $data['father_surname'] ?? null,
            'mother_surname' => $data['mother_surname'] ?? null,
            'type_document' => $data['type_document'] ?? null,
            'type_person' => $data['type_person'] ?? null,
            'business_name' => $data['business_name'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
        ];

        $person = !empty($data['number_document'])
            ? Person::firstOrCreate(['number_document' => $data['number_document']], $attributes)
            : Person::create($attributes);

        $name = isset($data['type_document']) && $data['type_document'] === 'DNI'
            ? (isset($data['names']) ? $data['names'] : '') . ' ' .
            (isset($data['father_surname']) ? $data['father_surname'] : '') . ' ' .
            (isset($data['mother_surname']) ? $data['mother_surname'] : '')
            : (isset($data['business_name']) ? $data['business_name'] : '');

        // Crear y devolver el usuario, asociándolo con la persona encontrada o creada
        return User::create([
            'name' => $name,
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'person_id' => $person->id,
            'rol_id' => $data['rol_id'],
        ]);
    }

    public function updatePassword(string $email, string $newPassword): bool
    {
        $user = User::where('username', $email)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            Log::warning("Intento de actualización de contraseña fallido: usuario no encontrado o eliminado. Email: {$email}");
            return false;
        }

        $user->password = bcrypt($newPassword);
        return $user->save();
    }


    public function updateUser($User, array $data)
    {
        // Encontrar a la persona asociada al usuario
        $person = $User->person; // Relación 'person' entre Usuario y Persona

        // Verificar si la persona existe
        if ($person) {

            // Actualizar los datos de la persona con los valores proporcionados
            $filteredData = array_intersect_key($data, $person->getAttributes());
            $person->update($filteredData);

            $User->name = $person->names;
        } else {
            // Si no se encuentra la persona asociada al usuario, lanzar un error o manejarlo
            throw new \Exception('Persona no encontrada para el usuario con ID: ' . $User->id);
        }

        // Verificar si se proporciona un nuevo password y, si es así, encriptarlo
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            // Si no se proporciona password, eliminar la clave para que no se actualice
            unset($data['password']);
        }

        // Verificar si el username está siendo actualizado y no es el mismo que el actual
        if (isset($data['username']) && $data['username'] !== $User->username) {
            // Validar que el nombre de usuario sea único, ignorando el ID del usuario
            $this->validateUsername($data['username'], $User->id);
        }

        // Actualizar el usuario con los datos proporcionados
        $User->update($data);

        return $User;
    }

    protected function validateUsername($username, $userId)
    {
        $existingUser = User::where('username', $username)
            ->whereNull('deleted_at')
            ->where('id', '<>', $userId) // Ignorar el usuario actual
            ->first();

        if ($existingUser) {
            throw new \Exception('El nombre de usuario ya ha sido registrado.');
        }
    }

    public function destroyById($id)
    {
        if ($id == 1) {
            return false; // No se permite la eliminación del usuario con ID 1
        }
        $User = User::find($id);

        if (!$User) {
            return false;
        }
        return $User->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
