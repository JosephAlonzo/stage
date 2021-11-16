<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Sport\Educator;
use App\Form\Sport\EducatorType;
use App\Form\Sport\EducatorEditType;

class EducatorController extends AbstractController
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    public function educatorsList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('educators_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.educator.plural'),
            'current' => true
        ];

        return $this->render('sport/educator/educatorsList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function educatorsListDatatables(Request $request, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Educator::class);
    
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
        $otherConditions = "e.tenant = " . $user->getTenant()->getId();
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
                    case 'lastName':
                        $responseTemp = $translator->trans('entity.general.table.none');
                        if($element->getUser() != null){
                            $responseTemp = $element->getUser()->getLastName();
                        }
                    break;
                    case 'firstName':
                        $responseTemp = $translator->trans('entity.general.table.none');
                        if($element->getUser() != null){
                            $responseTemp = $element->getUser()->getFirstName();
                        }
                    break;
                    case 'phoneNumber':
                        $responseTemp = $translator->trans('entity.general.table.none');
                        if($element->getUser() != null){
                            $responseTemp = $element->getUser()->getPhoneNumber();
                        }
                    break;
                    case 'email':
                        $responseTemp = $translator->trans('entity.general.table.none');
                        if($element->getUser() != null){
                            $responseTemp = $element->getUser()->getEmail();
                        }
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_educator', array('id' => $id));
                            $responseTemp = "<a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_educator', array('id' => $id));
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


    public function editEducator(Request $request, $id, TranslatorInterface $translator, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('educators_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.educator.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $educator = new Educator();            
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_educator', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.educator.article') . " " . $translator->trans('entity.educator.singular'),
                'current' => true
            ];
            $form = $this->createForm(EducatorType::class, $educator);
        }
        else
        {
            $educator = $em->getRepository(Educator::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_educator', ['id' => $educator->getId()]),
                'name' => $translator->trans('entity.educator.singular').': '.$educator->getUser()->getFirstName(),
                'current' => true
            ];
            $form = $this->createForm(EducatorEditType::class, $educator);
        }

     	$form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $tenant = $user->getTenant();
            $educator->getUser()->setRoles(["ROLE_USER","ROLE_EDUCATOR"]);
            $educator->setTenant( $tenant );

            if( $id == "new" ){
                $passwordEncoded = $passwordEncoder->encodePassword($educator->getUser(), $educator->getUser()->getPassword());
                $educator->getUser()->setPassword($passwordEncoded);
            }
            $em->persist($educator);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_educator',  ['id' => $educator->getId()]);
        }


        return $this->render('sport/educator/editEducator.html.twig', array(
            'form' => $form->createView(),
            'educator' => $educator,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function deleteEducator(Request $request, Educator $educator, TranslatorInterface $translator)
    {
        if($educator->hasRelations()){
            $this->addFlash('warning', $translator->trans('messages.failed_data_deleted_association') . " des éléments associés");
            return $this->redirectToRoute("educators_list");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($educator);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted') );
        return $this->redirectToRoute("educators_list");
    }
}
