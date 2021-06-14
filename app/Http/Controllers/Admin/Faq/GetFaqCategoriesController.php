<?php

namespace App\Http\Controllers\Admin\Faq;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;

class GetFaqCategoriesController extends Controller
{
    public function __invoke()
    {
        return $this->sendResponse([
            'categories' => FaqCategoryResource::collection(FaqCategory::all())
        ]);
    }
}
