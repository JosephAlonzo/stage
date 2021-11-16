<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Sport\Activity;
use App\Form\Sport\ActivityType;

class ActivityController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    

    public function activitiesList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('activities_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.activity.plural'),
            'current' => true
        ];

        return $this->render('sport/activity/activitiesList.html.twig', ['breadcrumbs' => $breadcrumbs] );
    }

    public function activitiesListDatatables(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Activity::class);
    
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
        $otherConditions = "a.tenant = " . $user->getTenant()->getId();
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
                    case 'educator':
                        $educators = "";
                        $listEducators = $element->getEducator();
                        foreach ( $listEducators as $key => $educator) {
                            $educators .=  $educator->getUser()->getFirstName() . " " . $educator->getUser()->getLastName();
                            $educators .= (($key+1)%count($listEducators)) != 0 ?  ", " : "";
                        }
                        $responseTemp = $educators;
                    break;
                    case 'color':
                        $responseTemp = "<div class='d-flex align-items-center'> <div class='circle' style='background-color:".$element->getColor()."'></div>";
                        $responseTemp .= "<span class='pl-1'>" . $element->getColor() . "</div>";
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_activity', array('id' => $id));
                            $responseTemp = "<a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_activity', array('id' => $id));
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

    public function editActivity(Request $request, $id, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('activities_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.activity.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $activity = new Activity();            
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_activity', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.activity.article') . " " . $translator->trans('entity.activity.singular'),
                'current' => true
            ];
        }
        else
        {
            $activity = $em->getRepository(Activity::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_activity', ['id' => $activity->getId()]),
                'name' => $translator->trans('entity.activity.singular').': '.$activity->getName(),
                'current' => true
            ];
        }
        

     	$form = $this->createForm(ActivityType::class, $activity);

     	$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $activity->setTenant( $user->getTenant());

            $em->persist($activity);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_activity',  ['id' => $activity->getId()]);
        }

        return $this->render('sport/activity/editActivity.html.twig', array(
            'form' => $form->createView(),
            'activity' => $activity,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }


    public function deleteActivity(Request $request, Activity $activity, TranslatorInterface $translator)
    {
        if( count($activity->getPlannings()) > 0 ){
            $this->addFlash('warning', $translator->trans('messages.failed_data_deleted_association') . " des plannings associés");
            return $this->redirectToRoute("activities_list");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($activity);
        $em->flush();

        $this->addFlash('success', "Activitié (".$activity->getName().") supprimé avec succès");
        return $this->redirectToRoute("activities_list");
    }
}
