<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Core\City;
use App\Form\Sport\CityType;

class CityController extends AbstractController
{
    public function citiesList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('cities_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.city.plural'),
            'current' => true
        ];

        return $this->render('sport/city/citiesList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function citiesListDatatables(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(City::class);
    
        if ($request->getMethod() == 'POST')
        {
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');
        }
        else
            die;

    
        foreach ($orders as $key => $order)
        {
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }
    
        $otherConditions = null;
        $params = [];

        
        $results = $repository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions, $params);
    
        $objects = $results["results"];
        $total_objects_count = $repository->countElement();
        $selected_objects_count = count($objects);
        $filtered_objects_count = $results["countResult"];
    
        $response = '{
            "draw": '.$draw.',
            "recordsTotal": '.$total_objects_count.',
            "recordsFiltered": '.$filtered_objects_count.',
            "data": [';
    
        $i = 0;
    
        foreach ($objects as $key => $element)
        {
            $response .= '["';
    
            $j = 0; 
            $nbColumn = count($columns);
            foreach ($columns as $key => $column)
            {
                $id = $element->getId();

                $responseTemp = "";
                switch($column['name'])
                {
                    case 'id':
                        $responseTemp = $element->getId();
                    break;
                    case 'name':
                        $responseTemp = $element->getName();
                    break;
                    case 'postalCode':
                        $responseTemp = $element->getPostalCode();
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_city', array('id' => $id));
                            $responseTemp = "<a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_city', array('id' => $id));
                            $responseTemp .= "&nbsp;<a href='".$url."'><i class='mdi mdi-pencil-box-outline'></i></a>";
                        }
                    break;
                }
    
                // Add the found data to the json
                $response .= str_replace('"', '', $responseTemp);
    
                if(++$j !== $nbColumn)
                    $response .='","';
            }
    
            $response .= '"]';
    
            // Not on the last item
            if(++$i !== $selected_objects_count)
                $response .= ',';
        }
    
        $response .= ']}';
    
        $returnResponse = new JsonResponse();
        $returnResponse->setJson($response);
    
        return $returnResponse;
    }


    public function editCity(Request $request, $id, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('cities_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.city.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $city = new City();            
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_city', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.city.article') . " " . $translator->trans('entity.city.singular'),
                'current' => true
            ];
        }
        else
        {
            $city = $em->getRepository(City::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_city', ['id' => $city->getId()]),
                'name' => $translator->trans('entity.city.singular').': '.$city->getName(),
                'current' => true
            ];
        }
        

     	$form = $this->createForm(CityType::class, $city);

     	$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_city',  ['id' => $city->getId()]);
        }

        return $this->render('sport/city/editCity.html.twig', array(
            'form' => $form->createView(),
            'city' => $city,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function deleteCity(Request $request, City $city, TranslatorInterface $translator)
    {
        if($city->hasRelations()){
            $this->addFlash('warning', $translator->trans('messages.failed_data_deleted_association') . " des éléments associés");
            return $this->redirectToRoute("cities_list");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($city);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted'));
        return $this->redirectToRoute("cities_list");
    }
}
