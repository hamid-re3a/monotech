<?php


namespace User\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use User\Http\Requests\Admin\CreateLoginAttemptSettingRequest;
use User\Http\Requests\Admin\DeleteLoginAttemptSettingRequest;
use User\Http\Requests\Admin\UpdateLoginAttemptSettingRequest;
use User\Http\Resources\Admin\LoginAttemptResource;
use User\Repository\LoginAttemptSettingRepository;

class LoginAttemptSettingController extends Controller
{
    private $setting_repository;

    public function __construct(LoginAttemptSettingRepository $loginAttemptSettingRepository)
    {
        $this->setting_repository = $loginAttemptSettingRepository;
    }

    /**
     * List settings
     * @group Admin > Login attempt Settings
     *
     */
    public function index()
    {

        return api()->success(null, LoginAttemptResource::collection($this->setting_repository->lists()));

    }

    /**
     * Create setting
     * @group Admin > Login attempt Settings
     * @param CreateLoginAttemptSettingRequest $request
     * @return JsonResponse
     */
    public function store(CreateLoginAttemptSettingRequest $request)
    {
        try {

            $setting = $this->setting_repository->store($request->all());

            return api()->success(null, LoginAttemptResource::make($setting));

        } catch (\Throwable $exception) {
            Log::error('LoginAttemptSetting@store => ' . $exception->getMessage());
            return api()->error(null, [
                'subject' => $exception->getMessage()
            ]);
        }

    }

    /**
     * Update setting
     * @group Admin > Login attempt Settings
     * @param UpdateLoginAttemptSettingRequest $request
     * @return JsonResponse
     */
    public function update(UpdateLoginAttemptSettingRequest $request)
    {
        try {

            $setting = $this->setting_repository->update($request->get('key'), $request->all());

            return api()->success(null, LoginAttemptResource::make($setting));

        } catch (\Throwable $exception) {
            Log::error('LoginAttemptSetting@update => ' . $exception->getMessage());
            return api()->error(null, [
                'subject' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Delete setting
     * @group Admin > Login attempt Settings
     * @param DeleteLoginAttemptSettingRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteLoginAttemptSettingRequest $request)
    {
        try {
            if($this->setting_repository->lists()->count() == 1 ){
                return api()->error();
            }
            $this->setting_repository->delete($request->get('id'));

            return api()->success(null, null);

        } catch (\Throwable $exception) {
            Log::error('LoginAttemptSetting@delete => ' . $exception->getMessage());
            return api()->error(null, [
                'subject' => $exception->getMessage()
            ]);
        }
    }

}
