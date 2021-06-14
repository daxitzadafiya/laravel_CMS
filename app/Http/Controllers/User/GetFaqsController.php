<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;

class GetFaqsController extends Controller
{
    public function __invoke()
    {
        $faqs = Faq::with('category')->active()->orderBy('order', 'asc')->get();

        return $this->sendResponse(['faqs' => FaqResource::collection($faqs)]);
    }
}
