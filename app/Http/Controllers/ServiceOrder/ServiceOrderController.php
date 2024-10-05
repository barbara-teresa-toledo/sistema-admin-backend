<?php

namespace App\Http\Controllers\ServiceOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceOrder\CreateServiceOrderRequest;
use App\Models\ServiceOrder;
use App\Repositories\ServiceOrder\ServiceOrderRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceOrderController extends Controller
{
    public function __construct(private readonly ServiceOrderRepository $serviceOrderRepository)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/service-orders",
     *     summary="Listar ordens de serviço",
     *     description="Retorna uma lista de ordens de serviço paginadas",
     *     tags={"Service Orders"},
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
     *                 property="service_orders",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="service_date", type="string", format="date-time"),
     *                     @OA\Property(property="service_description", type="string"),
     *                     @OA\Property(property="client_id", type="integer"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(
     *                         property="client",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="document", type="string"),
     *                         @OA\Property(property="phone", type="string"),
     *                         @OA\Property(property="email", type="string"),
     *                         @OA\Property(property="address_id", type="integer"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *                         @OA\Property(
     *                             property="address",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="zip_code", type="string"),
     *                             @OA\Property(property="street", type="string"),
     *                             @OA\Property(property="number", type="string"),
     *                             @OA\Property(property="city", type="string"),
     *                             @OA\Property(property="state", type="string"),
     *                             @OA\Property(property="complement", type="string"),
     *                             @OA\Property(property="created_at", type="string", format="date-time"),
     *                             @OA\Property(property="updated_at", type="string", format="date-time"),
     *                             @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                         )
     *                     )
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

        $serviceOrders = $this->serviceOrderRepository->getOrderWithClient($perPage, ['*'], 'page', $page);

        return response()->json([
            'service_orders' => $serviceOrders
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/service-orders",
     *     summary="Criar ordem de serviço",
     *     description="Cria uma nova ordem de serviço",
     *     tags={"Service Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da ordem de serviço",
     *         @OA\JsonContent(
     *             required={"service_date", "service_description", "client_id"},
     *             @OA\Property(property="service_date", type="string", format="date-time"),
     *             @OA\Property(property="service_description", type="string"),
     *             @OA\Property(property="client_id", type="integer"),
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
    public function store(CreateServiceOrderRequest $request)
    {
        $serviceOrder = $this->serviceOrderRepository->create($request->all());

        return response()->json([
            'service_order' => $serviceOrder
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/service-orders/{serviceOrder}",
     *     summary="Detalhes da ordem de serviço",
     *     description="Retorna os detalhes de uma ordem de serviço",
     *     tags={"Service Orders"},
     *     @OA\Parameter(
     *         name="serviceOrder",
     *         in="path",
     *         description="ID da ordem de serviço",
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
     *             @OA\Property(property="service_order", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ordem de serviço não encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function edit(ServiceOrder $serviceOrder)
    {
        try {
            $serviceOrder = $this->serviceOrderRepository->getOrderWithClientById($serviceOrder->id);

            return response()->json([
                'service_order' => $serviceOrder
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Service Order not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/service-orders/{serviceOrder}",
     *     summary="Atualizar ordem de serviço",
     *     description="Atualiza uma ordem de serviço",
     *     tags={"Service Orders"},
     *     @OA\Parameter(
     *         name="serviceOrder",
     *         in="path",
     *         description="ID da ordem de serviço",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da ordem de serviço",
     *         @OA\JsonContent(
     *             required={"service_date", "service_description", "client_id"},
     *             @OA\Property(property="service_date", type="string", format="date-time"),
     *             @OA\Property(property="service_description", type="string"),
     *             @OA\Property(property="client_id", type="integer"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ordem de serviço atualizada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="service_order", type="object")
     *         )
     *     )
     * )
     */
    public function update(CreateServiceOrderRequest $request, ServiceOrder $serviceOrder)
    {
        $serviceOrder = $this->serviceOrderRepository->update($request->all(), $serviceOrder->id);

        return response()->json([
            'service_order' => $serviceOrder
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/service-orders/{serviceOrder}",
     *     summary="Deletar ordem de serviço",
     *     description="Deletar uma ordem de serviço",
     *     tags={"Service Orders"},
     *     @OA\Parameter(
     *         name="serviceOrder",
     *         in="path",
     *         description="ID da ordem de serviço",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ordem de serviço deletada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ordem de serviço não encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(ServiceOrder $serviceOrder)
    {
        try {
            $this->serviceOrderRepository->delete($serviceOrder->id);

            return response()->json([
                'message' => 'Service Order deleted'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Service Order not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
