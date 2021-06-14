<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class AccountItemResource extends JsonResource
{
    public function toArray($request)
    {
        $type = Arr::where(config('reddish.account_item.types'), function ($value, $key) {
            return $value['id'] == $this->type;
        });

        $subtype = Arr::where(config('reddish.account_item.subtypes'), function ($value, $key) {
            return $value['id'] == $this->subtype;
        });

        $type = array_shift($type);
        $subtype = array_shift($subtype);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $type,
            'subtype' => $subtype,
        ];
    }
}
