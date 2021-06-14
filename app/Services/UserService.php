<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserService
{
    public function getUsers($request)
    {
        $users = User::query()
            ->with('company.prefecture','userGroups')
            ->select('id', 'last_name', 'first_name', 'last_name_kana', 'first_name_kana', 'email', 'company_id', 'position', 'role', 'photo', 'created_at', 'updated_at')
            ->where('role', 'U')
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                $order = $request->input('order');

                switch ($sort) {
                    case 'company': return $query->orderByCompany($order);
                    case 'name': return $query->orderByName($order);
                    default: return $query->orderBy($sort, $order);
                }
            })
            ->when($request->input('company_id'), function ($query, $companyId) {
                return $query->where('company_id', $companyId);
            })
            ->when($request->input('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("CONCAT(last_name_kana, ' ', first_name_kana) LIKE ?", ["%{$search}%"])
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($query) use ($search) {
                            $query->where('display_name', 'like', "%{$search}%");
                        });
                });
            });

        return isPaginate($request->input('paginate'))
            ? $users->paginate($request->input('paginate', 25))
            : $users->get();
    }

    public function getAdminUsers($request)
    {
        $users = User::query()
            ->select('id', 'last_name', 'first_name', 'email', 'role', 'photo', 'created_at', 'updated_at')
            ->where('role', 'A')
            ->when($request->input('sort'), function ($query, $sort) use ($request) {
                return $query->orderBy($sort, $request->input('order'));
            });

        return isPaginate($request->input('paginate'))
            ? $users->paginate($request->input('paginate', 25))
            : $users->get();
    }

    public function addPhoto($user, $photo): void
    {
        try {
            Storage::disk('public')->delete('user/photos/'. $user->photo);

            $photoFileName = $user->id . '.' . $photo->getClientOriginalExtension();

            $image = Image::make($photo)->fit(150, 150, function ($constraint) {
                $constraint->aspectRatio();
            });

            Storage::disk('public')
                ->put('user/photos/' . $photoFileName, (string) $image->encode());

            $user->photo = $photoFileName;
            $user->save();
        } catch (\Exception $e) {

        }
    }
}
