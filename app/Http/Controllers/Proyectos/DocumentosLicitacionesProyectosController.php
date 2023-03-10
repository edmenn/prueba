<?php

namespace App\Http\Controllers\Proyectos;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Models\Proyecto;
use App\Models\Licitacion;

class DocumentosLicitacionesProyectosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // PERMISOS PARA ROLES DE ADMINISTRADOR Y JEFE DEPARTAMENTAL 
        // solamente para la funcionalidad show no hace falta permisos
        $this->middleware('verificarRol:administracion,jefe_departamental')->except(['show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Licitacion $licitacion)
    {
        // obtenemos todos los documentos del proyecto
        $documentos = Documento::where('licitacion_id', $licitacion->id)->get();

        // retornamos respuesta
        return view('proyectos.documentoslicitaciones.index', compact('licitacion', 'documentos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Licitacion $licitacion)
    {
        $tipos_documento = TipoDocumento::all();
        return view('proyectos.documentoslicitaciones.create', compact('licitacion', 'tipos_documento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Licitacion $licitacion)
    {
        $archivo = $request->file('archivo');

        // validamos los datos enviados
        $rules = array(
            'tipo_documento_id' => 'required|integer|max:32767',
            'nombre' => 'required|string|max:50',
        );

        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // verificamos que se haya cargado un archivo
        if(!$request->hasFile('archivo')){
            $validator->errors()->add('archivo', 'ERROR. DEBE CARGAR UNA IMAGEN.');
            return back()->withErrors($validator)->withInput();
        }

        // verificamos que el archivo no pese m??s que cierto tama??o (1 MB)
        $size = ($archivo->getSize() / 1024 / 1024);
        if ($size > 1) {
            $validator->errors()->add('archivo', 'ERROR DE CARGA. ARCHIVO MUY PESADO (Supera 1MB).');
            return back()->withErrors($validator)->withInput();
        }

        // cargamos el archivo
        $extension = $archivo->extension();
        $nombre_archivo = time().'-documento'.'.'.$extension;
        // Cargamos el archivo (ruta storage/app/public/proyectos/{licitacion_id}/ is un enlace simbolico desde public/proyectos/{licitacion_id}/)
        $path = $archivo->storeAs('public/proyectos/licitaciones/'.$licitacion->id, $nombre_archivo);

        // creamos un nuevo documento
        $documento = new documento;
        $documento->licitacion_id = $licitacion->id;
        $documento->tipo_documento_id = $request->tipo_documento_id;
        $documento->nombre = $request->nombre;
        $documento->archivo = $nombre_archivo;
        $documento->save();

        // retornamos respuesta
        return redirect()->route('proyectos.documentoslicitaciones.index', $licitacion->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Licitacion $licitacion, $id)
    {
        // Verificamos que exista el registro de documento
        $documento = Documento::findOrFail($id);

        // eliminamos el archivo guardado
        if (Storage::exists('public/proyectos/licitaciones/'.$licitacion->id.'/'.$documento->archivo)){
            Storage::delete('public/proyectos/licitaciones/'.$licitacion->id.'/'.$documento->archivo);
        }

        // eliminamos el registro de documento
        $documento->delete();
        
        // retornamos respuesta
        return response()->json(['status' => 'success', 'message' => 'Documento eliminado correctamente']);
    }

}