<?php
declare(strict_types=1);

namespace App\Controller\Api\v1;

use App\Controller\AbstractController;
use App\Service\MessageService;
use App\Traits\GetFastAction;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Mzh\Validate\Annotations\RequestValidation;

/**
 * @package App\Controller\Api\v1
 * @AutoController()
 */
class Message extends AbstractController
{
    /**
     * @Inject()
     * @var MessageService
     */
    protected  $messageService;

    use GetFastAction;

    /**
     * 登录方法
     * @return array
     * @throws Exception
     */
    public function unread()
    {
        $data['status'] = 11;
        $result = $this->messageService->unread();
        return $this->json($result);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function user_list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->messageService->userList($data);
        return $this->json($result);
    }


    /**
     * @RequestValidation(mode="Message",value="user")
     * @return array
     * @throws Exception
     */
    public function read()
    {
        $data = $this->request->getParsedBody();
        $result = $this->messageService->read($data['message_id']);
        return $this->json($result);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function allread()
    {
        $data = $this->request->getParsedBody();
        $result = $this->messageService->read([], true);
        return $this->json($result);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function delete()
    {
        $data = $this->request->getParsedBody();
        $result = $this->messageService->delete($data['message_id']);
        return $this->json($result);
    }


    /**
     * @return array
     * @throws Exception
     */
    public function delete_all()
    {
        $data = $this->request->getParsedBody();
        $result = $this->messageService->delete([], true);
        return $this->json($result);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function user_info()
    {
        return $this->info();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function list()
    {
        $data = $this->request->getParsedBody();
        $result = $this->messageService->list($data);
        return $this->json($result);
    }


}
