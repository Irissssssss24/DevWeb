<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'document';
    protected $primaryKey = 'id_document';
    protected $fillable = ['type', 'fichier', 'id_stage'];
}