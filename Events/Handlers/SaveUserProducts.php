<?php


namespace Modules\Imonitor\Events\Handlers;

use Modules\Imonitor\Events\ProductWasCreated;
use Modules\Imonitor\Repositories\ProductRepository;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Modules\Setting\Contracts\Setting;


class SaveUserProducts
{
    private $product;
    private $role;
    private $user;
    /**
     * @var Setting
     */
    private $setting;

    public function __construct(ProductRepository $product, UserRepository $user, RoleRepository $role,Setting $setting)
    {
        $this->product = $product;
        $this->user = $user;
        $this->role = $role;
        $this->setting = $setting;
    }

    /**
     * @param ProductWasCreated $event
     */
    public function handle(ProductWasCreated $event)
    {

        $entity = $event->entity;
        $title = explode(" ", $entity->title);
        $email = str_slug($entity->title, '') . '@' . $this->setting->get('imonitor::domainsEmail');
        $password =$event->data['password'];
        $firstName = $title[0];
        unset($title[0]);
        $last = implode(' ', $title);
        $lastName = empty($last) ? ' ' : $last;
        $dataUser = ["email" => $email, "password" => $password, "first_name" => $firstName, "last_name" => $lastName];
        $roleCustomer = $this->role->findByName($this->setting->get('imonitor::rolProductUser','User'));
        $user = User::where("email", $email)->first();

        if (!isset($user->email) && empty($user->email)) {
                $user = $this->user->createWithRolesFromCli($dataUser, $roleCustomer, true);
        }
        $event->data['product_user_id'] = $user->id;
        $this->product->update($event->entity, $event->data);
    }
}