<?php

namespace App\Http\Controllers\Admin\Faq;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Http\Resources\FaqResource;
use App\Http\Resources\PaginationResource;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index(FaqRequest $request)
    {
        $faqs = Faq::with('category')->orderBy('order', 'asc');

        $faqs = isPaginate($request->input('paginate'))
            ? $faqs->paginate($request->input('paginate', 25))
            : $faqs->get();

        return $this->sendResponse(
            ['faqs' => FaqResource::collection($faqs)],
            isPaginate($request->input('paginate'))
                ? ['paginate' => new PaginationResource($faqs)]
                : []
        );
    }

    public function show(Faq $faq)
    {
        return $this->sendResponse([
            'faq' => new FaqResource($faq->load('category')),
        ]);
    }

    public function store(FaqRequest $request)
    {
        Faq::create($request->validated());

        return $this->sendResponse([
            'message' => __('Faq created successfully.'),
        ]);
    }

    public function update(Faq $faq, FaqRequest $request)
    {
        $faq->update($request->validated());

        return $this->sendResponse([
            'message' => __('Faq updated successfully.'),
        ]);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return $this->sendResponse([
            'message' => __('Faq deleted successfully.'),
        ]);
    }
}
