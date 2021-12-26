<?php


namespace User\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use User\Http\Requests\Admin\UpdateSettingRequest;
use User\Http\Resources\Admin\SettingResource;
use User\Repository\SettingRepository;

class SettingController extends Controller
{
    private $setting_repository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->setting_repository = $settingRepository;
    }
    /**
     * List settings
     * @group Admin > Settings
     *
     */
    public function index()
    {

        return api()->success(null,SettingResource::collection($this->setting_repository->lists()));

    }

    /**
     * Update setting
     * @group Admin > Settings
     * @param UpdateSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingRequest $request)
    {
        try {
            $setting = $this->setting_repository->update($request->get('key'), $request->all());

            return api()->success(null,SettingResource::make($setting));
        } catch (\Throwable $exception) {
            Log::error('SettingController@update => ' . $exception->getMessage());
            return api()->error(null,[
                'subject' => $exception->getMessage()
            ]);
        }
    }

}
