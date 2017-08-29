<?php

/**
 *
 */

namespace creditocofrem\Http\Controllers;

use Illuminate\Http\Request;

use Mockery\Exception;
use creditocofrem\User;
use Yajra\Datatables\Datatables;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Models\Permission;
use DB;

class HomeController extends Controller
{

    private $rolAuxiliar;
    private $userAuxiliar;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View vista principal de la gestion de usuarios
     */
    public function index()
    {
        return view('administracion.usuarios');
    }

    /**
     * obtiene la lista de usuarios del que seran mostrados en la table
     *
     * @return Datatables -> retorna la lista de usuarios en el formato que el datatable interpreta
     */
    public function gridusuarios()
    {
        $users = User::select(['id', 'name', 'email'])->get();
        return Datatables::of($users)
            ->addColumn('action', function ($users) {
                $acciones = '<a href="' . route("usuario.editar", ["id" => $users->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                $acciones = $acciones . ' ' . '<button class="btn btn-xs btn-danger" onclick="eliminar(' . $users->id . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                return $acciones;
            })
            ->addColumn('details_url', function ($users) {
                return url(route('detalleroles', ["id" => $users->id]));
            })
            ->make(true);
    }

    /**
     * trae la vista para cargar en el modal de editar suario
     * @param Request $request , la id del usuario que se desea editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarUsuario(Request $request)
    {
        $user = User::find($request->id);
        return view('administracion.modaleditarusuario', compact('user'));
    }

    /**
     * procesa la informacion del formulario de editar usuario
     * @param Request $request , id del usuario a editar
     * @return array
     */
    public function pEditarUsuario(Request $request)
    {
        $user = User::find($request->getQueryString());
        $user->name = $request->name;
        $user->email = $request->email;
        $result = [];
        if ($user->save()) {
            $result["estado"] = true;
            $result["mensaje"] = "Informacion de usuario actualizadad satisfactoriamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible actualizar la informacion del usuario";
        }
        return $result;
    }

    /**
     * agrega un nuevo rol un usario
     * @param Request $request , la id del usuario al que se le quiere agregar el rol, y la lista de roles
     * @return array
     */
    public function usuarioAgregarRol(Request $request)
    {
        DB::beginTransaction();
        $result = [];
        try {
            $user = User::find($request->getQueryString());
            foreach ($request->roles as $rol) {
                $user->assignRole($rol);
            }
            $result["estado"] = true;
            $result["mensaje"] = "Usuario actualizado Satisfactoriamente";
            DB::commit();
            return $result;
        } catch (Exception $e) {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible actualizar el usuario " . $e->getMessage();
            DB::rollBack();
            return $result;
        }
    }

    public function gridrolesusuario(Request $request)
    {
        $roles = User::join("role_user", "role_user.user_id", "=", "users.id")
            ->join("roles", "roles.id", "=", "role_user.role_id")
            ->select(["roles.id","roles.name","roles.description"])->where("users.id", $request->id)->get();
        $this->userAuxiliar = $request->id;
        return Datatables::of($roles)
            ->addColumn('action', function ($roles) {
                $acciones = '<button class="btn btn-xs btn-danger" onclick="eliminarpermi(' . $roles->id . ', ' . $this->userAuxiliar . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                return $acciones;
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
    }

    public function eliminarRolUsuario(Request $request){
        $user = User::find($request->idUser);
        $result = [];
        if($user->revokeRole($request->idRol)){
            $result["estado"] = true;
            $result["mensaje"] = "Rol eliminado del usuario satisfatoriamente";
        }else{
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible eliminar el rol del usuario";
        }
        return $result;
    }

    /**
     * mustra la lista de roles que tiene un usuario en la grid principal
     * @param Request $request id correspondiente al usuario al que se le quiere saber sus roles
     * @return mixed
     */
    public function usuarioDetalleRolles(Request $request)
    {
        $roles = User::join("role_user", "role_user.user_id", "=", "users.id")
            ->join("roles", "roles.id", "=", "role_user.role_id")
            ->select("roles.name")->where("users.id", $request->id)->get();
        return $roles;
    }

    public function eliminarUsuario(Request $request)
    {
        $user = User::find($request->id);
        $result = [];
        if ($user->delete()) {
            $result["estado"] = true;
            $result["mensaje"] = "Usuario elminado satisfactoriamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible eliminar el usuario";
        }
        return $result;
    }

    /**
     * muestra el modal para la creacion de nuevo usuario en el sistema
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View retrona la vista parcial del formulario de registro
     */
    public function viewCrearUsuario()
    {
        return view('administracion.modalcrearusuarios');
    }

    /**
     * porcesa la informacion del formulario de creacion de usuario
     * @param Request $request , trae la informacion de registro de usuario
     * @return array retorna una respuesta positiva o negativa de la operacion
     */
    public function pCrearUsuario(Request $request)
    {
        DB::beginTransaction();
        $result = [];
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->cambiarpass = 0;
            $user->password = bcrypt("secret");
            if ($user->save()) {
                $user->syncRoles($request->roles);
                $result["estado"] = true;
                $result["mensaje"] = "Usuario agregado satisfactoriamente";
            } else {
                $result["estado"] = false;
                $result["mensaje"] = "No fue posible agregar al usuario";
            }
            DB::commit();
            return $result;
        } catch (Exception $e) {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible agregar al usuario " . $e->getMessage();
            DB::rollBack();
            return $result;
        }
    }


    /**
     * Regresa la vista de administracion de roles
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewroles()
    {
        return view('administracion.roles');
    }


    /**
     * obtiene la lista de roles que seran mostrados en la tabla
     * @return  Datatables -> retorna la lista de Roles en el formato que el datatable interpreta
     */
    public function gridroles()
    {
        $roles = Role::all();

        return Datatables::of($roles)
            ->addColumn('action', function ($roles) {
                $acciones = '<a href="' . route("editar.rol", ["id" => $roles->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                $acciones = $acciones . ' ' . '<button class="btn btn-xs btn-danger" onclick="eliminar(' . $roles->id . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                return $acciones;
            })
            ->addColumn('details_url', function ($roles) {
                return url(route('detallerpermisos', ["id" => $roles->id]));
            })
            ->make(true);
    }

    /**
     * funcion que me trae la lista de permisos que tiene un rol, en la grid general del rol
     * @param Request $request , trae la id que corresponde al rol que le quiero ver la lista de permisos que tenga
     * @return mixed, la lista de permisos que tiene un rol
     */
    public function rolespermisos(Request $request)
    {
        $permisos = DB::table('roles')
            ->join('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->select('permissions.name')
            ->where('roles.id', $request->id)->get();
        return $permisos;
    }

    /**
     * Inserta una nuevo rol en el sistema
     * @param Request $request trae toda la informacion que fue envida del formulario de creacion de rol
     * @return array retorna una respuesta positiva o negativa dependiendo del exito de la transacion
     */
    public function agrerol(Request $request)
    {
        DB::beginTransaction();
        $result = [];
        try {
            $rol = new Role();
            $rol->name = $request->nombre;
            $rol->slug = $request->slug;
            $rol->description = $request->descripcion;
            if ($rol->save()) {
                if(count($request->permisos)>0){
                    foreach ($request->permisos as $permiso){
                        $rol->syncPermissions($permiso);
                        $rol->save();
                    }
                }
                $result["estado"] = true;
                $result["mensaje"] = "Rol agregado satisfactoriamente";
            } else {
                $result["estado"] = false;
                $result["mensaje"] = "No fue posible agregar el Rol";
            }
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible agregar el Rol " . $e->getMessage();
            DB::rollBack();
        }
    }

    /**
     * elimina un rol del sistema
     * @param Request $request , trae la id del rol que se va eliminar
     * @return array, retorna si se pudo o no eliminar el rol
     */
    public function eliminarrol(Request $request)
    {
        $rol = Role::find($request->id);
        $result = [];
        if ($rol->delete()) {
            $result["estado"] = true;
            $result["mensaje"] = "Rol eliminado correctamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible eliminar el Rol";
        }
        return $result;
    }

    /**
     * muestra un modal con la informacion del rol a editar
     * @param Request $request trae la id del rol que se desea editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarRol(Request $request)
    {
        $rol = Role::find($request->id);
        return view("administracion.modaleditarrol", compact('rol'));
    }

    /**
     * actualiza la informacion basica de un rol especifico
     * @param Request $request id del rol que se quiere editar
     * @return array retorna una respuesta positiva o negativa
     */
    public function editarrolp(Request $request)
    {
        $rol = Role::find($request->getQueryString());
        $result = [];
        $rol->name = $request->name;
        $rol->slug = $request->slug;
        $rol->description = $request->description;
        if ($rol->save()) {
            $result["estado"] = true;
            $result["mensaje"] = "Rol actualizado correctamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible actualizar el Rol";
        }
        return $result;
    }

    /**
     * agrega nuevos permisos a un rol especifico
     * @param Request $request , trae la informacion de las id correspondiente a los permisos que se agregaran al permiso, asi como la id del Rol a editar
     * @return array retorna si fue exitosa la operacion
     */
    public function rolAgregarPermiso(Request $request)
    {
        DB::beginTransaction();
        $result = [];
        try {
            $rol = Role::find($request->getQueryString());
            foreach ($request->permisos as $permiso) {
                $rol->assignPermission($permiso);
            }
            $result["estado"] = true;
            $result["mensaje"] = "Rol actualizado correctamente";
            DB::commit();
            return $result;
        } catch (Exception $e) {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible actualizar el Rol " . $e->getMessage();
            DB::rollBack();
            return $result;
        }

    }

    /**
     * elimina un permiso de un rol especifico
     * @param Request $request , trae la id del rol y del permiso que se desea eliminar
     * @return array retorna una respuesta positiva o negativa de la operacion
     */
    public function eliminarPermisoRol(Request $request)
    {
        $rol = Role::find($request->idRol);
        $result = [];
        if ($rol->revokePermission($request->idPermiso)) {
            $result["estado"] = true;
            $result["mensaje"] = "Permiso eliminado del rol satisfactoriamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible elminar el permiso al Rol";
        }
        return $result;
    }

    /**
     * trae la lista de permisos que tiene un rol para ser editado
     * @param Request $request , la id del rol al cual se le quiere sacar la lista de permisos
     * @return mixed, retorna la data lista para mostrase en un datatable
     */
    public function gridpermisosrol(Request $request)
    {
        $permisos = DB::table('roles')
            ->join('permission_role', 'roles.id', '=', 'permission_role.role_id')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->select(['permissions.id', 'permissions.name', 'permissions.description'])
            ->where('roles.id', $request->id)->get();
        $this->rolAuxiliar = $request->id;
        return Datatables::of($permisos)
            ->addColumn('action', function ($permisos) {
                $acciones = '<button class="btn btn-xs btn-danger" onclick="eliminarpermi(' . $permisos->id . ', ' . $this->rolAuxiliar . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                return $acciones;
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
    }

    /**
     * Retorna la vista de administracion de Permisos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewpermisos()
    {
        return view('administracion.permisos');
    }

    /**
     * obtiene la lista de permisos que seran mostrados en la tabla
     * @return Datatables -> retorna la lista de Roles en el formato que el datatable interpreta
     */
    public function gridpermisos()
    {
        $permisos = Permission::all();

        return Datatables::of($permisos)
            ->addColumn('action', function ($permisos) {
                $acciones = '<a href="' . route("editar.permiso", ["id" => $permisos->id]) . '" data-modal="" class="btn btn-xs btn-custom"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                $acciones = $acciones . ' ' . '<button class="btn btn-xs btn-danger" onclick="eliminar(' . $permisos->id . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                return $acciones;
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
    }

    /**
     * Inserta un nuevo permiso al sistema
     * @param Request $request trae los los datos que fueron pasados por el formulario
     * @return array retorna una respuesta positiva o negativa dependiendo del exito de la transacion
     */
    public function agrepermiso(Request $request)
    {
        $permiso = new Permission();
        $result = [];
        $permiso->name = $request->nombre;
        $permiso->slug = $request->slug;
        $permiso->description = $request->descripcion;
        if ($permiso->save()) {
            $result["estado"] = true;
            $result["mensaje"] = "Permiso agregado correctamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible agregar el Permiso";
        }
        return $result;
    }

    /**
     * Elimina un permiso del sistema
     * @param Request $request trae el id del permisos que se quiere eliminar
     * @return array retorna una respuesta positiva o negativa dependiendo del exito de la transacion
     */
    public function eliminapermiso(Request $request)
    {
        $permiso = Permission::find($request->id);
        $result = [];
        if ($permiso->delete()) {
            $result["estado"] = true;
            $result["mensaje"] = "Permiso eliminado correctamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible eliminar el Permiso";
        }
        return $result;
    }

    /**
     * muestra una vista parcial para editar un permiso
     * @param Request $request trae la id del permiso que se desea editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vieweditarpermiso(Request $request)
    {
        $permiso = Permission::find($request->id);
        return view('administracion.editarpermiso', compact('permiso'));
    }

    /**
     * Edita un permiso del sistema
     * @param Request $request trae los datos del formulario editar permiso
     * @return array retorna una respuesta positiva o negativa dependiendo del exito de la transacion
     */
    public function editarpermiso(Request $request)
    {
        $permiso = Permission::find($request->getQueryString());
        $result = [];
        $permiso->name = $request->name;
        $permiso->slug = $request->slug;
        $permiso->description = $request->description;
        if ($permiso->save()) {
            $result["estado"] = true;
            $result["mensaje"] = "Permiso actualizado correctamente";
        } else {
            $result["estado"] = false;
            $result["mensaje"] = "No fue posible actualizar el Permiso";
        }
        return $result;
    }

    /**
     * muestra todos los permisos que seran pasados a un select
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function selectpermisos(Request $request)
    {
        $permisos = Permission::where('name', 'like', '%' . $request->term . '%')->get();
        return $permisos;
    }

    public function selectroles(Request $request)
    {
        $roles = Role::where('name', 'like', '%' . $request->term . '%')->get();
        return $roles;
    }


}
