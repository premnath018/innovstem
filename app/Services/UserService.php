<?php

namespace App\Services;

use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserService
{
    protected $userRepository;

    protected $studentRepository; 

    public function __construct(UserRepository $userRepository , StudentRepository $studentRepository)
    {
        $this->userRepository = $userRepository;
        $this->studentRepository = $studentRepository;

    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return $this->userRepository->create($data);
    }

    public function generateResetToken(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        $token = Str::random(60);
        $expiry = Carbon::now()->addHours(1);
        $this->userRepository->saveResetToken($user, $token, $expiry);

        return ['token' => $token, 'user' => $user];
    }

    public function resetPassword(string $email, string $token, string $newPassword)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || $user->reset_token !== $token || Carbon::now()->greaterThan($user->reset_token_expiry)) {
            return false;
        }

        $this->userRepository->updatePassword($user, Hash::make($newPassword));
        return true;
    }

    public function getUser(string $email){
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return null;
        }
        $data = new \stdClass();
        $data->email = $user->email;
        $data->name = $user->name;
        $data->id = $user->id;
        return $data;
    }

    public function createStudentDetails(int $userId, array $data)
    {
        return $this->studentRepository->create([
            'user_id' => $userId,
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'standard' => $data['standard'],
            'ambition' => $data['ambition'] ?? null,
            'parent_no' => $data['parent_no'] ?? null,
            'age' => $data['age'],
            'gender' => $data['gender'],
            'district' => $data['district'],
            'address' => $data['address'],
            'state' => $data['state'],
        ]);
    }

    
}
