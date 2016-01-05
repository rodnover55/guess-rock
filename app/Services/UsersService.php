<?php
namespace App\Services;
use App\Models\Social;
use App\Models\User;

/**
 * @author Sergei Melnikov <me@rnr.name>
 */
class UsersService
{
    public function findOrCreateByFacebook($data) : User {
        $user = User::whereHas('connections', function ($query) use ($data) {
            $query->where('driver', 'facebook');
            $query->whereRaw('cast(("socials".data->>\'id\') as varchar) = ?', [$data->id]);
        })->first();

        if (!empty($user)) {
            return $user;
        }

        $user = User::whereIn('username', [$data->nickname, $data->user['email']])->first();

        if (!empty($user)) {
            $this->addConnection($user, 'vk', $data);

            return $user;
        }

        /** @var User $user */
        $user = User::create([
            'first_name' => $data->user['first_name'],
            'last_name' => $data->user['last_name'],
            'username' => $data->nickname ?? $data->user['email'],
            'avatar' => $data->avatar_original
        ]);

        $this->addConnection($user, 'facebook', $data);

        return $user;
    }

    public function findOrCreateByVk($data) : User {
        $user = User::whereHas('connections', function ($query) use ($data) {
            $query->where('driver', 'vk');
            $query->whereRaw('cast(("socials".data->>\'id\') as varchar) = ?', [$data->id]);
        })->first();

        if (!empty($user)) {
            return $user;
        }

        $user = User::whereIn('username', [$data->nickname, $data->email])->first();

        if (!empty($user)) {
            $this->addConnection($user, 'vk', $data);

            return $user;
        }

        /** @var User $user */
        $user = User::create([
            'first_name' => $data->user['first_name'],
            'last_name' => $data->user['last_name'],
            'username' => $data->nickname ?? $data->email,
            'avatar' => $data->avatar
        ]);

        $this->addConnection($user, 'vk', $data);

        return $user;
    }

    protected function addConnection(User $user, string $type, $data) {
        $user->connections()->create([
            'driver' => $type,
            'data' => json_encode($data)
        ]);
    }
}