<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfissionalFormRequest;
use App\Http\Requests\UpdateProfissionalFormRequest;
use App\Models\Agenda;
use App\Models\Profissional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfissionalController extends Controller
{
    public function profissional(ProfissionalFormRequest $request)
    {
        try {
            $data = $request->all();

            // Verifica se o usuário já existe
            $profissional = Profissional::where('email', $request->email)->first();
            if ($profissional) {
                return response()->json([
                    'status' => false,
                    'message' => "Profissional já cadastrado"
                ], 400);
            }

            $data['password'] = Hash::make($request->password);

            $response = Profissional::create($data)->createToken($request->server('HTTP_USER_AGENT'))->plainTextToken;

            return response()->json([
                'status' => 'sucess',
                'message' => "Profissional cadastrado com sucesso",
                'token' => $response
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function profissionalLogin(Request $request){ 
        try {
            if (Auth::guard('profissionals')->attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])) {
                /** @var UserContract $user */
                $user = Auth::guard('profissionals')->user();
                $token = $user->createToken($request->server('HTTP_USER_AGENT'), ['profissionals'])->plainTextToken;
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
    public function profissionalId($id){
        $profissional = Profissional::find($id);
        if($profissional == null){
            return response()->json([
                'status' => false,
                'message' => "Profissional não encontrado"
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $profissional
        ]);
    }
    public function profissionalNome(Request $request){
        $profissional = Profissional::where('nome', 'like', '%' . $request->nome . '%')->get();
        if(count($profissional) > 0){
            return response()->json([
                'status' => true,
                'data' => $profissional
            ]);
        }
        return response()->Json([
            'status' => true,
            'message' => "Não há resultados para pesquisa"
        ]);
    }
    public function profissionalCpf(Request $request){
        $profissional = Profissional::where('cpf', 'like', '%' . $request->cpf . '%')->get();
        if(count($profissional) > 0){
            return response()->json([
                'status' => true,
                'data' => $profissional
            ]);
        }
        return response()->Json([
            'status' => true,
            'message' => "Não há resultados para pesquisa"
        ]);
    }
    public function profissionalCelular(Request $request){
        $profissional = Profissional::where('celular', 'like', '%' . $request->celular . '%')->get();
        if(count($profissional) > 0){
            return response()->json([
                'status' => true,
                'data' => $profissional
            ]);
        }
        return response()->Json([
            'status' => true,
            'message' => "Não há resultados para pesquisa"
        ]);
    }
    public function profissionalEmail(Request $request){
        $profissional = Profissional::where('email', 'like', '%' . $request->email . '%')->get();
        if(count($profissional) > 0){
            return response()->json([
                'status' => true,
                'data' => $profissional
            ]);
        }
        return response()->Json([
            'status' => true,
            'message' => "Não há resultados para pesquisa"
        ]);
    }
    public function profissionalRetornar(){
        $profissional = Profissional::All();
        return response()->json([
            'status' => true,
            'data' => $profissional
        ]);
    }
    public function profissionalExcluir($id){
        $profissional = Profissional::find($id);
        if(!isset($profissional)){
            return response()->json([
                'status' => false,
                'message' => 'Profissional não encontrado'
            ]);
        }
        $profissional_agendamento = Agenda::where('profissional_id', $id)->get(); 
        if(count($profissional_agendamento) > 0){ 
            return response()->json([
                'status' => false, 
                'message' => 'Não foi possível excluir pois o profissional possui agendamentos registrados.'
            ]);
        }
        $profissional->delete();
        return response()->json([
            'status' => true,
            'message' => 'Profissional deletado com êxito'
        ]);
        }
        public function profissionalRestaurar(Request $request){
            $profissional = Profissional::where('email', 'like', $request->email)->first();
            if ($profissional) {
                $novaSenha = $profissional->cpf;
                $profissional->update([
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
                    'message' => 'Profissional não encontrado'
                ]);
            }
        }
        public function profissionalUpdate(UpdateProfissionalFormRequest $request){
            $profissional = Profissional::find($request->id);
            if(!isset($profissional)){
                return response()->json([
                    'status' => false,
                    'message' => "Profissional não encontrado"
                ]);
            }
            if(isset($request->nome)){
                $profissional->nome = $request->nome;
            }
            if(isset($request->celular)){
                $profissional->celular = $request->celular;
            }
            if(isset($request->email)){
                $profissional->email = $request->email;
            }
            if(isset($request->cpf)){
                $profissional->cpf = $request->cpf;
            }
            if(isset($request->dataNascimento)){
                $profissional->dataNascimento = $request->dataNascimento;
            }
            if(isset($request->cidade)){
                $profissional->cidade = $request->cidade;
            }
            if(isset($request->estado)){
                $profissional->estado = $request->estado;
            }
            if(isset($request->pais)){
                $profissional->pais = $request->pais;
            }
            if(isset($request->rua)){
                $profissional->rua = $request->rua;
            }
            if(isset($request->numero)){
                $profissional->numero = $request->numero;
            }
            if(isset($request->bairro)){
                $profissional->bairro = $request->bairro;
            }
            if(isset($request->cep)){
                $profissional->cep = $request->cep;
            }
            if(isset($request->complemento)){
                $profissional->complemento = $request->complemento;
            }
            if(isset($request->password)){
                $profissional->password = $request->password;
            }
            if(isset($request->salario)){
                $profissional->salario = $request->salario;
            }
            $profissional->update();
            return response()->json([
                'status' => true,
                'message' => 'Profissional atualizado'
            ]);
    }
}