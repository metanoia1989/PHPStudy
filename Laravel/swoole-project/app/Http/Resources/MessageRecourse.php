<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageRecourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'userid' => $this->user->name,
            'username' => $this->user->name,
            'src' => $this->user->avatar,
            'msg' => $this->msg,
            'img' => $this->img,
            'roomid' => $this->room_id,
            'time' => $this->created_at,
        ];
    }
}
