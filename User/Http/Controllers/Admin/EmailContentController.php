<?php


namespace User\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use User\Http\Requests\Admin\UpdateEmailContentRequest;
use User\Http\Resources\Admin\EmailContentResource;
use User\Repository\EmailContentRepository;

class EmailContentController extends Controller
{
    private $email_content_repository;

    public function __construct(EmailContentRepository $emailContentRepository)
    {
        $this->email_content_repository = $emailContentRepository;
    }
    /**
     * List emails
     * @group Admin > Settings > Emails
     *
     */
    public function index()
    {

        return api()->success(null,EmailContentResource::collection($this->email_content_repository->lists()));

    }

    /**
     * Update email
     * @group Admin > Settings > Emails
     * @param UpdateEmailContentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEmailContentRequest $request)
    {
        try {
            $email = $this->email_content_repository->update($request->get('key'), $request->all());

            return api()->success(null,EmailContentResource::make($email));
        } catch (\Throwable $exception) {
            Log::error('EmailContentController@update => ' . $exception->getMessage());
            return api()->error(null,[
                'subject' => $exception->getMessage()
            ],406);
        }
    }

}
