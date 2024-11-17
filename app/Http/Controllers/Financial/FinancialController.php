<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\CreateFinancialRequest;
use App\Http\Requests\Financial\UpdateFinancialRequest;
use App\Models\Financial;
use App\Repositories\Financial\FinancialRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinancialController extends Controller
{
    public function __construct(private readonly FinancialRepository $financialRepository)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/financial",
     *     summary="Listar operacoes financeiras",
     *     description="Retorna uma lista de operacoes financeiras",
     *     tags={"Financial"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página a ser recuperada",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de itens por página",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="financial",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="date", type="string", format="date-time"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="value", type="integer"),
     *                     @OA\Property(property="type", type="boolean"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);

        $financials = $this->financialRepository->getFinancialOperations($perPage, ['*'], 'page', $page);

        return response()->json([
            'operations' => $financials
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/financial",
     *     summary="Criar uma operacao",
     *     description="Cria uma nova operacao financeira",
     *     tags={"Financial"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da operacao",
     *         @OA\JsonContent(
     *             required={"date", "description", "value", "type"},
     *             @OA\Property(property="date", type="string", format="date-time"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="value", type="integer"),
     *             @OA\Property(property="type", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ordem de serviço criada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="service_order", type="object")
     *         )
     *     )
     * )
     */
    public function store(CreateFinancialRequest $request)
    {
        $financialOperation = $this->financialRepository->create($request->all());

        return response()->json([
            'operation' => $financialOperation
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/financial/{financial}",
     *     summary="Detalhes da operacao",
     *     description="Retorna os detalhes da operacao ",
     *     tags={"Financial"},
     *     @OA\Parameter(
     *         name="financial",
     *         in="path",
     *         description="ID da operacao financeira",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="financial", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Operacao não encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function edit(Financial $financial)
    {
        try {
            $financialOperation = $this->financialRepository->find($financial->id);

            return response()->json([
                'operation' => $financialOperation
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Operation not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/financial/{financial}",
     *     summary="Atualizar operacao financeira",
     *     description="Atualiza uma operacao financeira",
     *     tags={"Financial"},
     *     @OA\Parameter(
     *         name="financial",
     *         in="path",
     *         description="ID da operacao financeira",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da operacao",
     *         @OA\JsonContent(
     *             required={"date", "description", "value", "type"},
     *             @OA\Property(property="date", type="string", format="date-time"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="value", type="integer"),
     *             @OA\Property(property="type", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ordem de serviço atualizada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="financial", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateFinancialRequest $request, Financial $financial)
    {
        $financialOperation = $this->financialRepository->update($request->all(), $financial->id);

        return response()->json([
            'operation' => $financialOperation
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/financial/{financial}",
     *     summary="Deletar operacao financeira",
     *     description="Deletar uma operacao financeira",
     *     tags={"Financial"},
     *     @OA\Parameter(
     *         name="financial",
     *         in="path",
     *         description="ID da operacao financeira",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operacao financeira deletada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Operacao financeira não encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(Financial $financial)
    {
        try {
            $this->financialRepository->delete($financial->id);

            return response()->json([
                'message' => 'Financial operation deleted'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Financial operation not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
