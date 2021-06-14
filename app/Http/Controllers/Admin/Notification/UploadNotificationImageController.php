<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationImageRequest;
use Illuminate\Support\Facades\Storage;

class UploadNotificationImageController extends Controller
{
    public function __invoke(NotificationImageRequest $request)
    {
        $imagePath = Storage::disk('public')
            ->putFile('notifications', $request->file('image'));

        return $this->sendResponse(['image' => url('storage/' . $imagePath)]);
    }
}
