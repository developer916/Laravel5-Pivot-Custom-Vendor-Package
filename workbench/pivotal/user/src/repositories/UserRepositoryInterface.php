<?php namespace Pivotal\User\Repositories;

use Illuminate\Auth\UserInterface;

interface UserRepositoryInterface
{
    public function setEntity(UserInterface $model);
    public function getEntity();


}