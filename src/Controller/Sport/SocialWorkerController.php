<?php

namespace App\Controller\Sport;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\FileUploader;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\Sport\SocialWorker;
use App\Form\Sport\SocialWorkerType;
use App\Form\Sport\SocialWorkerEditType;

class SocialWorkerController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    public function socialWorkersList(Request $request, TranslatorInterface $translator)
    {
        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => 'active',
            'href' => $this->generateUrl('social_workers_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.social_worker.plural'),
            'current' => true
        ];
        
        return $this->render('sport/social_worker/socialWorkersList.html.twig', array('breadcrumbs' => $breadcrumbs));
    }

    public function socialWorkersListDatatables(Request $request, TranslatorInterface $translator)
    {
        $package = new Package(new EmptyVersionStrategy());
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(SocialWorker::class);

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

        $columns = array_values($columns);
        foreach ($orders as $key => $order)
        {   
            $columName = $columns[$order['column']]['name'];
            $orders[$key]['name'] = $columName;
        }
    
        $user = $this->security->getUser();
        $otherConditions = "s.tenant = " . $user->getTenant()->getId();
        $params = [];
        $results = $repository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions, $params);
       
        // $columns = $oldColumns;
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
                    case 'firstName':
                        $responseTemp = $translator->trans('entity.general.table.none');
                        if($element->getUser() != null){
                            $responseTemp = $element->getUser()->getFirstName();
                        }
                    break;
                    case 'lastName':
                        $responseTemp = $translator->trans('entity.general.table.none');
                        if($element->getUser() != null){
                            $responseTemp = $element->getUser()->getLastName();
                        }
                    break;
                    case 'origin':
                        $responseTemp = $element->getOrigin();
                    break;
                    case 'address':
                        $responseTemp = $element->getAddress();
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
                    case 'city':
                        $responseTemp = $element->getCity()->getName();
                    break;
                    case 'actions':
                        if ($this->isGranted('ROLE_USER'))
                        {
                            $url = $this->generateUrl('delete_social_worker', array('id' => $id));
                            $responseTemp = "<div class='row'><a href='".$url."' data-modal='modal' data-target-modal='#deleteModal'><i class='mdi mdi-trash-can-outline'></i></a>";
                            $url = $this->generateUrl('edit_social_worker', array('id' => $id));
                            $responseTemp .= "&nbsp;<a href='".$url."'><i class='mdi mdi-pencil-box-outline'></i></a></div>";
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


    public function editSocialWorker(Request $request, $id, TranslatorInterface $translator, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        $breadcrumbs = [];

        $breadcrumbs[] = [
            'active' => '',
            'href' => $this->generateUrl('social_workers_list'),
            'name' =>  $translator->trans('entity.general.crud.read') . " " . $translator->trans('entity.social_worker.plural'),
            'current' => false
        ];

        if ($id == 'new')
        {
            $socialWorker = new SocialWorker();            
            $msgFlashSuccess = 'messages.data_created';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_social_worker', ['id' => $id]),
                'name' => $translator->trans('entity.general.crud.create') . " " . $translator->trans('entity.social_worker.article') . " " . $translator->trans('entity.social_worker.singular'),
                'current' => true
            ];
            $form = $this->createForm(SocialWorkerType::class, $socialWorker);
        }
        else
        {
            $socialWorker = $em->getRepository(SocialWorker::class)->find($id);
            $msgFlashSuccess = 'messages.data_updated';

            $breadcrumbs[] = [
                'active' => 'active',
                'href' => $this->generateUrl('edit_social_worker', ['id' => $socialWorker->getId()]),
                'name' => $translator->trans('entity.social_worker.singular'). $socialWorker->getUser()->getFirstName(),
                'current' => true
            ];
            $form = $this->createForm(SocialWorkerEditType::class, $socialWorker);
        }
     	
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $socialWorker->setTenant( $user->getTenant());

            $socialWorker->getUser()->setRoles(["ROLE_USER","ROLE_SOCIAL_WORKER"]);
            if( $id == "new" ){
                $passwordEncoded = $passwordEncoder->encodePassword($socialWorker->getUser(), $socialWorker->getUser()->getPassword());
                $socialWorker->getUser()->setPassword($passwordEncoded);
            }
            $em->persist($socialWorker);
            $em->flush();

            $this->addFlash('success', $msgFlashSuccess);

            return $this->redirectToRoute('edit_social_worker',  ['id' => $socialWorker->getId()]);
        }

        return $this->render('sport/social_worker/editSocialWorker.html.twig', array(
            'form' => $form->createView(),
            'socialWorker' => $socialWorker,
            'breadcrumbs' => $breadcrumbs,
            'id' => $id
        ));
    }

    public function deleteSocialWorker(Request $request, SocialWorker $socialWorker, TranslatorInterface $translator)
    {
        if( count($socialWorker->getOrientationSheets()) > 0 ){
            $this->addFlash('warning', $translator->trans('messages.failed_data_deleted_association') . " des fiches d'orientation associés");
            return $this->redirectToRoute("social_workers_list");
        }
        $em = $this->getDoctrine()->getManager();
        $socialWorker->setUser(NULL);
        $em->remove($socialWorker);
        $em->flush();

        $this->addFlash('success', $translator->trans('messages.data_deleted'));
        return $this->redirectToRoute("social_workers_list");
    }
}
