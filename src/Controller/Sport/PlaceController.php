<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Sport\Place;
use App\Form\Sport\PlaceType;

class PlaceController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    public function placesList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('places_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.place.plural'),
            'current' => true
        ];

        return $this->render('sport/place/placesList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function placesListDatatables(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Place::class);
    
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
    
        $user = $this->security->getUser();
        $otherConditions = "p.tenant = " . $user->getTenant()->getId();
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
                    case 'address':
                        $responseTemp = $element->getAddress();
                    break;
                    case 'city':
                        $responseTemp = $element->getCity()->getName();
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_place', array('id' => $id));
                            $responseTemp = "<a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_place', array('id' => $id));
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


    public function editPlace(Request $request, $id, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('places_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.place.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $place = new Place();            
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_place', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.place.article') . " " . $translator->trans('entity.place.singular'),
                'current' => true
            ];
        }
        else
        {
            $place = $em->getRepository(Place::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_place', ['id' => $place->getId()]),
                'name' => $translator->trans('entity.place.singular').': '.$place->getName(),
                'current' => true
            ];
        }
        
     	$form = $this->createForm(PlaceType::class, $place);

     	$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $place->setTenant( $user->getTenant());
            $em->persist($place);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_place',  ['id' => $place->getId()]);
        }


        return $this->render('sport/place/editPlace.html.twig', array(
            'form' => $form->createView(),
            'place' => $place,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function deletePlace(Request $request, Place $place, TranslatorInterface $translator)
    {
        if( count($place->getPlannings()) > 0 ){
            $this->addFlash('warning', $translator->trans('messages.failed_data_deleted_association') . " des plannings associés");
            return $this->redirectToRoute("places_list");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($place);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted') );
        return $this->redirectToRoute("places_list");
    }
}
