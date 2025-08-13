<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalService extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'category_id'];
    public function services(){
        return $this->hasMany(Service::class);
    }
    public function initialservices(){
       return $this->hasMany(InitialServices::class);
    }
    public function categorie(){
        return $this->belongsTo(Category::class, 'category_id');
    }
}
