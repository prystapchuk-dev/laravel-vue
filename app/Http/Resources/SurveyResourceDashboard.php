<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResourceDashboard extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'image_url'   => $this->image ? URL::to($this->image) : null,
            'title'       => $this->title,
            'status'      => $this->status !== 'draft',
            'slug'        => $this->slug,
            'created_at'  => $this->created_at->format('Y-m-d H:m'),
            'expire_date' => $this->expire_date->format('Y-m-d'),
            'questions'   => $this->questions()->count(),
            'answers'     => $this->answers()->count()
        ];
    }
}
