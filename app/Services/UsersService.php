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

//        dd($user);

        if (empty($user)) {
            /** @var User $user */
            $user = User::create([
                'first_name' => $data->user['first_name'],
                'last_name' => $data->user['last_name'],
                'username' => $data->nickname ?? $data->user['email'],
                'avatar' => $data->avatar_original
            ]);

            $user->connections()->create([
                'driver' => 'facebook',
                'data' => json_encode($data)
            ]);
        }

        return $user;
    }
}