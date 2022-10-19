<?php

declare(strict_types=1);

namespace App\Controller\AbstractController;

use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author <andy.rotsaert@live.be>
 */
abstract class AbstractDatatableController extends AbstractFormController
{
    /**
     * @param $request
     * @param $repository
     *
     * @return Response|void
     */
    protected function dataTable($request, $repository)
    {
        if ($request->getMethod() == 'POST') {
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');

            $data = $repository->getDatatable($start, $length, $orders, $search, $columns);
            $serializer = SerializerBuilder::create()->build();
            $response = [
                "draw" => $draw,
                "recordsTotal" => $data['total'],
                "recordsFiltered" => $data['filtered'],
                "data" => $data['result'],
            ];
            $jsonContent = $serializer->serialize($response, 'json');

            return new Response(
                $jsonContent,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }
    }
}
