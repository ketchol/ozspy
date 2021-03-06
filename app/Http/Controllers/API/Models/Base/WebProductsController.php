<?php

namespace OzSpy\Http\Controllers\API\Models\Base;

use Illuminate\Http\Response;
use OzSpy\Http\Requests\Models\WebProducts\LoadRequest;
use OzSpy\Models\Base\WebProduct;
use Illuminate\Http\Request;
use OzSpy\Http\Controllers\Controller;
use OzSpy\Services\Entities\WebProduct\DestroyService;
use OzSpy\Services\Entities\WebProduct\GetService;
use OzSpy\Services\Entities\WebProduct\LoadService;
use OzSpy\Services\Entities\WebProduct\StoreService;
use OzSpy\Services\Entities\WebProduct\UpdateService;

class WebProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request|LoadRequest $request
     * @param LoadService $loadService
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(LoadRequest $request, LoadService $loadService)
    {
        return $loadService->handle($request->all())->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param StoreService $storeService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, StoreService $storeService)
    {
        $webProduct = $storeService->handle($request->all());
        return $webProduct->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \OzSpy\Models\Base\WebProduct $webProduct
     * @param GetService $getService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(WebProduct $webProduct, GetService $getService)
    {
        $webProductResource = $getService->handle($webProduct);
        return $webProductResource->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \OzSpy\Models\Base\WebProduct $webProduct
     * @param UpdateService $updateService
     * @return Response
     */
    public function update(Request $request, WebProduct $webProduct, UpdateService $updateService)
    {
        $result = $updateService->handle($webProduct, $request->all());
        if ($result === true) {
            return new Response(null, 204);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \OzSpy\Models\Base\WebProduct $webProduct
     * @param DestroyService $destroyService
     * @return Response
     */
    public function destroy(WebProduct $webProduct, DestroyService $destroyService)
    {
        $result = $destroyService->handle($webProduct);
        if ($result === true) {
            return new Response(null, 204);
        }
    }
}
