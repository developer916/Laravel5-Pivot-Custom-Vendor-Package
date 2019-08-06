<?php namespace Pivotal\User\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Pivotal\User\Models\Collections\UserMetaCollection;

class UserMeta extends User
{
    use SoftDeletingTrait;

    public $table = 'user_meta';

    public $timestamps = true;

    public function user()
    {
        return $this->BelongsTo('Pivotal/User/Models/User');
    }

    public function __toString()
    {
        return (string) $this->value;
    }

    public function newCollection(array $models = [])
    {
        $collection = new UserMetaCollection();
        if(is_array($models))
        {
            foreach($models as $model)
            {
                $collection->put($model->key,$model);
            }
        }
        return $collection;
    }

}