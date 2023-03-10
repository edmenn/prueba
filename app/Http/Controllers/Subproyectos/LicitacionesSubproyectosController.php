<?php

namespace App\Http\Controllers\Subproyectos;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Licitacion;
use App\Models\Subproyecto;
use App\Models\Proveedor;

class LicitacionesSubproyectosController extends Controller
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
    public function index(Subproyecto $subproyecto)
    {
        // obtenemos todas las licitaciones del subproyecto
        $licitaciones = Licitacion::where('subproyecto_id', $subproyecto->id)->get();

        // retornamos respuesta
        return view('subproyectos.licitaciones.index', compact('subproyecto', 'licitaciones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Subproyecto $subproyecto)
    {
        $proveedores = Proveedor::all();

        return view('subproyectos.licitaciones.create', compact('subproyecto', 'proveedores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Subproyecto $subproyecto)
    {
        // validamos los datos enviados
        $rules = array(
            'proveedor_id' => 'required|integer|max:32767',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|integer|max:999999999999',
            'comentarios' => 'required|string',
        );

        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // creamos una nueva licitacion de monto
        $licitacion = new Licitacion;
        $licitacion->subproyecto_id = $subproyecto->id;
        $licitacion->proveedor_id = $request->proveedor_id;
        $licitacion->concepto = $request->concepto;
        $licitacion->monto = $request->monto;
        $licitacion->comentarios = $request->comentarios;
        $licitacion->save();

        // retornamos respuesta
        return redirect()->route('subproyectos.licitaciones.index', $subproyecto->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Subproyecto $subproyecto, $id)
    {
        // chequeamos que exista el registro de licitacion
        $licitacion = Licitacion::findOrFail($id);
        
        // retornamos respuesta
        return response()->json(['subproyecto' => $subproyecto, 'licitacion' => $licitacion]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Subproyecto $subproyecto, $id)
    {
        // chequeamos que exista el registro de licitacion
        $licitacion = Licitacion::findOrFail($id);
        $proveedores = Proveedor::all();
                
        return view('subproyectos.licitaciones.edit', compact('subproyecto', 'licitacion', 'proveedores'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subproyecto $subproyecto, $id)
    {
        // Verificamos que exista el registro de licitacion
        $licitacion = Licitacion::findOrFail($id);

        // validamos los datos enviados
        $rules = array(
            'proveedor_id' => 'required|integer|max:32767',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|integer|max:999999999999',
            'comentarios' => 'required|string',
        );

        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // modificamos el registro de licitacion
        $licitacion->proveedor_id = $request->proveedor_id;
        $licitacion->concepto = $request->concepto;
        $licitacion->monto = $request->monto;
        $licitacion->comentarios = $request->comentarios;
        $licitacion->save();

        // retornamos respuesta
        return redirect()->route('subproyectos.licitaciones.index', $subproyecto->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subproyecto $subproyecto, $id)
    {
        // Verificamos que exista el registro de licitacion
        $licitacion = Licitacion::findOrFail($id);

        // eliminamos el registro de licitacion
        $licitacion->delete();
        
        // retornamos respuesta
        return response()->json(['status' => 'success', 'message' => 'Registro de licitaci??n eliminado correctamente']);
    }

}