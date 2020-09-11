<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>['guest']],function(){
    Route::get('/','Auth\LoginController@showLoginForm');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::get('/login', 'Auth\LoginController@login')->name('login');
    Route::get('/lista_empresas', 'Auth\LoginController@listaEmpresas');
});
Route::group(['middleware'=>['auth']],function(){
    
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    
    
    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');

    /*Route::group(['middleware' => ['Almacenero']], function () {
        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::put('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');

        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

        Route::get('/egreso', 'EgresoController@index');
        Route::post('/egreso/registrar', 'EgresoController@store');
        Route::put('/egreso/desactivar', 'EgresoController@desactivar');
        Route::get('/egreso/obtenerCabecera', 'EgresoController@obtenerCabecera');
        Route::get('/egreso/obtenerDetalles', 'EgresoController@obtenerDetalles');

    });

    Route::group(['middleware' => ['Vendedor']], function () {
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
 
        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');

        Route::get('/venta', 'VentaController@index');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/desactivar', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
    });*/

    Route::group(['middleware' => ['Administrador']], function () {
        
        //Route::get('/login', 'Auth\LoginController@login');

        // Route::get('/categoria', 'CategoriaController@index');
        // Route::post('/categoria/registrar', 'CategoriaController@store');
        // Route::put('/categoria/actualizar', 'CategoriaController@update');
        // Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        // Route::put('/categoria/activar', 'CategoriaController@activar');
        // Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        Route::get('/rol', 'RolController@index');
        Route::post('rol/registrar', 'RolController@store');
        Route::put('rol/actualizar', 'RolController@update');
        Route::put('/rol/desactivar', 'RolController@desactivar');
        Route::put('/rol/activar', 'RolController@activar');
        Route::get('/rol/permisos', 'RolController@listarPermisos');
        Route::get('/rol/selectRol', 'RolController@selectRol');
        
        Route::get('/modulo', 'ModuloController@index');
        Route::post('/modulo/registrar', 'ModuloController@store');
        Route::put('/modulo/cambiarHijos', 'ModuloController@cambiarHijos');
        Route::get('/modulo/selectPadre', 'ModuloController@selectPadre');
        Route::put('/modulo/actualizar', 'ModuloController@update');
        Route::put('/modulo/desactivar', 'ModuloController@desactivar');
        Route::put('/modulo/desactivarHijos', 'ModuloController@desactivarHijos');
        Route::put('/modulo/activar', 'ModuloController@activar');
        Route::put('/modulo/activarHijos', 'ModuloController@activarHijos');

        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
        
        // MODULO DE CONFIGURACIÓN DE USUARIOS
        // Usuarios
        Route::get('/users/id', 'UserController@miId');
        Route::get('/users/simple', 'UserController@listaSimple');
        Route::get('/users/simple_excluido', 'UserController@listaSimpleExcluido');
        Route::get('/user/basico_cargoscondocumentos', 'UserController@basicoListarCargosConDocumentos');
        Route::get('/user', 'UserController@index');
        Route::post('/user/registrar', 'UserController@store');
        Route::put('/user/actualizar', 'UserController@update');
        Route::patch('/users/rol/actualizar/{user}', 'UserController@updateRol');
        Route::put('/user/desactivar', 'UserController@desactivar');
        Route::put('/user/activar', 'UserController@activar');
        
        // Usuario -> Carpetas
        Route::get('/usuarios_carpetas', 'UsuarioCarpetaController@index');
        Route::post('/usuarios_carpetas/registrar', 'UsuarioCarpetaController@store');
        Route::put('/usuarios_carpetas/actualizar/{usuarioCarpeta}', 'UsuarioCarpetaController@update');
        Route::delete('/usuarios_carpetas/{usuarioCarpeta}', 'UsuarioCarpetaController@destroy');
        
        // Usuario -> Carpetas -> Documentos
        Route::get('/usuarios_documentos', 'UsuarioDocumentoController@index');
        Route::post('/usuarios_documentos/registrar', 'UsuarioDocumentoController@store');
        Route::put('/usuarios_documentos/actualizar/{usuarioDocumento}', 'UsuarioDocumentoController@update');
        Route::delete('/usuarios_documentos/{usuarioDocumento}', 'UsuarioDocumentoController@destroy');
        
        
        Route::post('/listar_permisos', 'PermisosController@listarPermisos');
        Route::get('/listar_permisos2', 'PermisosController@listarPermisos2');
        Route::get('/permisos/listarPermisosLogueado', 'PermisosController@listarPermisosLogueado');
        Route::post('/permisos', 'PermisosController@insertar');

        // MODULO DE CONFIGURACION GENERAL
        Route::get('/configgenerales', 'ConfigGeneralesController@index');
        Route::post('configgenerales/registrar', 'ConfigGeneralesController@store');
        Route::post('configgenerales/actualizar', 'ConfigGeneralesController@update');
        Route::put('/configgenerales/desactivar', 'ConfigGeneralesController@desactivar');
        Route::put('/configgenerales/activar', 'ConfigGeneralesController@activar');
        Route::get('/configgenerales/permisos', 'ConfigGeneralesController@listarPermisos');
        Route::get('/configgenerales/selectRol', 'ConfigGeneralesController@selectRol');
        
        // MODULO CONFIGURACION DE SEGMENTOS, CARPETAS Y ARCHIVOS
        Route::get('/segmentos', 'SegmentoController@index');
        Route::get('/segmentos/simple', 'SegmentoController@listaSimple');
        Route::post('/segmentos/registrar', 'SegmentoController@store');
        Route::put('/segmentos/actualizar/{segmento}', 'SegmentoController@update');
        Route::patch('/segmentos/desactivar/{segmento}', 'SegmentoController@desactivar');
        Route::patch('/segmentos/activar/{segmento}', 'SegmentoController@activar');

        Route::get('/tipos_carpetas', 'TipoCarpetaController@index');

        Route::get('/segmentos_carpetas', 'SegmentoCarpetaController@index');
        Route::get('/segmentos_carpetas/simple_agrupada', 'SegmentoCarpetaController@listaSimpleAgrupadaPorSegmento');
        Route::post('/segmentos_carpetas/registrar', 'SegmentoCarpetaController@store');
        Route::put('/segmentos_carpetas/actualizar/{carpeta}', 'SegmentoCarpetaController@update');
        Route::patch('/segmentos_carpetas/desactivar/{carpeta}', 'SegmentoCarpetaController@desactivar');
        Route::patch('/segmentos_carpetas/activar/{carpeta}', 'SegmentoCarpetaController@activar');

        Route::get('/segmentos_archivos', 'SegmentoArchivoController@index');
        Route::get('/segmentos_archivos/simple_agrupada_con_permiso', 'SegmentoArchivoController@listaSimpleConPermisoCrearAgrupadaPorCarpeta');
        Route::get('/segmentos_archivos/cargos', 'SegmentoArchivoController@cargosActivos');
        Route::post('/segmentos_archivos/registrar', 'SegmentoArchivoController@store');
        Route::put('/segmentos_archivos/actualizar/{archivo}', 'SegmentoArchivoController@update');
        Route::patch('/segmentos_archivos/desactivar/{archivo}', 'SegmentoArchivoController@desactivar');
        Route::patch('/segmentos_archivos/activar/{archivo}', 'SegmentoArchivoController@activar');
        
        Route::get('/segmentos_archivos/permisos', 'SegmentoArchivoController@indexPermisos');
        Route::post('/segmentos_archivos/permisos/registrar', 'SegmentoArchivoController@storePermisos');
        
        Route::get('/categorias_archivos', 'CategoriaArchivoController@index');
        Route::get('/categorias_archivos/simple_con_opciones_simple', 'CategoriaArchivoController@listaSimplePorSegmentoArchivoMasOpcionesListaSimple');
        Route::post('/categorias_archivos/registrar', 'CategoriaArchivoController@store');
        Route::put('/categorias_archivos/actualizar/{categoriaArchivo}', 'CategoriaArchivoController@update');
        Route::delete('/categorias_archivos/descartar/{categoriaArchivo}', 'CategoriaArchivoController@destroy');
        
        Route::get('/categorias_opciones', 'CategoriaOpcionController@indexPorCategoriaArchivo');
        Route::post('/categorias_opciones/registrar', 'CategoriaOpcionController@store');
        Route::put('/categorias_opciones/actualizar/{categoriaOpcion}', 'CategoriaOpcionController@update');
        Route::delete('/categorias_opciones/descartar/{categoriaOpcion}', 'CategoriaOpcionController@destroy');

        // MODULO CONFIGURACION DE CARGOS
        // Cargos
        Route::get('/cargos/simple', 'CargoController@listaSimple');
        Route::get('/cargos', 'CargoController@index');
        Route::post('/cargos/registrar', 'CargoController@store');
        Route::put('/cargos/actualizar/{cargo}', 'CargoController@update');
        Route::patch('/cargos/desactivar/{cargo}', 'CargoController@desactivar');
        Route::patch('/cargos/activar/{cargo}', 'CargoController@activar');
        // Cargos -> Documentos
        Route::get('/cargos_documentos', 'CargoDocumentoController@index');
        Route::post('/cargos_documentos/registrar', 'CargoDocumentoController@store');
        Route::put('/cargos_documentos/actualizar/{cargoDocumento}', 'CargoDocumentoController@update');
        Route::patch('/cargos_documentos/desactivar/{cargoDocumento}', 'CargoDocumentoController@desactivar');
        Route::patch('/cargos_documentos/activar/{cargoDocumento}', 'CargoDocumentoController@activar');
        
        // MODULO CONFIGURACION DE PROYECTOS
        Route::get('/conf_proyectos', 'ConfProyectoController@index');
        Route::get('/conf_proyectos/simple', 'ConfProyectoController@listaSimple');
        Route::post('/conf_proyectos/registrar', 'ConfProyectoController@store');
        Route::put('/conf_proyectos/actualizar/{proyecto}', 'ConfProyectoController@update');
        Route::put('/conf_proyectos/desactivar/{proyecto}', 'ConfProyectoController@desactivar');
        Route::put('/conf_proyectos/activar/{proyecto}', 'ConfProyectoController@activar');
        Route::put('/conf_proyectos/finalizar/{proyecto}', 'ConfProyectoController@finalizar');
        Route::get('/conf_proyectos/s_responsables', 'ConfProyectoController@selectResponsables');
        Route::get('/conf_proyectos/s_segmentos', 'ConfProyectoController@selectSegmentos');
        Route::get('/conf_proyectos/ms_asociados/{proyectoId}', 'ConfProyectoController@msUsuAsociados');
        Route::get('/conf_proyectos/ms_segmentos/{proyectoId}', 'ConfProyectoController@msSegAsociados');
        Route::get('/conf_proyectos/listar_asociados/{proyectoId}', 'ConfProyectoController@listarUsuAsociados');

        // MODULO CONFIGURACION PLANES DE ACCION Y ACTIVIDADES
        Route::get('/planes_accion/proyectos_registrados', 'PlanAccionController@basicoProyectosRegistrados');
        Route::get('/planes_accion', 'PlanAccionController@index');
        Route::post('/planes_accion/notificar', 'PlanAccionController@notificar');
        Route::post('/planes_accion/registrar', 'PlanAccionController@store');
        Route::put('/planes_accion/actualizar/{planAccion}', 'PlanAccionController@update');
        Route::patch('/planes_accion/desactivar/{planAccion}', 'PlanAccionController@desactivar');
        Route::patch('/planes_accion/enviar_correo', 'PlanAccionController@enviarCorreo');
        Route::patch('/planes_accion/activar/{planAccion}', 'PlanAccionController@activar');

        Route::get('/actividades/planes_accion_asociables', 'ActividadController@basicoListarPlanesAccionAsociables');
        Route::post('/actividades/notificar', 'ActividadController@notificar');
        Route::get('/actividades', 'ActividadController@index');
        Route::post('/actividades/registrar', 'ActividadController@store');
        Route::put('/actividades/actualizar/{actividad}', 'ActividadController@update');
        Route::patch('/actividades/desactivar/{actividad}', 'ActividadController@desactivar');
        Route::patch('/actividades/activar/{actividad}', 'ActividadController@activar');
        
        // MODULO GESTION INDICADORES
        Route::get('/objetivos', 'ObjetivoController@index');
        Route::post('/objetivos/registrar', 'ObjetivoController@store');
        Route::put('/objetivos/actualizar/{objetivo}', 'ObjetivoController@update');
        Route::patch('/objetivos/desactivar/{objetivo}', 'ObjetivoController@desactivar');
        Route::patch('/objetivos/activar/{objetivo}', 'ObjetivoController@activar');
        
        Route::get('/indicadores', 'IndicadorController@indexPorObjetivo');
        Route::post('/indicadores/registrar', 'IndicadorController@store');
        Route::put('/indicadores/actualizar/{indicador}', 'IndicadorController@update');
        Route::patch('/indicadores/desactivar/{indicador}', 'IndicadorController@desactivar');
        Route::patch('/indicadores/activar/{indicador}', 'IndicadorController@activar');
        
        Route::get('/indicadores_actividades/basico_planes_accion', 'IndicadorActividadController@basicoListarPlanesAccion');
        Route::get('/indicadores_actividades', 'IndicadorActividadController@index');
        Route::post('/indicadores_actividades/desatar', 'IndicadorActividadController@desatar');
        Route::post('/indicadores_actividades/atar', 'IndicadorActividadController@atar');
        
        // MODULO GESTION DE ACTIVIDADES
        Route::get('/gestion_actividades', 'ActividadGestionController@index');
        
        Route::get('/actividades_seguimientos/{actividadSeguimientoId}', 'ActividadSeguimientoController@show');
        Route::post('/actividades_seguimientos/registrar', 'ActividadSeguimientoController@store');
        Route::put('/actividades_seguimientos/actualizar/{actividadSeguimiento}', 'ActividadSeguimientoController@update');
        
        Route::get('/actividades_evidencias', 'ActividadEvidenciaController@index');
        Route::post('/actividades_evidencias/registrar', 'ActividadEvidenciaController@store');
        Route::delete('/actividades_evidencias/descartar/{actividadEvidencia}', 'ActividadEvidenciaController@destroy');
        
        // MODULO CONFIGURACION DE CAUSAS BASICAS E INMEDIATAS
        Route::get('/causas_novedades', 'CausaNovedadController@index');
        Route::post('/causas_novedades/registrar', 'CausaNovedadController@store');
        Route::put('/causas_novedades/actualizar/{causaNovedad}', 'CausaNovedadController@update');
        Route::patch('/causas_novedades/desactivar/{causaNovedad}', 'CausaNovedadController@desactivar');
        Route::patch('/causas_novedades/activar/{causaNovedad}', 'CausaNovedadController@activar');
        
        Route::get('/causas_novedades_items/buscar', 'CausaNovedadItemController@buscarCausa');
        Route::get('/causas_novedades_items', 'CausaNovedadItemController@indexPorCausaNovedad');
        Route::post('/causas_novedades_items/registrar', 'CausaNovedadItemController@store');
        Route::put('/causas_novedades_items/actualizar/{causaNovedadItem}', 'CausaNovedadItemController@update');
        Route::delete('/causas_novedades_items/descartar/{causaNovedadItem}', 'CausaNovedadItemController@destroy');

        // MODULO DE CONFIGURACION DE RAIC
        // Raics
        Route::get('/raics/logid', 'RaicController@usuarioId');
        Route::get('/raics/usuarios_filtro_sel', 'RaicController@usuariosFiltro');
        Route::get('/raics/usuarios', 'RaicController@usuarios');
        Route::get('/raics/pi_obtener', 'RaicController@implicadosObtener');
        Route::get('/raics/fuentes_rep', 'RaicController@fuentesRep');
        Route::get('/raics/tipos_desviaciones', 'RaicController@tiposDesviaciones');
        Route::get('/raics/cbi', 'RaicController@cbi');
        Route::get('/raics/cbi/marcados', 'RaicController@cbiMarcados');
        Route::get('/raics/hay_contingencias/{raic}', 'RaicController@contingenciasExistentes');
        Route::get('/raics', 'RaicController@index');
        Route::post('/raics/registrar', 'RaicController@store');
        Route::put('/raics/actualizar/{raic}', 'RaicController@update');
        Route::patch('/raics/cerrar/{raic}', 'RaicController@cerrar');
        Route::patch('/raics/abrir/{raic}', 'RaicController@abrir');
        Route::patch('/raics/desactivar/{raic}', 'RaicController@desactivar');
        Route::patch('/raics/activar/{raic}', 'RaicController@activar');

        // Raics -> Contingencias
        Route::get('/raics_contingencias/usuarios', 'RaicContingenciaController@usuariosContingencia');
        Route::get('/raics_contingencias', 'RaicContingenciaController@indexPorRaic');
        Route::post('/raics_contingencias/registrar', 'RaicContingenciaController@store');
        Route::put('/raics_contingencias/actualizar/{raicContingencia}', 'RaicContingenciaController@update');
        Route::patch('/raics_contingencias/cerrar/{raicContingencia}', 'RaicContingenciaController@cerrar');
        Route::patch('/raics_contingencias/abrir/{raicContingencia}', 'RaicContingenciaController@abrir');
        Route::patch('/raics_contingencias/desactivar/{raicContingencia}', 'RaicContingenciaController@desactivar');
        Route::patch('/raics_contingencias/activar/{raicContingencia}', 'RaicContingenciaController@activar');
        
        // Raics -> Contingencias -> Evidencias
        Route::get('/raics_evidencias', 'RaicEvidenciaController@indexPorContingencia');
        Route::post('/raics_evidencias/registrar', 'RaicEvidenciaController@store');
        Route::put('/raics_evidencias/actualizar/{raicEvidencia}', 'RaicEvidenciaController@update');
        Route::delete('/raics_evidencias/descartar/{raicEvidencia}', 'RaicEvidenciaController@destroy');
        
        // MODULO DE CONFIGURACION DE PENDIENTES
        // Pendientes
        Route::get('/pendientes/proyectos', 'PendienteController@proyectoSelector');
        Route::get('/pendientes/valida_cierre/{pendiente}', 'PendienteController@cierreValidar');
        Route::get('/pendientes', 'PendienteController@index');
        Route::post('/pendientes/registrar', 'PendienteController@store');
        Route::put('/pendientes/actualizar/{pendiente}', 'PendienteController@update');
        Route::patch('/pendientes/cerrar/{pendiente}', 'PendienteController@cerrar');
        Route::patch('/pendientes/abrir/{pendiente}', 'PendienteController@abrir');
        Route::patch('/pendientes/desactivar/{pendiente}', 'PendienteController@desactivar');
        Route::patch('/pendientes/activar/{pendiente}', 'PendienteController@activar');
        
        // Pendientes -> Asignaciones
        Route::get('/pendiente_asignaciones/users', 'AsignacionController@userSelector');
        Route::get('/pendiente_asignaciones', 'AsignacionController@indexPorPendiente');
        Route::post('/pendiente_asignaciones/registrar', 'AsignacionController@store');
        Route::put('/pendiente_asignaciones/actualizar/{asignacion}', 'AsignacionController@update');
        Route::patch('/pendiente_asignaciones/cerrar/{asignacion}', 'AsignacionController@cerrar');
        Route::patch('/pendiente_asignaciones/abrir/{asignacion}', 'AsignacionController@abrir');
        Route::patch('/pendiente_asignaciones/desactivar/{asignacion}', 'AsignacionController@desactivar');
        Route::patch('/pendiente_asignaciones/activar/{asignacion}', 'AsignacionController@activar');

        // Pendientes -> Asignaciones -> Evidencias
        Route::get('/pendiente_evidencias', 'AsignacionEvidenciaController@indexPorAsignacion');
        Route::post('/pendiente_evidencias/registrar', 'AsignacionEvidenciaController@store');
        Route::delete('/pendiente_evidencias/descartar/{evidencia}', 'AsignacionEvidenciaController@destroy');
        
        // MODULO DE CUADRO DE MANDO
        Route::get('/cuadro_mando/actividades', 'CuadroMandoController@informeActividades');
        
        // MODULO DE CONFIGURACIÓN DE SOLICITUDES
        Route::get('/solicitudes_opciones/{solicitud}', 'SolicitudController@opcionesRegistradas');
        Route::get('/solicitudes', 'SolicitudController@indexPorSegmentoArchivo');
        Route::post('/solicitudes/notificar', 'SolicitudController@notificar');
        Route::post('/solicitudes/registrar', 'SolicitudController@store');
        Route::put('/solicitudes/actualizar/{solicitud}', 'SolicitudController@update');
        Route::patch('/solicitudes/desactivar/{solicitud}', 'SolicitudController@desactivar');
        Route::patch('/solicitudes/activar/{solicitud}', 'SolicitudController@activar');
        Route::patch('/solicitudes/finalizar/{solicitud}', 'SolicitudController@finalizar');
    });
});

//Route::get('/home', 'HomeController@index')->name('home');