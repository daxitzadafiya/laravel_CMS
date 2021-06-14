<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserPreferenceRequest;
use App\Models\UserPreference;
use Illuminate\Support\Arr;

class UserPreferenceController extends Controller
{
    public function index()
    {
        $userPreferences = auth()->user()->preferences;
        $allPreferences = config('reddish.user.preferences');

        $response = [];

        foreach ($allPreferences as $type => $preferences) {
            foreach ($preferences as $preference) {
                // Ignored keys for frontend setting list
                if ($preference['id'] == 'notification.browser') {
                    continue;
                }

                $preferenceValue = $userPreferences->where('name', $preference['id'])->first()->value
                    ?? ($type == 'notification' ? 0 : null);
                $response[$type][] = Arr::add($preference, 'value', $preferenceValue);
            }
        }

        return $this->sendResponse($response);
    }

    public function update(UserPreferenceRequest $request)
    {
        $data = $request->validated();

        $preferences = [];

        foreach ($data['preferences'] as $preference) {
            $preferences[] = [
                'user_id' => auth()->user()->id,
                'name' => $preference['name'],
                'value' => $preference['value'],
            ];
        }

        UserPreference::upsert($preferences, ['user_id', 'name']);

        return $this->sendResponse([
            'message' => __('User preferences updated successfully.'),
            'hide_success_message' => $request->hide_success_message ?: false
        ]);
    }
}
