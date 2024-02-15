<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdministradorFormRequest;
use App\Http\Requests\UpdateAdministradorFormRequest;
use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdministradorController extends Controller
{
    public function administrador(AdministradorFormRequest $request)
    {
        try {
            $data = $request->all();

            // Verifica se o usuário já existe
            $administrador = Administrador::where('email', $request->email)->first();
            if ($administrador) {
                return response()->json([
                    'status' => false,
                    'message' => "Admin já cadastrado"
                ], 400);
            }

            $data['password'] = Hash::make($request->password);

            $response = Administrador::create($data)->createToken($request->server('HTTP_USER_AGENT'))->plainTextToken;

            return response()->json([
                'status' => 'sucess',
                'message' => "Admin cadastrado com sucesso",
                'token' => $response
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function administradorLogin(Request $request){ 
        try {
            if (Auth::guard('administradors')->attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])) {
                /** @var UserContract $user */
                $user = Auth::guard('administradors')->user();
                $token = $user->createToken($request->server('HTTP_USER_AGENT'), ['administradors'])->plainTextToken;
                return response()->json([
                    'status' => true,
                    'message' => "Login efetuado com sucesso",
                    'token' => $token
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciais incorretas'
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function verificarAdministradorLogado()
    {
        return Auth::user();
    }
    public function administradorCpf(Request $request)
    {
        $administrador = Administrador::where('cpf', 'like', '%' . $request->cpf . '%')->get();
        if (count($administrador) > 0) {
            return response()->json([
                'status' => true,
                'data' => $administrador
            ]);
        }
        return response()->Json([
            'status' => true,
            'message' => "Não há resultados para pesquisa"
        ]);
    }
    public function administradorExcluir($id)
    {
        $administrador = Administrador::find($id);
        if (!isset($administrador)) {
            return response()->json([
                'status' => false,
                'message' => 'Administrador não encontrado'
            ]);
        }
        $administrador->delete();
        return response()->json([
            'status' => true,
            'message' => 'Administrador deletado com êxito'
        ]);
    }
    public function administradorRetornar()
    {
        $administrador = Administrador::all();

        return response()->json([
            'status' => true,
            'data' => $administrador
        ]);
    }
    public function administradorRestaurar(Request $request)
    {
        $administrador = Administrador::where('email', 'like', $request->email)->first();
        if ($administrador) {
            $novaSenha = $administrador->cpf;
            $administrador->update([
                'password' => Hash::make($novaSenha),
                'updated_at' => now()
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Senha redefinida',
                'nova_senha' => Hash::make($novaSenha)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Administrador não encontrado'
            ]);
        }
    }
    public function administradorUpdate(UpdateAdministradorFormRequest $request)
    {
        $administrador = Administrador::find($request->id);
        if (!isset($administrador)) {
            return response()->json([
                'status' => false,
                'message' => "Administrador não encontrado"
            ]);
        }
        if (isset($request->nome)) {
            $administrador->nome = $request->nome;
        }
        if (isset($request->cpf)) {
            $administrador->cpf = $request->cpf;
        }
        if (isset($request->email)) {
            $administrador->email = $request->email;
        }
        if (isset($request->password)) {
            $administrador->password = $request->password;
        }
        $administrador->update();
        return response()->json([
            'status' => true,
            'message' => 'Administrador atualizado'
        ]);
    }
}
