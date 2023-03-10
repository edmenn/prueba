<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licitacion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'licitaciones';

    /**
     * Para obtener el vinculo con la tabla proyectos
     */
    public function proyecto(){
        return $this->belongsTo('App\Models\Proyecto', 'proyecto_id');
    }

    /**
     * Para obtener el vinculo con la tabla subproyectos
     */
    public function subproyecto(){
        return $this->belongsTo('App\Models\Subproyecto', 'subproyecto_id');
    }

    /**
     * Para obtener el vinculo con la tabla proveedores
     */
    public function proveedor(){
        return $this->belongsTo('App\Models\Proveedor', 'proveedor_id');
    }

    /**
     * Para obtener el vinculo con la tabla documentos
     */
    public function documentos(){
        return $this->hasMany('App\Models\Documento', 'licitacion_id');
    }

    /**
     * Para obtener el vinculo con la tabla adjudicaciones
     */
    public function adjudicacion(){
        return $this->hasOne('App\Models\Adjudicacion', 'licitacion_id');
    }
}
